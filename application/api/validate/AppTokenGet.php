<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-23
 * Time: 8:47
 */

namespace app\api\validate;


class AppTokenGet extends BaseValidate
{
    protected $rule = [
        'ac'=>'require|isNotEmpty',
        'se'=>'require|isNotEmpty'
    ];
}