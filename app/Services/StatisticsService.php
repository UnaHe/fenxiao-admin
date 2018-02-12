<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2018/2/6
 * Time: 14:45
 */

namespace App\Services;
use App\Helpers\UtilsHelper;
use App\Models\AlimamaOrder;
use App\Models\UserOrderIncome;
use App\Models\UserTree;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * 统计报表
 * Class StatisticsService
 * @package App\Services
 */
class StatisticsService
{
    /**
     * 查询指定日期订单信息
     * @param Carbon $day 指定日期
     * @param int $userId 用户id
     */
    public function day($day, $userId){
        $data = $this->predict($day->startOfDay()->toDateTimeString(), $day->endOfDay()->toDateTimeString(), $userId, $userId, 1);
        $data['day'] = $day->toDateString();

        return $data;
    }

    /**
     * 查询月收益数据
     * @param int $userId 用户id
     * @return array
     */
    public function month($userId){
        $startMonth = Carbon::now()->startOfMonth()->startOfDay()->toDateTimeString();
        $endMonth = Carbon::now()->endOfMonth()->endOfDay()->toDateTimeString();
        $startOfLastMonth = Carbon::now()->subMonth(1)->startOfMonth()->startOfDay()->toDateTimeString();
        $endOfLastMonth = Carbon::now()->subMonth(1)->endOfMonth()->endOfDay()->toDateTimeString();

        //当月预估
        $curMonthPredict = $this->predict($startMonth, $endMonth, $userId, $userId, 1);
        //上月预估
        $lastMonthPredict = $this->predict($startOfLastMonth, $endOfLastMonth, $userId, $userId, 1);

        //当月结算
        $curMonthSettle = $this->predict($startMonth, $endMonth, $userId, $userId, 1, 1);
        //上月结算
        $lastMonthSettle = $this->predict($startOfLastMonth, $endOfLastMonth, $userId, $userId, 1, 1);


        $data = [
            'predict' => [
                'cur_month' => $curMonthPredict,
                'last_month' => $lastMonthPredict
            ],
            'settle' => [
                'cur_month' => $curMonthSettle,
                'last_month' => $lastMonthSettle
            ]
        ];

        return $data;
    }

    /**
     * 查询团队月收益数据
     * @param int $userId 用户id
     * @return array
     */
    public function teamMonth($userId){
        $startMonth = Carbon::now()->startOfMonth()->startOfDay()->toDateTimeString();
        $endMonth = Carbon::now()->endOfMonth()->endOfDay()->toDateTimeString();
        $startOfLastMonth = Carbon::now()->subMonth(1)->startOfMonth()->startOfDay()->toDateTimeString();
        $endOfLastMonth = Carbon::now()->subMonth(1)->endOfMonth()->endOfDay()->toDateTimeString();

        $user = UserTree::where("user_id", $userId)->first();

        //下两层团队成员id
        $teamUserIds = UserTree::where([
            ['left_val', ">", $user['left_val']],
            ['right_val', "<", $user['right_val']],
            ['level', "<=", $user['level']+2],
        ])->pluck("user_id")->toArray();

        //当月预估
        $curMonthPredict = $this->predict($startMonth, $endMonth, $teamUserIds, null, 1);
        //上月预估
        $lastMonthPredict = $this->predict($startOfLastMonth, $endOfLastMonth, $teamUserIds, null, 1);

        //当月结算
        $curMonthSettle = $this->predict($startMonth, $endMonth, $teamUserIds, null, 1,1);
        //上月结算
        $lastMonthSettle = $this->predict($startOfLastMonth, $endOfLastMonth, $teamUserIds, null, 1,1);

        $data = [
            'predict' => [
                'cur_month' => $curMonthPredict,
                'last_month' => $lastMonthPredict
            ],
            'settle' => [
                'cur_month' => $curMonthSettle,
                'last_month' => $lastMonthSettle
            ]
        ];

        return $data;
    }

    /**
     * 查询用户团队奖励收入（团队提成）
     * @param int $userId 用户id
     * @return array
     */
    public function userTeamIncome($userId){
        $startMonth = Carbon::now()->startOfMonth()->startOfDay()->toDateTimeString();
        $endMonth = Carbon::now()->endOfMonth()->endOfDay()->toDateTimeString();

        $user = UserTree::where("user_id", $userId)->first();

        //所有团队成员id
        $teamUserIds = UserTree::where([
            ['left_val', ">", $user['left_val']],
            ['right_val', "<", $user['right_val']],
        ])->pluck("user_id")->toArray();

        //当月结算
        $curMonthPredict = $this->predict($startMonth, $endMonth, $teamUserIds, $userId, 0, 1);

        $data = [
            'settle' => [
                'cur_month' => $curMonthPredict,
            ],
        ];

        return $data;
    }


