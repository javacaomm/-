<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-2
 * Time: 8:20
 */

namespace app\lib\exception;


class ScopeException extends BaseException
{
    public $code=403;
    public $msg='您当前账户权限不够';
    public $errorCode=10001;
}