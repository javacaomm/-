<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-5
 * Time: 17:20
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\WxNotify;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;


class Pay extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'getPreOrder'],
    ];

    public function getPreOrder($id=''){
        (new IDMustBePositiveInt())->goCheck();

        $pay=new PayService($id);
        return $pay->pay();
    }

    //以下接口用于微信服务器来调用
    public function receiveNotify(){
        //通知频率为15/15/30/180/1800/1800/1800/1800/3600，不确保一定可以返回信息

        //1.检查库存，防止超卖
        //2.更新订单的status的状态
        //3.减库存量
        //如果成功处理，我们返回微信成功处理的消息，否则我们需要返回没有成功处理。

        //特点：post xml格式 路由地址不能用问号携带参数，微信会过滤问号后的参数
        $notify=new WxNotify();
        //接下来不要直接调用我们写的类，因为我们无法获取微信传过来的参数，所以这里需要使用微信SDK的handle函数
        $notify->Handle();
    }
}