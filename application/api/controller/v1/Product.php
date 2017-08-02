<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-26
 * Time: 14:34
 */

namespace app\api\controller\v1;

use app\api\model\Product as ProductModel;
use app\api\validate\Count;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ProductException;

class Product
{
    public function getRecent($count=15){
        (new Count())->goCheck();
        $products=ProductModel::getMostRecent($count);
        if($products->isEmpty()){
            throw new ProductException();
        }
        /*下面的collection是助手函数，将数据数组变为数据集对象，因此可以使用对象的操作方法
         * */
        $products=$products->hidden(['summary']);

        return $products;
    }

    public function getProductsById($id){
        (new IDMustBePositiveInt())->goCheck();
        $products=ProductModel::getAllByCategoryId($id);
        if($products->isEmpty()){
            throw new ProductException();
        }
        $products=$products->hidden(['summary']);
        return $products;
    }

    public function getOne($id){
        (new IDMustBePositiveInt())->goCheck();
        $product=ProductModel::getProductDetail($id);
        if(!$product){
            throw new ProductException();
        }
        return $product;
    }
}