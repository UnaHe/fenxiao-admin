<?php

namespace App\Http\Controllers;

use App\Services\PidService;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Response;

class PidController extends Controller
{
    /**
     * 显示管理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request){
        return view("admin.pid.pid_list");
    }

    /**
     * 列表
     * @param Request $request
     * @return mixed
     */
    public function getList(Request $request){
        $data = (new PidService())->pidList($request);
        return Response::json($data);
    }

}
