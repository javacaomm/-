<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-5
 * Time: 17:28
 */

namespace app\api\service;


use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\lib\enum\PayStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Exception;
use think\Loader;
use think\Log;

// extend/WxPAy/WxPay.Api.php 类库导入方法
Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');

class Pay
{
    private $orderID;
    private $orderNO;

    function __construct($orderID)
    {
        if(!$orderID){
            throw new Exception('订单号不允许为NULL');
        }
        $this->orderID=$orderID;
    }

    public function pay(){
        //订单号可能根本不存在
        //订单号确实存在，但是订单号和当前用户不匹配
        //订单有可能已经被支付
        //检测库存量
        //以上方法先后顺序推荐为：前面放出现可能性最大的检测、占用资源少的检测，后面放调用资源（类、函数）最多的函数，节省服务器和数据库资源
        $this->checkOrderValid();
        $orderService=new OrderService();
        $status=$orderService->checkOrderStock($this->orderID);
        if(!$status['pass']){
            return $status;
        }
        return $this->makeWxPreOrder($status['orderPrice']);
    }

    private function makeWxPreOrder($totalPrice){
        //openid
        $openid=Token::getCurrentTokenVar('openid');
        if(!$openid){
            throw new TokenException();
        }
        $wxOrderData=new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNO);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice*100);
        $wxOrderData->SetBody('零食商贩');
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url(config('secure.pay_back_url'));
        return  $this->getPaySignatrue($wxOrderData);
    }

    private function getPaySignatrue($wxOrderData){
        $wxOrder=\WxPayApi::unifiedOrder($wxOrderData);
        //由于没有真的商用mcd_id,因此这里写假数据，不用上面从微信服务器获取的值了，等有了商户id值以后删除假数据
        $wxOrder=[
            'appid'=>'aaaaaaaaaa',
            'mch_id'=>'bbbbbbbbbbbb',
            'nonce_str'=>'w6z07V2wlwiFcQWv',
            'prepay_id'=>$this->getFakePrePayID(),
            'result_code'=>'SUCCESS',
            'return_code'=>'SUCCESS',
            'return_msg'=>'OK',
            'sign'=>$this->getFakeSign(),
            'trade_type'=>'JSAPI'
        ];
        if($wxOrder['return_code']!='SUCCESS'||$wxOrder['result_code']!='SUCCESS'){
            Log::record($wxOrder,'error');
            Log::record('获取预支付订单失败','error');
        }
        //prepay_id用于向用户推送模板消息
        $this->recordPreOrder($wxOrder);

        $signature=$this->sign($wxOrder);
        return $signature;
    }

    //封装签名，appid只在服务器和微信服务器保存，不明文传递以保护签名算法的安全
    private function sign($wxOrder){
        $jsApiPayData=new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());

        $rand=md5(time().mt_rand(0,1000));
        $jsApiPayData->SetNonceStr($rand);

        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');

        $sign=$jsApiPayData->MakeSign();
        $rawValues=$jsApiPayData->GetValues();
        $rawValues['paySign']=$sign;

        //appid没必要给客户端
        unset($rawValues['appId']);
        return $rawValues;
    }

    private function recordPreOrder($wxOrder){
        OrderModel::where('id','=',$this->orderID)->update(['prepay_id'=>$wxOrder['prepay_id']]);
    }

    //为假的prepayid赋值，正式使用删除
    private function getFakePrePayID(){
        $randomChars=getRandChar(36);
        return $randomChars;
    }

    //为假sign赋值，正式使用删除
    private function getFakeSign(){
        $randomChars=getRandChar(34);
        return $randomChars;
    }


    private function checkOrderValid(){
        $order=OrderModel::where('id','=',$this->orderID)->find();
        if(!$order){
            throw new OrderException();
        }
        if(!Token::isValidOperate($order->user_id)){
            throw new TokenException([
                'msg'=>'订单与用户不匹配',
                'errorCode'=>10003
            ]);
        }
        if($order->status!=PayStatusEnum::UNPAID){
            throw new OrderException([
                'msg'=>'订单已被支付',
                'errorCode'=>80003,
                'code'=>400
            ]);
        }
        $this->orderNO=$order->order_no;
        return true;
    }
}