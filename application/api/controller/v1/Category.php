<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-28
 * Time: 10:23
 */

namespace app\api\controller\v1;
use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;

class Category
{
    public function getAllCategories(){
        $categories=CategoryModel::all([],'img');
        if($categories->isEmpty()){
            throw new CategoryException();
        }
        return $categories;
    }

    
}