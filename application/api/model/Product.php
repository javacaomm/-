<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-22
 * Time: 18:09
 */

namespace app\api\model;


class Product extends BaseModel
{
    protected $hidden=['delete_time','main_img_id','pivot','from','category_id','create_time','update_time'];

    public function getMainImgUrlAttr($value,$data){
        return $this->prefixImgUrl($value,$data);
    }

    public function imgs(){
        return $this->hasMany('ProductImage','product_id','id');
    }

    public function properties(){
        return $this->hasMany('ProductProperty','product_id','id');
    }

    public static function getMostRecent($count){
        $products=self::limit($count)->order('create_time desc')->select();
        return $products;
    }

    public static function getAllByCategoryId($categoryID){
        $products=self::where('category_id','=',$categoryID)->select();
        return $products;
    }

    //重要知识，如果是对模型关联的模型查询的结果进行排序，则需要使用以下方法

    public static function getProductDetail($id){
        $product=self::with([
            'imgs'=>function($query){
                $query->with(['imgUrl'])->order('order','asc');
            }
        ])->with(['properties'])->find($id);
        return $product;
    }
}