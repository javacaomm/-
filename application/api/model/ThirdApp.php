<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-23
 * Time: 9:03
 */

namespace app\api\model;



class ThirdApp extends BaseModel
{
    //!!!真实项目中账号密码不要用明文，至少要用md5加密
    public static function check($ac,$se){
        $app=self::where('app_id','=',$ac)->where('app_secret','=',$se)->find();
        return $app;
    }
}