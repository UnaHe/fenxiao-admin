<?php
/**
 * 短信发送相关配置
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/26
 * Time: 14:55
 */

return [
    /**
     * 阿里短信appid
     */
    'app_id' => env('SMS_ACCESSKEYID'),
    /**
     * 阿里短信app secret
     */
    'app_secret' => env('SMS_ACCESSKEYSECRET'),
    /**
     * 短信签名
     */
    'signname' => env('SMS_SIGNNAME'),
    /**
     * 验证码过期时间，分钟
     */
    'code_expire_time' => env('SMS_CODE_EXPIRE_TIME', 5),
];