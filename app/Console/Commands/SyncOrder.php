<?php

namespace App\Console\Commands;

use App\Events\CalculateOrderEvent;
use App\Models\AlimamaOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SyncOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync_order {--from_time=} {--to_time=} {--file=} {--update=} {--increase=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步联盟订单';

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
        $nowDate = Carbon::now()->toDateString();
        //同步开始日期
        $fromTime = $this->option('from_time') ?: $nowDate;
        //同步结束日期
        $toTime = $this->option('to_time') ?: $nowDate;
        //指定文件
        $excelFile = $this->option('file');
        //更新未结算订单
        $update = $this->option('update');
        //增量同步
        $increase = $this->option('increase');


        //未结算订单同步
        if($update){
            $this->syncNotSettled();
        }
        //增量同步
        else if($increase){
            $this->syncIncrease();
        }
        //同步指定文件
        else if($excelFile){
            $file = storage_path("download/".$excelFile);
            if(!is_file($file)){
                throw new \Exception("文件不存在");
            }
            $this->importFile($file);
        }
        //同步指定时间
        else{
            $this->syncFromTime($fromTime, $toTime);
        }
    }


    /**
     * 增量同步
     */
    public function syncIncrease(){
        $time = Carbon::now()->startOfDay()->toDateTimeString();
        $lastCreateTime = AlimamaOrder::where("create_time", ">=", $time)->max("create_time");
        $lastCreateTime = $lastCreateTime ?: $time;

        $this->syncFromTime($lastCreateTime, Carbon::now()->toDateTimeString());
    }

    /**
     * 按时间同步
     * @param $fromTime
     * @param $toTime
     * @throws \Exception
     */
    public function syncFromTime($fromTime, $toTime){
        $file = $this->downloadFile($fromTime, $toTime);
        if(!is_file($file)){
            throw new \Exception("文件不存在");
        }
        $this->importFile($file);
    }

    /**
     * 同步未结算订单
     */
    public function syncNotSettled(){
        //时间区间
        $timeRanges = [];
        $notSettledTimes = AlimamaOrder::where('order_state', AlimamaOrder::ORDERSTATE_PAYED)->orderBy("create_time", "desc")->pluck("create_time")->toArray();
        foreach ($notSettledTimes as $settledTime){
            $time = new Carbon($settledTime);
            $date = $time->toDateString();
            if(!isset($timeRanges[$date])){
                $timeRanges[$date] = [];
            }
            $timeRanges[$date][] = $time->toDateTimeString();
        }

        foreach ($timeRanges as $range){
            $count = count($range);
            $toTime = $range[0];
            $fromTime = $range[$count-1];
            //开始时间和结束相同，则结束时间+1s
            if($count == 1){
                $toTime = (new Carbon($toTime))->addSecond(1)->toDateTimeString();
            }
            $this->syncFromTime($fromTime, $toTime);
        }
    }

    /**
     * 下载文件
     * @param $fromTime
     * @param $toTime
     * @return string
     * @throws \Exception
     */
    public function downloadFile($fromTime, $toTime){
        $downloadUrl = "http://pub.alimama.com/report/getTbkPaymentDetails.json?queryType=1&payStatus=&DownloadID=DOWNLOAD_REPORT_INCOME_NEW&startTime={$fromTime}&endTime={$toTime}";

        $cookie = $this->getCookie();
        if(!$cookie){
            throw new \Exception("获取cookie失败");
        }

        $client = (new \GuzzleHttp\Client([
            'headers' => [
                'cookie' => $cookie,
            ]
        ]));

        $dir = storage_path("download");
        if(!is_dir($dir)){
            @mkdir($dir);
        }
        $file = $dir."/order_".time().mt_rand(1000, 9999).".xls";
        $response = $client->get($downloadUrl, ['save_to' => $file]);
        //下载文件类型错误，删除文件
        if(strpos($response->getHeader('Content-Type')[0], "application/vnd.ms-excel") === false){
            @unlink($file);
        }

        return $file;
    }

    /**
     * 导入文件
     * @param $file
     */
    public function importFile($file){
        $content = Excel::load($file)->get()->toArray();

        $orders = [];
        foreach ($content as $item){
            $orderNo = $item['订单编号'];
            $orderState = $this->getOrderState($item['订单状态']);
            $goodsId = $item['商品id'];
            $goodsTitle = $item['商品信息'];
            $goodsNum = $item['商品数'];
            $goodsPrice = $item['商品单价'];
            $sellerName = $item['掌柜旺旺'];
            $shopName = $item['所属店铺'];
            $incomeRate = floatval($item['收入比率']);
            $shareRate = floatval($item['分成比率']);
            $payMoney = $item['付款金额'];
            $settleMoney = $item['结算金额'];
            $settleTime = $item['结算时间'];
            $predictMoney = $item['效果预估'];
            $predictIncome = $item['预估收入'];
            $commissionRate = floatval($item['佣金比率']);
            $commissionMoney = $item['佣金金额'];
            $subsidyRate = floatval($item['补贴比率']);
            $subsidyMoney = $item['补贴金额'];
            $subsidyType = $item['补贴类型'];
            $siteId = $item['来源媒体id'];
            $adzoneId = $item['广告位id'];
            $payPlatform = $item['订单类型'];
            $platform = $item['成交平台'];
            $createTime = $item['创建时间'];
            $clickTime = $item['点击时间'];

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
                        $this->info($orderNo." 同步成功");
                    }else{
                        $this->info($orderNo." 状态未更新");
                    }
                }else{
                    if(!$orderModel = AlimamaOrder::create($order)){
                        throw new \Exception("添加失败");
                    }
                    event(new CalculateOrderEvent($orderModel));
                    $this->info($orderNo." 同步成功");
                }
            }catch (\Exception $e){
                $this->error($orderNo." 同步失败");
            }
        }

        //删除文件
        @unlink($file);
    }

    /**
     * 获取cookie
     * @return bool
     */
    public function getCookie(){
        $result = (new \GuzzleHttp\Client())->get(config('taobao.alimama_cookie_url'))->getBody()->getContents();
        $result = json_decode($result, true);
        if(json_last_error()){
            throw new \Exception("获取阿里妈妈cookie失败");
            return false;
        }
        if(isset($result['errmsg']) && $result['errmsg']){
            throw new \Exception($result['errmsg']);
        }
        return $result['data'];
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
