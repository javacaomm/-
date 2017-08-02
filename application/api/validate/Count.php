<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-26
 * Time: 14:48
 */

namespace app\api\validate;


class Count extends BaseValidate
{
    protected $rule=[
        'count'=>'isRealNumber|between:1,15'
    ];
}