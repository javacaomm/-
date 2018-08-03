<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-28
 * Time: 10:24
 */

namespace app\api\model;


class Category extends BaseModel
{
    protected $hidden=['delete_time','update_time'];

    public function img(){
        return $this->belongsTo('Image','topic_img_id','id');
    }
}