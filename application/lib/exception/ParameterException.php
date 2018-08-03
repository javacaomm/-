<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-18
 * Time: 23:07
 */

namespace app\lib\exception;




class ParameterException extends BaseException
{
    public $code=400;
    public $msg='参数错误';
    public $errorCode=10000;
}