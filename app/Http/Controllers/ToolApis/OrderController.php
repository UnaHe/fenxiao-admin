<?php

namespace App\Http\Controllers\ToolApis;

use App\Http\Controllers\Controller;
use App\Jobs\SaveSyncOrder;
use App\Models\AlimamaOrder;
use App\Services\AlimamaOrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * 订单统计信息
     * @return static
     */
    public function statistics(Request $request)
    {
        $memberId = $request->get('member_id');
        $orderStatistics = (new AlimamaOrderService())->orderStatistics();
        return $this->ajaxSuccess($orderStatistics);
    }

    /**
     * 保存订单信息
     * @param Request $request
     */
    public function save(Request $request){
        $memberId = $request->post('member_id');
        $data = $request->post('data');
        if(!$memberId || !$data){
            return $this->ajaxError("参数错误");
        }

        try{
            $data = str_replace(" ", "+", $data);
            $data = gzdecode(base64_decode($data));

            SaveSyncOrder::dispatch($memberId, $data);
        }catch (\Exception $e){
            return $this->ajaxError("保存失败");
        }

        return $this->ajaxSuccess();
    }

    /**
     * 增量同步时间点
     * @param Request $request
     * @return static
     */
    public function syncIncreaseTime(Request $request){
        $memberId = $request->get('member_id');

        $time = Carbon::now()->startOfDay()->toDateTimeString();
        $lastCreateTime = AlimamaOrder::where([
            ["create_time", ">=", $time],
            ['member_id', "=", $memberId]
        ])->max("create_time");
        $lastCreateTime = $lastCreateTime ?: $time;

        $data = [[
            'start_time' => $lastCreateTime,
            'end_time' => Carbon::now()->toDateTimeString()
        ]];

        return $this->ajaxSuccess($data);
    }

    /**
     * 未结算订单时间点
     * @param Request $request
     * @return static
     */
    public function notSettleTime(Request $request){
        $memberId = $request->get('member_id');

        //时间区间
        $timeRanges = [];
        $notSettledTimes = AlimamaOrder::where([
            ['order_state', '=', AlimamaOrder::ORDERSTATE_PAYED],
            ['member_id', "=", $memberId]
        ])->orderBy("create_time", "desc")->pluck("create_time")->toArray();

        foreach ($notSettledTimes as $settledTime){
            $time = new Carbon($settledTime);
            $date = $time->toDateString();
            if(!isset($timeRanges[$date])){
                $timeRanges[$date] = [];
            }
            $timeRanges[$date][] = $time->toDateTimeString();
        }

        $times = [];
        foreach ($timeRanges as $range){
            $count = count($range);
            $toTime = $range[0];
            $fromTime = $range[$count-1];
            //开始时间和结束相同，则结束时间+1s
            if($count == 1){
                $toTime = (new Carbon($toTime))->addSecond(1)->toDateTimeString();
            }

            $times[] = [
                'start_time' => $fromTime,
                'end_time' => $toTime
            ];
        }

        return $this->ajaxSuccess($times);
    }

}
