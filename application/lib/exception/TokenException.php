<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-30
 * Time: 22:06
 */

namespace app\lib\exception;


class TokenException extends BaseException
{
    public $code=401;
    public $msg='Token已过期或无效Token';
    public $errorCode=10001;
}