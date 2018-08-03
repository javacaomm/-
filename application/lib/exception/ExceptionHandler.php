<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-18
 * Time: 8:21
 */

namespace app\lib\exception;


use think\Exception;
use think\exception\Handle;
use think\Log;
use think\Request;

class ExceptionHandler extends Handle
{
    private $code;
    private $msg;
    private $errorCode;
    //需要返回客户端当前请求的URL路径
    public function render(\Exception $e)
    {
        if($e instanceof BaseException){
            //如果是自定义的异常
            $this->code=$e->code;
            $this->msg=$e->msg;
            $this->errorCode=$e->errorCode;
        }else{
//            Config::get('app_debug')这个助手函数也可以读取到config下面的内容
            if(config('app_debug')){
                //在子类里面重新返回父类信息，就可以重新使用tp5自定义的render方法了
                return parent::render($e);
            }else{
                $this->code=500;
                $this->msg='这是服务器的内部错误，不想告诉你';
                $this->errorCode=999;
                $this->recordErrorLog($e);
            }
        }
        $request=Request::instance();

        $result=[
            'msg'=>$this->msg,
            'error_code'=>$this->errorCode,
            'request_url'=>$request->url()
        ];
        //这里上传的就是private里面的code
        return json($result,$this->code);

    }
    public function recordErrorLog(\Exception $e){
        Log::init([
            'type'=>'File',
            'path'=>LOG_PATH,
            'level'=>['error']
        ]);
        Log::record($e->getMessage(),'error');
    }
}