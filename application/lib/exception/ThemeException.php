<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-23
 * Time: 11:06
 */

namespace app\lib\exception;


class ThemeException extends BaseException
{
    public $code=404;
    public $message='检索主体不存在，请仔细检查主体Id';
    public $errorCode=30000;
}