<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-18
 * Time: 8:28
 */

namespace app\lib\exception;


class BannerMissException extends BaseException
{
    public $code=404;
    public $msg='Requested Banner is not Excited';
    public $errorCode=40000;
}