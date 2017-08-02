<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-31
 * Time: 22:58
 */

namespace app\api\validate;


class AddressNew extends BaseValidate
{
    protected $rule=[
        'name'=>'require|isNotEmpty',
        'mobile'=>'require|isMobile',
        'province'=>'require|isNotEmpty',
        'city'=>'require|isNotEmpty',
        'country'=>'require|isNotEmpty',
        'detail'=>'require|isNotEmpty'
    ];
}