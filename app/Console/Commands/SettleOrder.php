<?php

namespace App\Console\Commands;

use App\Models\AlimamaOrder;
use App\Models\SystemPids;
use App\Models\UserOrderIncome;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SettleOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settle_order {--from_time=} {--to_time=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '结算订单';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //结算开始日期
        $fromTime = $this->option('from_time') ?: Carbon::now()->subMonth(1)->startOfMonth()->startOfDay()->toDateTimeString();
        //结算结束日期
        $toTime = $this->option('to_time') ?: Carbon::now()->subMonth(1)->endOfMonth()->endOfDay()->toDateTimeString();
        $userService = new UserService();


        $query = AlimamaOrder::where([
            ['settle_time', ">=", $fromTime],
            ['settle_time', "<=", $toTime],
            ['order_state', "=", AlimamaOrder::ORDERSTATE_SETTLE],
        ])->whereNull("deal_time");

        $totalPredictIncomeQuery = clone $query;
        $totalPredictIncome = $totalPredictIncomeQuery->sum("predict_income");
        $predictTotalMoney = round($userService->getUserMoney($totalPredictIncome, 1),2);

        if($this->confirm("{$fromTime}至{$toTime}共有{$totalPredictIncome}元未结算，预计总共返利{$predictTotalMoney}，是否继续结算？") == false){
            return;
        }

        $orderListQuery = clone $query;
        $orderList = $orderListQuery->select([
            "id",
            "predict_income",
        ])->get()->toArray();

        $totalMoney = 0;
        foreach ($orderList as $order){
            $orderId = $order['id'];
            $orderIncome = $order['predict_income'];

            $users = UserOrderIncome::where("order_id", $orderId)->get()->toArray();
            try{
                DB::beginTransaction();
                foreach ($users as $user){
                    $money = $userService->getUserMoney($orderIncome, $user['share_rate']);
                    $money = round($money, 2);

                    if($money){
                        $totalMoney += $money;

                        if(!$userService->addBalance($user['user_id'], $money, "订单结算奖励")){
                            $msg = $user['user_id']." 返利失败";
                            $this->error($msg);
                            throw new \Exception($msg);
                        }
                        $this->info($user['user_id']." 返利".$money);
                    }
                }

                //修改订单为结算状态
                if(!AlimamaOrder::where("id", $orderId)->update(['deal_time' => Carbon::now()])){
                    throw new \Exception("更新订单状态失败");
                }

                DB::commit();

                $this->info("订单{$orderId} 结算成功");

            }catch (\Exception $e){
                $this->error("订单{$orderId} 结算失败");
                DB::rollBack();
            }

        }

        $this->info("预计总共返利{$predictTotalMoney}, 实际返利{$totalMoney}");

    }

}
