<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-1
 * Time: 13:52
 */

namespace app\lib\exception;


class UserException extends BaseException
{
    public $code=404;
    public $msg='用户不存在';
    public $errorCode=60000;
}