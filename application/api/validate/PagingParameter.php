<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-8
 * Time: 22:26
 */

namespace app\api\validate;


class PagingParameter extends BaseValidate
{
    protected $rule=[
        'page'=>'isRealNumber',
        'size'=>'isRealNumber'
    ];

    protected $message=[
        'page'=>'分页参数必须是正整数',
        'size'=>'分页参数必须是正整数'
    ];
}