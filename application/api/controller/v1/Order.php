<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-2
 * Time: 10:48
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\OrderPlace;
use think\Controller;
use app\api\service\Token as TokenService;
use app\api\service\Order as OrderModel;

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
    ];
    
    public function placeOrder(){
        (new OrderPlace())->goCheck();

        //获取products数组,所以必须加/a
        $products=input('post.products/a');
        $uid=TokenService::getCurrentUid();
//        如果不是静态方法，这里必须使用实例化才能使用模型的方法
//        $orderOk=OrderModel::place($products,$uid);
        $order=new OrderModel();
        $status=$order->place($products,$uid);
        return $status;
    }
}