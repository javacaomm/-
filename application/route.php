<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//return [
//    '__pattern__' => [
//        'name' => '\w+',
//    ],
//    '[hello]'     => [
//        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//        ':name' => ['index/hello', ['method' => 'post']],
//    ],
//
//];

use think\Route;

Route::get('api/:version/banner/:id', 'api/:version.Banner/getBanner');

Route::get('api/:version/theme', 'api/:version.Theme/getSimpleList');
Route::get('api/:version/theme/:id', 'api/:version.Theme/getComplexOne');


/*Route::get('api/:version/product/by_category','api/:version.Product/getProductsById');
Route::get('api/:version/product/:id','api/:version.Product/getOne');
Route::get('api/:version/product/recent','api/:version.Product/getRecent');*/
//路由分组主要是为了大批量一起改前缀方便所以使用的
//路由里面第四个数组可以定义验证的数据类型

Route::group('api/:version/product', function () {
    Route::get('/by_category', 'api/:version.Product/getProductsById');
    Route::get('/:id', 'api/:version.Product/getOne', [], ['id' => '\d+']);
    Route::get('/recent', 'api/:version.Product/getRecent');
});

Route::get('api/:version/category/all', 'api/:version.Category/getAllCategories');

Route::post('api/:version/token/user', 'api/:version.Token/getToken');

Route::post('api/:version/address', 'api/:version.Address/createOrUpdateAddress');

Route::post('api/:version/order', 'api/:version.Order/placeOrder');

Route::post('api/:version/pay/pre_order', 'api/:version.Pay/getPreOrder');

Route::post('api/:version/pay/notify', 'api/:version.Pay/receiveNotify');

//Route::rule('路由表达式','路由地址(模块名/控制器名/方法名)','请求类型','路由参数（数组）','变量规则（数组）');
//Route::rule('hello','sample/test/hello','GET',['https'=>false]);
//Route::rule('hello','sample/test/hello','GET|POST',['https'=>false]);
//Route::post('hello/:id','sample/test/hello');
//Route::post();
//Route::any();