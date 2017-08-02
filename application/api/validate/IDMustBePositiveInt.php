<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-16
 * Time: 9:47
 */

namespace app\api\validate;


use think\Validate;

class IDMustBePositiveInt extends BaseValidate
{
    protected $rule=[
        'id'=>'require|isRealNumber'
    ];

    protected $message=[
        'id'=>'id必须是正整数'
    ];
}