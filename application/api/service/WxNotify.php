<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-8
 * Time: 8:17
 */

namespace app\api\service;

use app\api\model\Product;
use app\lib\enum\PayStatusEnum;
use think\Db;
use think\Exception;
use think\Loader;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use think\Log;

Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');


//我们复写父类里面的方法，类似于我们的exception复写一样
class WxNotify extends \WxPayNotify
{
    //        <xml>
//       <return_code><![CDATA[SUCCESS]]></return_code>
//       <return_msg><![CDATA[OK]]></return_msg>
//       <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
//       <mch_id><![CDATA[10000100]]></mch_id>
//       <device_info><![CDATA[1000]]></device_info>
//       <nonce_str><![CDATA[TN55wO9Pba5yENl8]]></nonce_str>
//       <sign><![CDATA[BDF0099C15FF7BC6B1585FBB110AB635]]></sign>
//       <result_code><![CDATA[SUCCESS]]></result_code>
//       <openid><![CDATA[oUpF8uN95-Ptaags6E_roPHg7AG0]]></openid>
//       <is_subscribe><![CDATA[Y]]></is_subscribe>
//       <trade_type><![CDATA[MICROPAY]]></trade_type>
//       <bank_type><![CDATA[CCB_DEBIT]]></bank_type>
//       <total_fee>1</total_fee>
//       <fee_type><![CDATA[CNY]]></fee_type>
//       <transaction_id><![CDATA[1008450740201411110005820873]]></transaction_id>
//       <out_trade_no><![CDATA[1415757673]]></out_trade_no>
//       <attach><![CDATA[订单额外描述]]></attach>
//       <time_end><![CDATA[20141111170043]]></time_end>
//       <trade_state><![CDATA[SUCCESS]]></trade_state>
//</xml>

//这个函数返回的true和false主要是为了让微信服务器是否继续发送值，跟业务流程无关
    public function NotifyProcess($data, &$msg)
    {
        if ($data['result_code'] == 'SUCCESS') {
            //通过微信返回结果找到该订单信息
            $orderNo = $data['out_trade_no'];
            //下面三个Db代码防止我们删库存操作未执行完成微信服务器就再次发送请求过来了，可能会删除两遍库存（在高并发的情况可能发生）
            Db::startTrans();
            //寻找异常
            try {
                //找到该订单号下面的订单信息
                $order = OrderModel::where('order_no', '=', $orderNo)->lock(true)->find();
                //检查库存量
                if ($order->status == 1) {
                    $service = new OrderService();
                    //以下数据是一个数组
                    $stockStatus = $service->checkOrderStock($order['id']);
                    //如果库存量足够
                    if ($stockStatus['pass']) {
                        //更新表中的状态
                        $this->updateOrderStatus($order->id, true);
                        //减库存量
                        $this->reduceStock($stockStatus);
                    } //如果库存量不足
                    else {
                        $this->updateOrderStatus($order->id, false);
                    }
                }
                Db::commit();
                return true;
            } catch (Exception $ex) {
                Db::rollback();
                Log::error($ex);
                return false;
            }
        } else {
            return true;
        }
    }

    private function updateOrderStatus($orderID, $success)
    {
        $status = $success ? PayStatusEnum::PAID : PayStatusEnum::PAID_BUT_OUT_OF;
        OrderModel::where('id', '=', $orderID)->update(['status' => $status]);
    }

    private function reduceStock($stockStatus)
    {
        foreach ($stockStatus['pStatusArray'] as $singlePstatus) {
            Product::where('id', '=', $singlePstatus['id'])->setDec('stock', $singlePstatus['count']);
        }
    }
}