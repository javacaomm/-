<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-8-3
 * Time: 8:21
 */

namespace app\api\service;


use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use think\Db;
use think\Exception;

class Order
{
    //API接口通过提交的信息查询商品的库存量

    //商品订单列表信息，也就是客户端提交过来的商品信息
    protected $oProducts;

    //数据库里面的真实商品信息（包含库存量）
    protected $products;
    protected $uid;

    //以下方法是用来对比订单信息和数据库商品信息的
    public  function place($oProducts, $uid)
    {
        $this->oProducts = $oProducts;
        $this->products = $this->getProductsByOrder($oProducts);
        $this->uid = $uid;

        $status = $this->getOrderStatus();
        if (!$status['pass']) {
            $status['order_id'] = -1;
            return $status;
        } else {
            //开始创建订单
            $orderSnap = $this->snapOrder($status);
            $order=$this->createOrder($orderSnap);
            $order['pass']=true;
            return $order;
        }

    }

    //生成订单数据，把订单写入数据库中
//    $orderNo=Self::makeOrderNo()这里self应用静态类，$this->function引用非静态类的实例名称
//数据库的操作 最好还是加一个异常处理 try catch
    private function createOrder($snap)
    {
        //加入三个Db事务代码防止插入操作只执行一部分
        Db::startTrans();
        try {
            $orderNo = $this->makeOrderNo();
            $order = new \app\api\model\Order();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus']);

            $order->save();

            //orderNo多用于跟订单号交互，而自己服务器端还是用订单主键比较好
            $orderID = $order->id;
            $create_time = $order->create_time;
            //以下代码修改了oProducts
            foreach ($this->oProducts as &$p) {
                $p['order_id'] = $orderID;
            }
            //调用模型把值保存到数据库里面去
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);
            Db::commit();
            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'create_time' => $create_time
            ];
        } catch (Exception $ex) {
            Db::rollback();
            throw $ex;
        }
    }

    //通过最后的为微妙数和随机数减少高并发重复订单号的生成概率
    //以下方法一毫秒几千几万订单可能会出现重复订单号，但是中小型电商足够使用
    //位数也比较短
    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }

    //保存订单快照，用于数据库保存历史订单信息，加快历史订单查询速度
    private function snapOrder($status)
    {
        $snap = [
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatus' => [],
            'snapName' => '',
            'snapAddress' => null,
            'snapImg' => ''
        ];

        $snap['orderPrice'] = $status['orderPrice'];
        $snap['pStatus'] = $status['pStatusArray'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['snapName'] = $this->products[0]['name'];
        $snap['snapImg'] = $this->products[0]['main_img_url'];
        //数组无法插入数据库，因此需要序列化
        //如果想存储对象的话，最好不要选择关系型数据库，用文档类数据库，比如mangoDB，这里这样足够了，但是无搜索功能
        //如果以后包含对历史订单相关信息进行搜索，就需要再单独弄一个MangoDB数据库
        $snap['snapAddress'] = json_encode($this->getUserAddress());

        //如果商品类型超过一个(数组元素数量超过一个)，就加一个“等”字
        if (count($this->products) > 1) {
            $snap['snapName'] .= '等';
        }
        return $snap;
    }

    private function getUserAddress()
    {
        $userAddress = UserAddress::where('user_id', '=', $this->uid)->find();
        if (!$userAddress) {
            throw new UserException([
                'msg' => '用户收货地址不存在，下单失败',
                'errorCode' => 60001
            ]);
        }
        //模型查出来的是对象，因此把它转化为数组
        return $userAddress->toArray();
    }

    //支付订单再次检查库存量
    public function checkOrderStock($orderID){
        $oProducts=OrderProduct::where('order_id','=',$orderID)->select();
        $this->oProducts=$oProducts;
        $this->products=$this->getProductsByOrder($oProducts);
        $orderStatus=$this->getOrderStatus();
        return $orderStatus;
    }

    private function getOrderStatus()
    {
        //需要根据对应的每件商品的对比状态汇总成为订单状态
        //由于已经把两个商品信息写入成员变量，因此函数不需要传参数
        $orderStatus = [
            'pass' => true,
            'orderPrice' => 0,
            'totalCount' => 0,
            //以下的数组用来保存所有订单的详细信息
            'pStatusArray' => []
        ];

        foreach ($this->oProducts as $oProduct) {
            //对比最关键的stock信息并返回该商品对应订单的关键信息
            //一定要好好利用$oProduct这个单个元素
            $pStatus = $this->getProductStatus(
                $oProduct['product_id'], $oProduct['count'], $this->products
            );
            if (!$pStatus['haveStock']) {
                $orderStatus['pass'] = false;
            }
            $orderStatus['orderPrice'] += $pStatus['totalPrice'];
            $orderStatus['totalCount'] += $pStatus['count'];
            array_push($orderStatus['pStatusArray'], $pStatus);
        }
        return $orderStatus;
    }

    //实现每一个订单商品信息对比并返回关键信息,包含每一个商品缺货状态、单一商品总价等
    //oPid是用户订单里面某个商品的id号码
    private function getProductStatus($oPid, $oCount, $products)
    {
        $pIndex = -1;

        $pStatus = [
            'id' => null,
            'haveStock' => false,
            'count' => 0,
            'name' => '',
            'totalPrice' => 0
        ];
        for ($i = 0; $i < count($products); $i++) {
            if ($oPid == $products[$i]['id']) {
                $pIndex = $i;
            }
        }

        if ($pIndex == -1) {
            throw new OrderException([
                'msg' => 'id为' . $oPid . '的商品不存在，创建订单失败'
            ]);
        } else {
            $product = $products[$pIndex];
            $pStatus['id'] = $product['id'];
            $pStatus['count'] = $oCount;
            $pStatus['name'] = $product['name'];
            $pStatus['totalPrice'] = $product['price'] * $oCount;
            if ($product['stock'] - $oCount >= 0) {
                $pStatus['haveStock'] = true;
            }
        }

        return $pStatus;
    }

    //通过订单信息获取对应的数据库内的商品信息
    public function getProductsByOrder($oProducts)
    {
        //遍历从数据库里面查询信息,这种方法会多次访问数据库，最终导致数据库崩溃
//        foreach ($oProducts as $oProduct){
//            //查询数据库
//        }
        $oPros = [];
        foreach ($oProducts as $item) {
            array_push($oPros, $item['product_id']);
        }
//把获取的数据集转换为数组
        $products = Product::all($oPros)->visible(['id', 'name', 'price', 'stock', 'main_img_url'])->toArray();
        return $products;
    }
}