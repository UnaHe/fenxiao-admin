<?php

namespace App\Http\Controllers;

use App\Banner;
use App\Services\BannerService;
use App\Services\WechatDomainService;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Response;

class WechatDomainController extends Controller
{
    /**
     * 管理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request){
        return view("admin.wechat_domain.domain_list");
    }

    /**
     * 列表
     * @param Request $request
     * @return mixed
     */
    public function getList(Request $request){
        $data = (new WechatDomainService())->bannerList($request);
        return Response::json($data);
    }

    /**
     * 保存
     * @param Request $request
     * @return static
     */
    public function save(Request $request){
        try{
            $data = (new WechatDomainService())->save($request);
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
            (new WechatDomainService())->delete($id);
        }catch (\Exception $e){
            return $this->ajaxError($e->getMessage());
        }

        return $this->ajaxSuccess();
    }
}
