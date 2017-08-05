<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-4
 * Time: 10:30
 */

namespace app\api\model;


class Order extends BaseModel
{
    protected $hidden=[
        'delete_time','update_time','user_id'
    ];
    protected $autoWriteTimestamp=true;
}