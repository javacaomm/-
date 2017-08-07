<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-5
 * Time: 17:20
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
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
        
    }
}