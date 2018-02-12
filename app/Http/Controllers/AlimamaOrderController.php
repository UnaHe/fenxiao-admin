<?php

namespace App\Http\Controllers;

use App\Orderlist;
use App\Services\AlimamaOrderService;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class AlimamaOrderController extends Controller
{
    /**
     * 显示订单管理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request){
        return view("admin.order.index");
    }

    /**
     * 订单列表
     * @param Request $request
     * @return mixed
     */
    public function getList(Request $request){
        $data = (new AlimamaOrderService())->getList($request);
        return Response::json($data);
    }



    /**
     * 汇款审核
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function remittance(){
        return view("admin.order.remittance");
    }

    /**
     * 汇款待审核数据列表
     * @param Request $request
     * @return mixed
     */
    public function getRemittanceList(Request $request){
        return $this->getOrderlist($request, Orderlist::STATUS_REMITTANCE);
    }


}
