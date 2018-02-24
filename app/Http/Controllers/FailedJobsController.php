<?php

namespace App\Http\Controllers;

use App\Services\FailedJobsService;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Response;

class FailedJobsController extends Controller
{
    /**
     * 显示管理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request){
        return view("admin.failed_jobs.failed_jobs_list");
    }

    /**
     * 列表
     * @param Request $request
     * @return mixed
     */
    public function getList(Request $request){
        $data = (new FailedJobsService())->jobList($request);
        return Response::json($data);
    }

    /**
     * 重试任务
     * @param Request $request
     */
    public function retry(Request $request){
        $id = $request->post('id');
        if(!$id){
            return $this->ajaxError("参数错误");
        }
        try{
            (new FailedJobsService())->retry($id);
        }catch (\Exception $e){
            return $this->ajaxError($e->getMessage());
        }

        return $this->ajaxSuccess();
    }

}
