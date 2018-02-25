<?php

namespace App\Http\Controllers;

use App\Services\NoticeService;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Response;

class NoticeController extends Controller
{
    /**
     * 管理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request){
        return view("admin.notice.notice_list");
    }

    /**
     * 列表
     * @param Request $request
     * @return mixed
     */
    public function getList(Request $request){
        $data = (new NoticeService())->getList($request);
        return Response::json($data);
    }

    /**
     * 保存
     * @param Request $request
     * @return static
     */
    public function save(Request $request){
        try{
            $data = (new NoticeService())->save($request);
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
            (new NoticeService())->delete($id);
        }catch (\Exception $e){
            return $this->ajaxError($e->getMessage());
        }

        return $this->ajaxSuccess();
    }
}
