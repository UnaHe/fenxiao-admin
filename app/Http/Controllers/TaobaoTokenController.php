<?php

namespace App\Http\Controllers;

use App\Services\PidService;
use App\Services\TaobaoTokenService;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Response;

class TaobaoTokenController extends Controller
{
    /**
     * 显示管理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request){
        $authUrl = 'https://oauth.taobao.com/authorize?response_type=token&client_id='.config("taobao.appkey").'&state=pyt&view=wap';
        return view("admin.taobao_token.taobao_token_list", [
            'auth_url' => $authUrl
        ]);
    }

    /**
     * 列表
     * @param Request $request
     * @return mixed
     */
    public function getList(Request $request){
        $data = (new TaobaoTokenService())->tokenList($request);
        return Response::json($data);
    }

    /**
     * 刷新token
     * @param Request $request
     */
    public function refreshToken(Request $request){
        $id = $request->post('id');
        if(!$id){
            return $this->ajaxError("参数错误");
        }
        try{
            (new TaobaoTokenService())->refreshToken($id);
        }catch (\Exception $e){
            return $this->ajaxError($e->getMessage());
        }

        return $this->ajaxSuccess();
    }

    /**
     * 保存token
     * @param Request $request
     */
    public function save(Request $request){
        $memberId = $request->post('member_id');
        $tokenUrl = $request->post('token_url');
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


    /**
     * 删除
     * @param Request $request
     * @return static
     */
    public function del(Request $request){
        $id = $request->input("id");

        try{
            (new TaobaoTokenService())->delete($id);
        }catch (\Exception $e){
            return $this->ajaxError($e->getMessage());
        }

        return $this->ajaxSuccess();
    }
}
