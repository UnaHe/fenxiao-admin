<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/18
 * Time: 16:26
 */
namespace App\Helpers;

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;

/**
 * 短信发送
 * Class SmsHelper
 * @package App\Helpers
 */
class SmsHelper
{
    private $accessKeyId;
    private $accessKeySecret;
    //短信API产品名（短信产品名固定，无需修改）
    private $product = "Dysmsapi";
    //短信API产品域名（接口地址固定，无需修改）
    private $domain = "dysmsapi.aliyuncs.com";
    //暂时不支持多Region（目前仅支持cn-hangzhou请勿修改）
    private $region = "cn-hangzhou";

    public function __construct(){
        require_once app_path('Librarys/Sms/api_sdk/vendor/autoload.php');
        Config::load();
        $this->accessKeyId = config('sms.app_id');
        $this->accessKeySecret = config('sms.app_secret');
        if(!$this->accessKeyId || !$this->accessKeySecret){
            throw new \Exception('短信配置错误');
        }
    }

    /**
     * 发送短信
     * @param $mobile 手机号码
     * @param $signName 短信签名
     * @param $templateCode 短信模板
     * @param $templateParam 模板变量
     * @return bool
     */
    public function sms($mobile, $signName, $templateCode, $templateParam){
        //初始化访问的acsCleint
        $profile = DefaultProfile::getProfile($this->region, $this->accessKeyId, $this->accessKeySecret);
        DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $this->product, $this->domain);
        $acsClient= new DefaultAcsClient($profile);
        $request = new SendSmsRequest;
        //必填-短信接收号码。支持以逗号分隔的形式进行批量调用，批量上限为1000个手机号码,批量调用相对于单条调用及时性稍有延迟,验证码类型的短信推荐使用单条调用的方式
        $request->setPhoneNumbers($mobile);
        //必填-短信签名
        $request->setSignName($signName);
        //必填-短信模板Code
        $request->setTemplateCode($templateCode);
        //选填-假如模板中存在变量需要替换则为必填(JSON格式),友情提示:如果JSON中需要带换行符,请参照标准的JSON协议对换行符的要求,比如短信内容中包含\r\n的情况在JSON中需要表示成\\r\\n,否则会导致JSON在服务端解析失败
        $request->setTemplateParam(json_encode($templateParam));

        //选填-发送短信流水号
//        $request->setOutId("1234");

        //发起访问请求
        $acsResponse = $acsClient->getAcsResponse($request);
        if($acsResponse->Code === 'OK'){
            return true;
        }

        return false;
    }

}