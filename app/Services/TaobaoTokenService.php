<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/31
 * Time: 17:36
 */

namespace App\Services;


use App\Models\TaobaoToken;

class TaobaoTokenService
{
    /**
     * 获取token
     * @param $memberId
     * @return mixed
     */
    public function getAuthToken($memberId){
        return TaobaoToken::where("member_id", $memberId)->first();
    }

    /**
     * 获取token
     * @param $memberId
     * @return mixed
     */
    public function getToken($memberId){
        $token = $this->getAuthToken($memberId);
        if(!$token){
            return null;
        }
        return $token['access_token'];
    }

}