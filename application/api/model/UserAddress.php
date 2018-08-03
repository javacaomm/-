<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-1
 * Time: 21:06
 */

namespace app\api\model;


class UserAddress extends BaseModel
{
    protected $hidden=[
        'id','delete_time','user_id'
    ];
}