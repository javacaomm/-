<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-31
 * Time: 21:30
 */

namespace app\api\model;


class ProductProperty extends BaseModel
{
    protected $hidden=['delete_time','update_time','product_id','id'];
}