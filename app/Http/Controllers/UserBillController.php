<?php

namespace App\Http\Controllers;

use App\Services\PidService;
use App\Services\TaobaoTokenService;
use App\Services\UserBillService;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Response;

class UserBillController extends Controller
{
    /**
     * 显示管理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request){
        return view("admin.user_bill.user_bill_list");
    }

    /**
     * 列表
     * @param Request $request
     * @return mixed
     */
    public function getList(Request $request){
        $data = (new UserBillService())->billList($request);
        return Response::json($data);
    }

}
