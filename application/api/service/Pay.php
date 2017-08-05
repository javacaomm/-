<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-5
 * Time: 17:28
 */

namespace app\api\service;


use think\Exception;

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

    //需要再次检查库存量
    public function pay(){
        //订单号可能根本不存在
        //订单号确实存在，但是订单号和当前用户不匹配
        //订单有可能已经被支付
        $orderService=new Order();
        $status=$orderService->checkOrderStock($this->orderID);
        return $status;
    }
}