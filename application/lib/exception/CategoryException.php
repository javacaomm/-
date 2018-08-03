<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-28
 * Time: 10:38
 */

namespace app\lib\exception;


class CategoryException extends BaseException
{
    public $code=404;
    public $msg='产品分类参数错误，请再次确认';
    public $errorCode=50000;
}