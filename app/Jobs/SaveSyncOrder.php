<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\CalculateOrderEvent;
use App\Models\AlimamaOrder;
use Illuminate\Support\Facades\Log;

class SaveSyncOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //订单json数据
    private $orderJsonStr;

    //订单所属member_id
    private $memberId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($memberId, $orderJsonStr)
    {
        $this->orderJsonStr = $orderJsonStr;
        $this->memberId = $memberId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $content = json_decode($this->orderJsonStr, true);

        $orders = [];
        foreach ($content as $item){
            $orderNo = $item['order_no'];
            $orderState = $this->getOrderState($item['order_state']);
            $goodsId = $item['goods_id'];
            $goodsTitle = $item['goods_title'];
            $goodsNum = $item['goods_num'];
            $goodsPrice = $item['goods_price'];
            $sellerName = $item['seller_name'];
            $shopName = $item['shop_name'];
            $incomeRate = floatval($item['income_rate']);
            $shareRate = floatval($item['share_rate']);
            $payMoney = $item['pay_money'];
            $settleMoney = $item['settle_money'];
            $settleTime = $item['settle_time'];
            $predictMoney = $item['predict_money'];
            $predictIncome = $item['predict_income'];
            $commissionRate = floatval($item['commission_rate']);
            $commissionMoney = $item['commission_money'];
            $subsidyRate = floatval($item['subsidy_rate']);
            $subsidyMoney = $item['subsidy_money'];
            $subsidyType = $item['subsidy_type'];
            $siteId = $item['site_id'];
            $adzoneId = $item['adzone_id'];
            $payPlatform = $item['pay_platform'];
            $platform = $item['platform'];
            $createTime = $item['create_time'];
            $clickTime = $item['click_time'];

            $arrayKey = $orderNo."_".$goodsId;
            if(isset($orders[$arrayKey])){
                $order = $orders[$arrayKey];
                $goodsNum += $order['goods_num'];
                $payMoney += $order['pay_money'];
                $settleMoney += $order['settle_money'];
                $predictMoney += $order['predict_money'];
                $predictIncome += $order['predict_income'];
                $commissionMoney += $order['commission_money'];
                $subsidyMoney += $order['subsidy_money'];
            }

            $orders[$arrayKey] = [
                'order_no' => $orderNo,
                'order_state' => $orderState,
                'goods_id' => $goodsId,
                'goods_title' => $goodsTitle,
                'goods_num' => $goodsNum,
                'goods_price' => $goodsPrice,
                'seller_name' => $sellerName,
                'shop_name' => $shopName,
                'income_rate' => $incomeRate,
                'share_rate' => $shareRate,
                'pay_money' => $payMoney,
                'settle_money' => $settleMoney,
                'settle_time' => $settleTime,
                'predict_money' => $predictMoney,
                'predict_income' => $predictIncome,
                'commission_rate' => $commissionRate,
                'commission_money' => $commissionMoney,
                'subsidy_rate' => $subsidyRate,
                'subsidy_money' => $subsidyMoney,
                'subsidy_type' => $subsidyType,
                'member_id' => $this->memberId,
                'site_id' => $siteId,
                'adzone_id' => $adzoneId,
                'pay_platform' => $payPlatform,
                'platform' => $platform,
                'create_time' => $createTime,
                'click_time' => $clickTime,
                'sync_time' => Carbon::now(),
            ];

        }

        foreach ($orders as $order){
            $orderNo = $order['order_no'];
            $where = [
                'order_no' => $orderNo,
                'goods_id' => $order['goods_id']
            ];

            try{
                $orderModel = AlimamaOrder::where($where)->first();

                if($orderModel){
                    if($orderModel['order_state'] != $order['order_state']){
                        if(!AlimamaOrder::where($where)->update($order)){
                            throw new \Exception("更新失败");
                        }
//                        $this->info($orderNo." 同步成功");
                    }else{
//                        $this->info($orderNo." 状态未更新");
                    }
                }else{
                    if(!$orderModel = AlimamaOrder::create($order)){
                        throw new \Exception("添加失败");
                    }
                    event(new CalculateOrderEvent($orderModel));
                }
            }catch (\Exception $e){
                Log::error($orderNo." 同步失败".$e->getMessage());
            }
        }
    }


    public function getOrderState($stateStr){
        $orderState = [
            '订单付款' => 1,
            '订单结算' => 2,
            '订单失效' => 3,
            '订单成功' => 4,
        ];

        return isset($orderState[$stateStr]) ? $orderState[$stateStr] : 0;
    }

}
