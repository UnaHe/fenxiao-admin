<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/18
 * Time: 15:51
 */
namespace App\Services;

use App\Helpers\SmsHelper;
use Illuminate\Support\Facades\Cache;

class CaptchaService
{
    /**
     * 发送注册验证码
     * @param $mobile
     */
    public function registerSms($mobile){
        return $this->sendCodeSms($mobile, 'SMS_105510071');
    }

    /**
     * 修改密码验证码
     * @param $mobile
     * @return bool|string
     */
    public function modifyPasswordSms($mobile){
        return $this->sendCodeSms($mobile, 'SMS_105895041');
    }

    /**
     * 发送验证码
     * @param $mobile
     * @param $templateCode
     * @return bool|string
     */
    public function sendCodeSms($mobile, $templateCode){
        $code = mt_rand(1000, 9999);
        $codeId = md5(__METHOD__.uniqid().time());
        $cacheKey = "smsCode.".$codeId;

        if((new SmsHelper())->sms($mobile, config('sms.signname'), $templateCode, ['code'=>$code])){
            Cache::put($cacheKey, $code, config('sms.code_expire_time'));
            return $codeId;
        }

        return false;
    }

    /**
     * 验证注册验证码
     * @param $codeId
     * @param $code
     * @return bool
     */
    public function checkSmsCode($codeId, $code){
        $cacheKey = "smsCode.".$codeId;

        if(Cache::get($cacheKey) == $code){
            return true;
        }

        return false;
    }

}
