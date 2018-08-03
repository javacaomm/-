<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-14
 * Time: 8:44
 */

namespace app\api\validate;


use think\Validate;

class TestValidate extends Validate
{
protected $rule=[
    'name' => 'require|max:10',
    'email' => 'email'
];
}