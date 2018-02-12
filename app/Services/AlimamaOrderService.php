<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2018/2/7
 * Time: 15:09
 */

namespace App\Services;


use App\Helpers\QueryHelper;
use App\Models\AlimamaOrder;
use App\Models\UserOrderIncome;

class AlimamaOrderService
{
    /**
     * 订单列表
     * @param \Illuminate\Http\Request $request
     */
    public function getList($request){
        $query = UserOrderIncome::query()->from((new UserOrderIncome())->getTable() . ' as income');
        $query->leftJoin((new AlimamaOrder())->getTable() . ' as aliorder', 'aliorder.id', '=', 'income.order_id')
//            ->where('income.user_id', $orderUserId)
            ->orderBy("create_time", "desc");

        $orderStateCondition = [];

//        if($orderStateCondition){
//            $query->whereIn('aliorder.order_state', $orderStateCondition);
//        }

        $query->select([
            "aliorder.id",
            "aliorder.goods_id",
            "aliorder.goods_title",
            "aliorder.shop_name",
            "aliorder.create_time",
            "aliorder.order_state",
            "aliorder.pay_money",
            "aliorder.predict_money",
            "aliorder.settle_money",
            "aliorder.predict_income",
            "income.share_rate as user_rate"
        ]);

        $orderList = (new QueryHelper())->pagination($query);

        $userService = new UserService();

        foreach ($orderList as &$order){
            //订单状态
//            $order['order_state_str'] = AlimamaOrder::getOrderStateStr($order['order_state']);
//            //预估收入
//            $order['predict_money'] = round($userService->getUserMoney($order['predict_money'], $order['user_rate']), 2);
//            //预估结算收入
//            $order['predict_income'] = round($userService->getUserMoney($order['predict_income'], $order['user_rate']), 2);
        }

        return $orderList;
    }
}