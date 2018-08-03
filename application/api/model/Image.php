<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-21
 * Time: 8:38
 */

namespace app\api\model;


use think\Model;

class Image extends BaseModel
{
    protected $hidden=['id','from','delete_time','update_time'];
    public function getUrlAttr($value,$data){
        return $this->prefixImgUrl($value,$data);
    }
    //
}