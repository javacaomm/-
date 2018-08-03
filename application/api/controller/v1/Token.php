<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-29
 * Time: 22:02
 */

namespace app\api\controller\v1;


use app\api\service\AppToken;
use app\api\service\UserToken;
use app\api\validate\AppTokenGet;
use app\api\validate\TokenGet;
use app\lib\exception\ParameterException;
use app\api\service\Token as TokenService;

class Token
{
    public function getToken($code=''){
        (new TokenGet())->goCheck();
        $ut=new UserToken($code);
        $token=$ut->get();
        return [
            'token'=>$token
        ];
    }

    /*
     * 第三方应用获取令牌
     * @url /app_token？
     * @POST ac=:ac se=:secret
     * 这里提供的scope权限是面对整个应用的权限，至于对每一个管理员的子权限我们自己再研发
     * */
    public function getAppToken($ac='',$se='')
    {
        (new AppTokenGet())->goCheck();
        $app = new AppToken();
        $token = $app->get($ac,$se);
        return[
          'token'=>$token
        ];
    }
    
    public function verifyToken($token=''){
        if(!$token){
            throw new ParameterException([
                'Token值不能为空'
            ]);
        }
        $valid=TokenService::verifyToken($token);
        return([
           'isValid'=>$valid
        ]);
    }
}