<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-2
 * Time: 16:03
 */

namespace app\api\validate;


use app\lib\exception\ParameterException;

class OrderPlace extends BaseValidate
{
    protected $rule = [
        'products' => 'checkProducts'
    ];

    //一个验证器里面只有一个rule，因此下面还有一个的话就需要自己引用
    protected $singleRule = [
        'product_id' => 'require|isRealNumber',
        'count' => 'require|isRealNumber'
    ];

    //验证用户提交的数据是数组且不为空
    protected function checkProducts($values)
    {
        if (!is_array($values)) {
            throw new ParameterException([
                'msg' => '参数不正确'
            ]);
        }

        if (empty($values)) {
            throw new ParameterException([
                'msg' => '商品列表不能为空'
            ]);
        }

        foreach ($values as $value) {
            $this->checkProduct($value);
        }

        return true;
    }

    //由于想引用我们之前定义好的验证正整数的方法，因此使用以下方法重新实例化一个对象，再次使用基类方法
    //以下是在模型验证器里面使用了基础验证器的用法
    protected function checkProduct($value)
    {
        $validate = new BaseValidate($this->singleRule);
        $result = $validate->check($value);
        if (!$result) {
            throw new ParameterException([
                'msg' => '商品列表参数错误'
            ]);
        }
    }
}