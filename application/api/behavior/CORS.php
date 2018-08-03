<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-26
 * Time: 21:40
 */

namespace app\api\behavior;


class CORS
{
    public function appInit(&$params){
        header('Access-Control-Allow-Origin: *');//允许所有域访问我们的API
        header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");//允许携带的键值对
        header('Access-Control-Allow-Methods: POST,GET');
        if(request()->isOptions()){
            exit();
        }
    }
}