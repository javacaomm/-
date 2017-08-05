<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-5
 * Time: 17:20
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;


class Pay extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'getPreOrder'],
    ];

    public function getPreOrder($id=''){
        (new IDMustBePositiveInt())->goCheck();

    }
}