<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/23
 * Time: 15:51
 */
namespace App\Services;


use App\Helpers\CacheHelper;
use App\Helpers\ErrorHelper;
use App\Helpers\ProxyClient;
use App\Models\Goods;
use App\Models\TaobaoPid;
use App\Models\TaobaoToken;
use App\Services\Requests\CouponGet;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class TaobaoService
{
    /**
     * 保存淘宝token
     * @param $userId
     * @param $tokens
     */
    public function saveAuthToken($userId, $tokens){
        $time = time();
        $now  = date('Y-m-d H:i:s', $time);

        $token = TaobaoToken::where("member_id", $userId)->first();
        if(!$token){
            $token = new TaobaoToken();
            $token['create_time'] = $now;
            $token['user_id'] = $userId;
        }
        try{
            $token['access_token'] = $tokens['access_token'];
            $token['token_type'] = $tokens['token_type'];
            $token['expires_at'] = date('Y-m-d H:i:s', $time+$tokens['expires_in']);
            $token['refresh_token'] = $tokens['refresh_token'];
            $token['re_expires_at'] = $tokens['re_expires_in'] ? date('Y-m-d H:i:s', $time+$tokens['re_expires_in']) : null;
            $token['taobao_user_id'] = $tokens['taobao_user_id'];
            $token['taobao_user_nick'] = $tokens['taobao_user_nick'];
            $token['update_time'] = $now;
            $token->save();

        }catch (\Exception $e){
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return true;
    }


    /**
     * 刷新token
     * @param $userId
     * @return bool
     */
    public function refreshUserToken($userId){
        $token = TaobaoToken::where("member_id", $userId)->first();
        if(!$token){
            return false;
        }

        try{
            $url = 'https://oauth.taobao.com/token';
            $client = new Client();
            $response = $client->post($url, [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'client_id' => config('taobao.appkey'),
                    'client_secret' => config('taobao.secretkey'),
                    'refresh_token' => $token['refresh_token']
                ]
            ])->getBody()->getContents();

            $token = json_decode($response, true);
            return $this->saveAuthToken($userId, $token, null);
        }catch (\Exception $e){
        }

        return false;
    }




}
