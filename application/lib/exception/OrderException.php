<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-3
 * Time: 11:10
 */

namespace app\lib\exception;


class OrderException extends BaseException
{
    public $code=404;
    public $msg='订单错误';
    public $errorCode=80000;
}