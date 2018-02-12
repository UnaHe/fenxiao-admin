<?php

namespace App\Http\Controllers;

use App\Banner;
use App\Services\BannerService;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Response;

class BannerController extends Controller
{
    /**
     * 管理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request){
        return view("admin.banner.banner_list");
    }

    /**
     * 列表
     * @param Request $request
     * @return mixed
     */
    public function getList(Request $request){
        $data = (new BannerService())->bannerList($request);
        return Response::json($data);
    }

    /**
     * 保存
     * @param Request $request
     * @return static
     */
    public function save(Request $request){
        try{
            $data = (new BannerService())->save($request);
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
            (new BannerService())->delete($id);
        }catch (\Exception $e){
            return $this->ajaxError($e->getMessage());
        }

        return $this->ajaxSuccess();
    }
}
