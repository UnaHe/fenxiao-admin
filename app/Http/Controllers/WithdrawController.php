<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Services\WithdrawService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class WithdrawController extends Controller
{
    /**
     * 显示管理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request){
        return view("admin.withdraw.withdraw_list");
    }

    /**
     * 列表
     * @param Request $request
     * @return mixed
     */
    public function getList(Request $request){
        $data = (new WithdrawService())->getList($request);
        return Response::json($data);
    }

    /**
     * 提现详情
     * @param Request $request
     * @return mixed
     */
    public function detail(Request $request){
        $data = (new WithdrawService())->detail($request->post('id'));
        return $this->ajaxSuccess($data);
    }

    /**
     * 同意提现申请
     * @param Request $request
     * @return static
     */
    public function confirm(Request $request){
        if(!(new WithdrawService())->confirm($request->post('id'))){
            return $this->ajaxError("操作失败, 请重试");
        }

        return $this->ajaxSuccess();
    }

    /**
     * 拒绝提现申请
     * @param Request $request
     * @return static
     */
    public function refuse(Request $request){
        if(!(new WithdrawService())->refuse($request->post('id'))){
            return $this->ajaxError("操作失败, 请重试");
        }

        return $this->ajaxSuccess();
    }

}
