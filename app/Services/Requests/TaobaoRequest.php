<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/27
 * Time: 10:45
 */

namespace App\Services\Requests;


use App\Helpers\ProxyClient;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\Cache;

class TaobaoRequest{

    protected $appKey = 12574478;
    protected $version = "1.0";
    protected $h5Tk;
    protected $client;
    protected $cookieJar;

    /**
     * 淘宝请求cookie缓存
     * @var string
     */
    private $cookieCacheKey = __CLASS__.':cookies';

    /**
     * cookie缓存时间
     * @var int
     */
    private $cookieCacheTime = 20;

    public function __construct(){
        $this->client = new ProxyClient(['cookie'=>true]);
        $this->cookieJar = new CookieJar;

        $cookies = Cache::get($this->cookieCacheKey);
        if($cookies){
            $this->cookieJar = $cookies;
        }
    }

    /**
     * 淘宝接口请求
     * @param $api
     * @param $data
     * @param null $extraData
     * @return mixed
     */
    public function requestWithH5tk($api, $data, $extraData = null){
        if(is_array($data)){
            $data = json_encode($data);
        }
        $h5TkCookie = $this->cookieJar->getCookieByName('_m_h5_tk');
        if($h5TkCookie){
            $this->h5Tk = explode('_', $h5TkCookie->getValue())[0];
        }

        $t		= intval(microtime(true)*1000);
        $cookieUrl	= 'https://acs.m.taobao.com/h5/'.$api.'/1.0/?type=json&api='.$api.'&v='.$this->version;

        //拼接实际请求地址
        $sign	= md5($this->h5Tk.'&'.$t.'&'.$this->appKey.'&'.$data);
        $url	= $cookieUrl.'&appKey='.$this->appKey.'&sign='.$sign.'&t='.$t.'&data='.urlencode($data);
        if($extraData){
            $url .= "&".http_build_query($extraData);
        }

        //第一次取cookie参数
        $response = $this->client->request('GET', $url, ['cookies' => $this->cookieJar])->getBody()->getContents();

        if(strpos($response, "令牌为空") || strpos($response, '令牌过期')){
            Cache::put($this->cookieCacheKey, $this->cookieJar, $this->cookieCacheTime);
            return $this->requestWithH5tk($api, $data, $extraData);
        }

        return json_decode($response, true)['data'];
    }

}