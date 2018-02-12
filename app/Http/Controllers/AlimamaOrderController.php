<?php

namespace App\Http\Controllers;

use App\Models\AlimamaOrder;
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
        $orderStates = AlimamaOrder::$ORDERSTATE;
        return view("admin.order.index", [
            'orderStates' => $orderStates
        ]);
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
     * 订单详情
     * @param Request $request
     * @return mixed
     */
    public function detail(Request $request){
        $orderId = $request->get('order_id');
        if(!$orderId){
            return $this->ajaxError("参数错误");
        }

        $data = (new AlimamaOrderService())->detail($orderId);
        return $this->ajaxSuccess($data);
    }

}
