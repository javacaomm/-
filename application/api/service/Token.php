<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-30
 * Time: 21:25
 */

namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\ScopeException;
use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;
use app\api\service\Token as TokenService;

class Token
{
    public static function generateToken(){
        //32个字符组成一组随机字符串
        $randChars=getRandChar(32);
        //用三组这样的字符串进行md5加密
        $timestamp=$_SERVER['REQUEST_TIME_FLOAT'];
        //salt 盐
        $salt=config('secure.token_salt');

        return md5($randChars.$timestamp.$salt);
    }

    public static function getCurrentTokenVar($key){
        $token=Request::instance()->header('token');
        $vars=Cache::get($token);
        if(!$vars){
            throw new TokenException();
        }else{
            if(!is_array($vars)){
                $vars=json_decode($vars,true);
            }
            if(array_key_exists($key,$vars)){
                return $vars[$key];
            }else{
                throw new Exception('尝试获取得Token变量不存在');
            }
        }

    }

    public static function getCurrentUid(){
        //token
        $uid=self::getCurrentTokenVar('uid');
        return $uid;
    }

    //用户和CMS管理员都可以访问接口，只需要scope>=16即可
    public static function needPrimaryScope(){
        $scope = self::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope >= ScopeEnum::User) {
//            $this->createOrUpdateAddress();直接返回正确即可
                return true;
            } else {
                throw new ScopeException();
            }
        } else {
            throw new TokenException();
        }
    }

    //只有用户可以访问的接口，例如防止管理员给用户下单，需要scope=16即可
    public static function needExclusiveScope(){
        $scope = TokenService::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope == ScopeEnum::User) {
//            $this->createOrUpdateAddress();直接返回正确即可
                return true;
            } else {
                throw new ScopeException();
            }
        } else {
            throw new TokenException();
        }
    }

    public static function isValidOperate($checkedUID){
        if(!$checkedUID){
            throw new Exception('检查UID时必须传入一个被检测的UID');
        }
        $currentOperateUID=self::getCurrentUid();
        if($currentOperateUID==$checkedUID){
           return true;
        }
        return false;
    }

    public static function verifyToken($token){
//        微信过来查看token是否存在，就是看是否失效了
        $exist=Cache::get($token);
        if($exist){
            return true;
        }else{
            return false;
        }
    }
}