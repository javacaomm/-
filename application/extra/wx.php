<?php
/**
 * Created by PhpStorm.
 * User: caoxu
 * Date: 2017-7-29
 * Time: 23:30
 */

return [
    'app_id' => 'wxfd163cdc4c59b130',
    'app_secret' => 'e556f72805b7fddd978e6df4107630bd',
    'login_url' => 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code',
    //微信获取accesstoken的url地址
    'access_token_url'=>"https://api.weixin.qq.com/cgi-bin/token?"."grant_type=client_credential&appid=%s&secret=%s"
];