<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-22
 * Time: 20:18
 */

namespace app\api\validate;


class IDCollection extends BaseValidate
{
    protected $rule = [
        'ids' => 'require|checkIDs'
    ];

    protected $message = [
        'ids' => 'ids参数必须是以逗号隔开的多个正整数'
    ];

    protected function checkIDs($value)
    {
        $values = explode(',', $value);
        if (empty($values)) {
            return false;
        }
        foreach ($values as $id) {
            if (!$this->isRealNumber($id)) {
                return false;
            }
            return true;
        }
    }
}