    /**
     * 预估数据
     * @param string $startTime 开始时间
     * @param string $endTime 结束时间
     * @param int|array $orderUserId 订单用户
     * @param int|array $userId 返利用户
     * @param int $equalUser 订单用户和返利用户是否相同
     * @param int $settlement 是否计算结算
     * @return array
     */
    public function predict($startTime, $endTime, $orderUserId, $userId, $equalUser=1, $settlement=0){
        $params = [];

        //返利用户id
        if($userId){
            $params['user_id'] = $userId;
        }

        //订单用户id
        if($orderUserId){
            $params['order_user_id'] = $orderUserId;
        }

        //返利用户和订单用户是否相等
        if($equalUser){
            $params['equal_user'] = 1;
        }

        if($settlement){
            //结算时间
            $params['start_settle_time'] = $startTime;
            $params['end_settle_time'] = $endTime;
            //结算订单状态：已结算
            $params['order_state'] = [
                AlimamaOrder::ORDERSTATE_SETTLE
            ];
        }else{
            //预估时间
            $params['start_create_time'] = $startTime;
            $params['end_create_time'] = $endTime;
            //预估订单状态： 已付款和已结算
            $params['order_state'] = [
                AlimamaOrder::ORDERSTATE_PAYED,
                AlimamaOrder::ORDERSTATE_SETTLE
            ];
        }

        $result = $this->getOrders($params);

        //预估收入
        $preMoneyTotal = 0;
        //付款订单数
        $payOrderNum = 0;
        //付款金额
        $payMoneyTotal = 0;

        //数据库字段，结算收入predict_income 预估金额predict_money
        $incomeKey = $settlement ? 'predict_income' : 'predict_money';
        //数据库字段，结算金额settle_money 付款金额pay_money
        $moneyKey = $settlement ? 'settle_money' : 'pay_money';

        $orderIds = [];

        $userService = new UserService();
        foreach ($result as $item){
            //预估收入 = (订单预估 - 系统扣减手续费) * 用户分成比例
            $preMoney = $userService->getUserMoney($item[$incomeKey], $item['user_rate']);
            $preMoneyTotal = bcadd($preMoneyTotal, $preMoney, 5);

            if(!isset($orderIds[$item['id']])){
                $payOrderNum++;
                $payMoneyTotal = bcadd($payMoneyTotal, $item[$moneyKey], 5);
                $orderIds[$item['id']] = $item;
            }
        }

        $data = [
            'predict_money' => round($preMoneyTotal, 2),
            'pay_order_num' => $payOrderNum,
            'pay_money_total' => round($payMoneyTotal, 2),
        ];

        return $data;
    }


    /**
     * 获取订单
     * @param $queryParams
     */
    public function getOrders($queryParams){
        $query = AlimamaOrder::query()->from((new AlimamaOrder())->getTable() . ' as aliorder');
        $query->leftJoin((new UserOrderIncome())->getTable() . ' as income', 'aliorder.id', '=', 'income.order_id');

        //订单状态
        $orderState = UtilsHelper::arrayValue($queryParams, 'order_state');
        if(!$orderState){
            $orderState = [
                AlimamaOrder::ORDERSTATE_PAYED,
                AlimamaOrder::ORDERSTATE_SETTLE,
            ];
        }
        if($orderState){
            $query->whereIn('aliorder.order_state', $orderState);
        }

        //开始下单时间
        if($startCreateTime = UtilsHelper::arrayValue($queryParams, 'start_create_time')){
            $query->where('aliorder.create_time', '>=', $startCreateTime);
        }

        //结束下单时间
        if($endCreateTime = UtilsHelper::arrayValue($queryParams, 'end_create_time')){
            $query->where('aliorder.create_time', '<=', $endCreateTime);
        }

        //开始结算时间
        if($startSettleTime = UtilsHelper::arrayValue($queryParams, 'start_settle_time')){
            $query->where('aliorder.settle_time', '>=', $startSettleTime);
        }

        //结束结算时间
        if($endSettleTime = UtilsHelper::arrayValue($queryParams, 'end_settle_time')){
            $query->where('aliorder.settle_time', '<=', $endSettleTime);
        }

        //订单用户id
        if($orderUserId = UtilsHelper::arrayValue($queryParams, 'order_user_id')){
            $orderUserId = is_array($orderUserId) ? $orderUserId : [$orderUserId];
            $query->whereIn('income.order_user_id', $orderUserId);
        }

        //返利用户id
        if($userId = UtilsHelper::arrayValue($queryParams, 'user_id')){
            $userId = is_array($userId) ? $userId : [$userId];
            $query->whereIn('income.user_id', $userId);
        }

        //返利用户和订单用户是否相同
        if($equalUser = UtilsHelper::arrayValue($queryParams, 'equal_user')){
            $query->whereRaw('income.user_id=income.order_user_id');
        }

        $query->select(["aliorder.id", "aliorder.order_state", "aliorder.pay_money", "aliorder.predict_money", "aliorder.settle_money", "aliorder.predict_income", "income.share_rate as user_rate"]);

        $result = $query->get()->toArray();

        return $result;
    }
}