<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/11/6
 * Time: 10:02
 */

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Log;

class ProxyClient extends Client {
    /**
     * 代理ip获取地址
     */
    private $ipUrl;
    /**
     * 固定代理ip
     */
    private $ip;

    public function __construct(array $config = []){
        $this->ipUrl = config('proxy.proxy_ip_url');
        $this->ip = config('proxy.proxy_ip');

        if(!isset($config['headers'])){
            $config['headers']=[];
        }
        if(!isset($config['headers']['User-Agent'])){
            $config['headers']['User-Agent'] = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36';
        }
        if(!isset($config['proxy'])){
            $config['proxy'] = $this->getProxy();
        }
        if(!isset($config['connect_timeout'])){
            $config['connect_timeout'] = 5;
        }
        if(!isset($config['timeout'])){
            $config['timeout'] = 5;
        }

        parent::__construct($config);
    }

    public function request($method, $uri = '', array $options = []){
        $response = null;
        try{
            try{
                $response = parent::request($method, $uri, $options);
            }catch (\Exception $e){
                //代理过期，重试一次
                if($e instanceof ConnectException){
                    Log::error(__METHOD__." "."代理IP无法连接，刷新重试");
                    $options['proxy'] = $this->getProxy(true);
                    $response = parent::request($method, $uri, $options);
                }else{
                    throw $e;
                }
            }
        }catch (\Exception $e){
            //代理过期
            if($e instanceof ConnectException){
                Log::error(__METHOD__." "."新代理IP无法连接");
            }else{
                Log::error($e);
            }
        }

        return $response;
    }

    /**
     * 获取代理ip
     * @param bool $refresh 是否刷新
     */
    public function getProxy($refresh = false){
        if($this->ip){
            return $this->ip;
        }
        
        $ip = CacheHelper::getCache("ip");
        if($ip && !$refresh){
            return $ip;
        }

        $response = (new Client())->get($this->ipUrl)->getBody()->getContents();
        $ips = explode("\n", trim($response));
        $ip = $ips[0];

        CacheHelper::setCache($ip, 1, "ip");
        return $ip;
    }
}