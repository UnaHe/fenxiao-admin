<?php
/**
 * 淘宝账号信息
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/26
 * Time: 15:00
 */

return [
    /**
     * 淘宝开发平台appkey
     */
    'appkey' => env('TAOBAO_OPEN_APPKEY'),
    /**
     * 淘宝开发平台secretkey
     */
    'secretkey' => env('TAOBAO_OPEN_SECRETKEY'),

    //阿里妈妈cookie地址
    'alimama_cookie_url' => env('ALIMAMA_COOKIE_URL'),
    //阿里妈妈member_id
    'alimama_member_id' => env('ALIMAMA_MEMBER_ID'),
    //阿里妈妈需要创建和同步的广告位id
    'alimama_site_id' => env('ALIMAMA_SITE_ID'),
];