<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-31
 * Time: 22:46
 */

namespace app\api\controller\v1;


use app\api\model\User as UserModel;
use app\api\service\Token as TokenService;
use app\api\validate\AddressNew;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ScopeException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\TokenException;
use app\lib\exception\UserException;
use think\Cache;
use think\Controller;

class Address extends Controller
{
    protected $beforeActionList = [
        'checkPrimaryScope' => ['only' => 'createOrUpdateAddress'],
    ];

    public function checkPrimaryScope()
    {
        $scope = TokenService::getCurrentTokenVar('scope');
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

//通过以上实例，使用前置方法首先验证scope是否大于16，如果是则调用后续函数，如果不是则抛出异常,20170801

    public function createOrUpdateAddress()
    {
        $validate = new AddressNew();
        $validate->goCheck();
//        (new AddressNew())->goCheck();由于这样做获取不到六个数据，所以把这里修改为上面的形式

        //根据Token获取uid
        //根据uid在缓存内来查找用户数据，判断用户是否存在，如果不存在则抛出异常
        //获取用户从客户端提交来的地址信息
        //根据用户地址信息是否存在，从而判断是添加地址还是更新地址
        $uid = TokenService::getCurrentUid();

        $user = UserModel::get($uid);
        if (!$user) {
            throw new UserException();
        }

        //以下方法可以获取所有的post里面的参数
        $dataArray = $validate->getDataByRule(input('post.'));

        $userAddress = $user->address;
        if (!$userAddress) {
            //使用关联模型来子模型中新增数据
            $user->address()->save($dataArray);
        } else {
            //直接更新字段即可
            $user->address->save($dataArray);
        }
//        return $user;因为用户只需要知道新增地址成功或失败了即可
//        return 'success';也可以
        //下面这样写可以手动修改postman调用接口返回的status码与我们抛出异常的code一致
        return json(new SuccessMessage(), 201);

    }
}