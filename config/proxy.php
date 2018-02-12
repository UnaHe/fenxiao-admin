<?php
/**
 * 代理ip配置
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/17
 * Time: 10:40
 */

return [
    /**
     * 固定代理ip
     */
    'proxy_ip' => env('PROXY_IP'),

    /**
     * 动态代理ip获取地址
     */
    'proxy_ip_url' => env('PROXY_IP_URL'),
];