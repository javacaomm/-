<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-6
 * Time: 11:02
 */

namespace app\lib\enum;


class PayStatusEnum
{
    //未支付
    const UNPAID=1;

    //已支付
    const PAID=2;

    //已发货
    const DELIVERED=3;

    //已支付，但库存不足
    const PAID_BUT_OUT_OF=4;
}