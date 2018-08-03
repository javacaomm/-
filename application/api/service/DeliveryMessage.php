<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-23
 * Time: 20:54
 */

namespace app\api\service;


use app\api\model\User;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;

class DeliveryMessage extends WxMessage
{
    const DELIVERY_MSG_ID='KMj758ZnZHVEJOFCzoaH7GZqbm19nXVacTf5_8HqLdk';

    public function sendDeliveryMessage($order,$tplJumpPage=''){
        if(!$order){
            throw new OrderException();
        }
        $this->tplID = self::DELIVERY_MSG_ID;
        $this->formid = $order->prepay_id;
        $this->page = $tplJumpPage;
        $this->prepareMassageData($order);
        $this->emphasisKeyWord = 'keyword2.DATA';
        return parent::sendMessage($this->getUserOpenID($order->user_id));
    }

    private function prepareMassageData($order){
        $dt = new \DateTime();
        $data = [
            'keyword1'=>[
                'value'=>$order->order_no
            ],
            'keyword2'=>[
                'value'=>$dt->format("Y-m-d H:i")
            ],
            'keyword3'=>[
                'value'=>'顺风快递'
            ],
            'keyword4'=>[
                'value'=>1234567890,
                'color'=>'#27408B'
            ],
            'keyword5'=>[
                'value'=>$order->snap_address,
                'color'=>'#27408B'
            ],
            'keyword6'=>[
                'value'=>$order->snap_name,
                'color'=>'#27408B'
            ],
            'keyword7'=>[
                'value'=>4001123123,
                'color'=>'#27408B'
            ]
        ];
        //保存到成员变量中
        $this->data=$data;
    }

    //cms
    private function getUserOpenID($uid)
    {
        $user = User::get($uid);
        if (!$user) {
            throw new UserException();
        }
        return $user->openid;
    }
}