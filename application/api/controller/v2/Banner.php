<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-13
 * Time: 21:06
 */

namespace app\api\controller\v2;

//ctrl+alt+f格式化代码
use app\api\validate\User;
use think\Exception;

class Banner
{
    public function getBanner($id)
    {
        return 'This is v2 version';
    }
}

//其他方法学习笔记
//        $validate `= new Validate([
//            'name' => 'require|max:10',
//            'email' => 'email'
//        ]);

////        独立验证：对于很多各数据来说复用性很差
////        验证器
//        $data = ['id '=> $id];
//        $validate=new IDMustBePositiveInt();
////        $validate=new TestValidate();
//        $result=$validate->batch()->check($data);
//        if($result){
//
//        }else{
//
//        }