<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-2
 * Time: 15:09
 */

namespace app\api\controller;

use app\api\service\Token as TokenService;
use think\Controller;


class BaseController extends Controller
{
    public function checkPrimaryScope()
    {
        TokenService::needPrimaryScope();
    }

    public function checkExclusiveScope()
    {
        TokenService::needExclusiveScope();
    }
    
}

//这一部分是重构权限控制前置方法