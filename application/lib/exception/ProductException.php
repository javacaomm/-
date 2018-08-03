<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-26
 * Time: 14:53
 */

namespace app\lib\exception;


class ProductException extends BaseException
{
    public $code=404;
    public $msg='产品参数错误，请再次确认';
    public $errorCode=20000;
}