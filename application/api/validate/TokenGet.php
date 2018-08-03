<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-29
 * Time: 22:22
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule=[
      'code'=>'require|isNotEmpty'
    ];

    protected $message=[
        'code'=>'没有code还想获取token，做梦哦'
    ];
}