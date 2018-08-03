<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-23
 * Time: 8:50
 */

namespace app\api\service;

//apptoken和usertoken都是子类，一起使用父类的方法
use app\api\model\ThirdApp;
use app\lib\exception\TokenException;

class AppToken extends Token
{
    public function get($ac, $se)
    {
        $app = ThirdApp::check($ac, $se);
        if (!$app) {
            throw new TokenException([
                'msg' => '授权失败',
                'errorCode' => 10004
            ]);
        } else {
            $scope = $app->scope;
            //这里本来是不应用uid代表管理员标识，但是为了统一用户uid，方便序列化，所以才这样写
            $uid = $app->id;
            $values = [
                'scope' => $scope,
                'uid' => $uid
            ];
            $token = $this->saveToCache($values);
            return $token;
        }
    }

    private function saveToCache($values){
        $token=self::generateToken();
        $expire_in = config('setting.token_expire_in');
        $result = cache($token,json_encode($values),$expire_in);
        if(!$result){
            throw new TokenException([
                'msg'=>'服务器缓存异常',
                'errorCode'=>10005
            ]);
        }
        return $token;
    }
}