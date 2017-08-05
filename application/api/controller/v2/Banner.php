<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-13
 * Time: 21:06
 */

namespace app\api\controller\v2;

//ctrl+alt+f格式化代码
//banner/v2是测试好接口啊
use app\api\validate\User;
use think\Exception;

class Banner
{
    public function getBanner($id)
    {
        $hello=$this->hello();
        $happy=self::happy();
        $this->decodeEx();

        return $hello.$happy;
    }

    public static function hello(){
        return 'I love you!';
    }

    public function happy(){
        return 'OYM';
    }

    public function decodeEx(){
        $arr1 = array();
        $arr1["name"] = "zhangsan";
        $arr1["age"] = 25;
        $arr1["address"] = "anhui";
        echo json_encode($arr1);
//        这是encode之后形式
//        {"name":"zhangsan","age":25,"address":"anhui"}
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