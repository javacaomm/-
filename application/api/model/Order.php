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

    //读取器会自动把数据包含字段的信息转为驼峰法然后在模型里面找到相应的读取器以转换格式
    public function getSnapItemsAttr($value){
        if(empty($value)){
            return null;
        }
        return json_decode($value);
    }

    public function getSnapAddressAttr($value){
        if(empty($value)){
            return null;
        }
        return json_decode($value);
    }

    public static function getSummaryByUser($uid,$page=1,$size=15){
        //下面查询到的是Paginator的对象
        $pagingData=self::where('user_id','=',$uid)->order('create_time desc')->paginate($size,true,['page'=>$page]);
        return $pagingData;
    }
}