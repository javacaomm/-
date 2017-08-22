<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-2
 * Time: 10:48
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\Order as OrderModel;
use app\api\service\Token;
use app\api\service\Token as TokenService;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\validate\PagingParameter;
use app\api\service\Order as OrderService;
use app\lib\exception\OrderException;
use think\Controller;

class Order extends BaseController
{
    //用户在选择好商品之后，向API接口提交所选商品的信息
    //API接口通过提交的信息查询商品的库存量
    //如果库存量足够则存入用户的订单信息=下单成功了，返回支付消息，告诉用户可以支付了，如果库存不足则抛出异常
    //确认订单消息返回给客户端并调用微信支付接口
    //通过接口再次检查库存量，防止在订单支付等待时间内货物被别人买走
    //如果库存量仍然够，则调用支付并完成支付
    //小程序根据服务器返回的结果拉起微信支付
    //微信返回支付结果（因为微信是异步处理，不能实时返回）
    //完成支付之后再次检查库存量，防止微信的bug
    //成功：进行库存量的删减

    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder'],
        //查看订单消息应该是用户和管理员都可以看到的
        'checkPrimaryScope'=>['only'=>'getDetail,getSummaryByUser']
    ];

    //分页查询和获取历史订单消息
    public function getSummaryByUser($page=1,$size=15){
        (new PagingParameter())->goCheck();
        $uid=Token::getCurrentUid();
        $pagingOrders=OrderModel::getSummaryByUser($uid,$page,$size);
        //对象判空的话要用->isEmpty()
        if($pagingOrders->isEmpty())
        {
            return[
                'data'=>[],
                'current_page'=>$pagingOrders->getCurrentPage()
            ];
        }
        //这里也可以使用hidden方法单独隐藏字段
        $data=$pagingOrders->hidden(['snap_items','snap_address','prepay_id'])->toArray();
        return[
            'data'=>$data,
            'current_page'=>$pagingOrders->getCurrentPage()
        ];
    }

    public function getDetail($id){
        (new IDMustBePositiveInt())->goCheck();
        $orderDetail=OrderModel::get($id);
        if(!$orderDetail){
            throw new OrderException();
        }
        return $orderDetail->hidden(['prepay_id']);
    }

    public function placeOrder(){
        (new OrderPlace())->goCheck();

        //获取products数组,所以必须加/a
        $products=input('post.products/a');
        $uid=TokenService::getCurrentUid();
//        如果不是静态方法，这里必须使用实例化才能使用模型的方法
//        $orderOk=OrderModel::place($products,$uid);
        $order=new OrderService();
        $status=$order->place($products,$uid);
        return $status;
    }
}