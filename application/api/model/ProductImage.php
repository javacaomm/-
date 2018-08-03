<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-31
 * Time: 21:29
 */

namespace app\api\model;


class ProductImage extends BaseModel
{
    protected $hidden=['delete_time','product_id','img_id'];

    public function imgUrl(){
        return $this->belongsTo('Image','img_id','id');
    }
}