<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/27
 * Time: 13:00
 */
namespace App\Helpers;

use App\Services\SysConfigService;
use GuzzleHttp\Client;

class UrlHelper
{
    /**
     * 网站缩短
     * @param $url
     * @return null
     * @throws \Exception
     */
    public function shortUrl($url){
        $url = urlencode($url);
        $appId = config('services.sina_open.key');
        $apiUrl = "http://api.weibo.com/2/short_url/shorten.json?source=5786724301&url_long=".$url;
        $client = new Client(['timeout' => 3]);
        try{
            if((new SysConfigService())->get('sina_short_url') != 1){
                throw new \Exception('暂停使用新浪');
            }
            $response = $client->get($apiUrl)->getBody()->getContents();
            if(!$response){
                throw new \Exception('短网址转换失败');
            }
            $response = json_decode($response, true);
            $code = parse_url($response['urls'][0]['url_short']);
            $path = trim($code['path'], '/');
            if(!isset($response['urls'][0]['url_short'])){
                return null;
            }
            // 转换格式不正确进入下一次转换.
            if (!(strlen($path) > 1 && strlen($path) <= 8)){
                throw new \Exception('短网址转换失败');
            }
            return $response['urls'][0]['url_short'];
        }catch (\Exception $e){
            $apiUrl = "http://xapi.in/urls/add?&secretkey=00007821518940188wzk.im!@6b2494e&lurl=".$url;
            $response = $client->get($apiUrl)->getBody()->getContents();
            if(strpos($response,"http") === false){
                throw new \Exception('短网址转换失败');
            }
            return $response;
        }
    }

    /**
     * 修复url前缀
     * @param $url
     * @return string
     */
    public static function fixUrlPrefix($url){
        if (strpos($url,"http") !== 0){
            $url = ltrim($url, ":/");
            return "http://".$url;
        }
        return $url;
    }
}