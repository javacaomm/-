<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-17
 * Time: 8:16
 */

namespace app\api\model;


use think\Db;
use think\Exception;
use think\Model;

class Banner extends BaseModel
{
    protected $hidden=['delete_time','update_time'];
    public function items(){
        return $this->hasMany('BannerItem','banner_id','id');
    }
//    protected $table='category'; tp5默认查找的是和类名相同的表，所以如果要查其他表就用这个方法
    public static function getBannerByID($id){
        $banner=self::with(['items','items.img'])->find($id);
        return $banner;

    }
}

//        $result=Db::query('select * from banner_item WHERE banner_id=?',[$id]);
//        return $result;
//        where('字段名','表达式','查询条件')

//        $result=Db::table('banner_item')->where('banner_id','=',$id)->select();
//        return $result;这两行是自己写的getBannerByID的代码

//        find update delete insert