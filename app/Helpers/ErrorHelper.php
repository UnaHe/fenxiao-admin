<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/23
 * Time: 16:50
 */
namespace App\Helpers;


class ErrorHelper
{
    const ERROR_TAOBAO_INVALID_SESSION = 3001;
    const ERROR_TAOBAO_INVALID_PID = 3002;
    const ERROR_TAOBAO_INVALID_GOODS = 3003;

    const ERROR_MSG = [
        self::ERROR_TAOBAO_INVALID_SESSION => '淘宝授权过期，请重新登录授权',
        self::ERROR_TAOBAO_INVALID_PID => 'PID错误，请重新输入',
        self::ERROR_TAOBAO_INVALID_GOODS => '宝贝已下架或非淘客宝贝',
    ];

    /**
     * 查询错误信息
     * @param $code
     */
    public static function getMessage($code){
        return isset(self::ERROR_MSG[$code]) ? self::ERROR_MSG[$code] : null;
    }
}