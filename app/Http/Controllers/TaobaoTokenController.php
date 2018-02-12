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
        return view("admin.taobao_token.taobao_token_list");
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

}
