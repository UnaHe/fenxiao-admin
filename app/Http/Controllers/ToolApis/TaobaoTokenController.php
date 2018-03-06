<?php

namespace App\Http\Controllers\ToolApis;

use App\Http\Controllers\Controller;
use App\Services\TaobaoTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaobaoTokenController extends Controller
{
    /**
     * 获取token
     * @param Request $request
     * @return mixed
     */
    public function getToken(Request $request){
        $memberId = $request->get('member_id');

        $data = (new TaobaoTokenService())->getAuthToken($memberId);
        if(!$data){
            return $this->ajaxError("未授权");
        }
        return $this->ajaxSuccess($data);
    }

    /**
     * 保存token
     * @param Request $request
     */
    public function save(Request $request){
        $memberId = $request->post('member_id');
        $tokenUrl = urldecode($request->post('token_url'));
        $tokens = null;

        if($tokenUrl){
            $urlInfo = parse_url($tokenUrl);
            parse_str($urlInfo['fragment'], $tokens);
            if(!(array_key_exists('access_token', $tokens)
                && array_key_exists('token_type', $tokens)
                && array_key_exists('expires_in', $tokens)
                && array_key_exists('refresh_token', $tokens)
                && array_key_exists('re_expires_in', $tokens)
                && array_key_exists('taobao_user_id', $tokens)
                && array_key_exists('taobao_user_nick', $tokens)
            )){
                return $this->ajaxError("授权结果地址错误");
            }
        }

        try{
            (new TaobaoTokenService())->saveToken($memberId, $tokens);
        }catch (\Exception $e){
            return $this->ajaxError($e->getMessage());
        }

        return $this->ajaxSuccess();

    }

}
