<?php

namespace App\Console\Commands;

use App\Models\AlimamaOrder;
use App\Models\SystemPids;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class CreatePid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create_pid {--num=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '批量创建PID';

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
        $total = $this->option('num');

        $tbToken = null;
        $memberId = 123195020;
        $siteId = 42138501;
        $createUrl = "http://pub.alimama.com/common/adzone/selfAdzoneCreate.json";

        $cookie = (new SyncOrder())->getCookie();
        if(!$cookie){
            throw new \Exception("获取cookie失败");
        }

        $matchToken = preg_match('/_tb_token_=(.*?);/', $cookie, $match);
        if($matchToken){
            $tbToken = $match[1];
        }

        $client = (new \GuzzleHttp\Client([
            'headers' => [
                'cookie' => $cookie,
            ]
        ]));

        for ($n = 0; $n<$total; $n++){
            $zoneName = "tk".uniqid().time();

            $response = $client->post($createUrl, [
                'form_params' => [
                    'tag' => 29,
                    'gcid' => 8,
                    'siteid' => $siteId,
                    'selectact' => 'add',
                    'newadzonename' => $zoneName,
                    't' => time(),
                    '_tb_token_' => $tbToken,
                ]
            ])->getBody()->getContents();

            $result = json_decode($response, true);
            if(json_last_error()){
                $this->error($zoneName." 创建失败");
                continue;
            }else{
                try{
                    $zoneId = $result['data']['adzoneId'];
                    $pid = "mm_{$memberId}_{$siteId}_{$zoneId}";
                    if(!$this->addPid($zoneName, $memberId, $siteId, $zoneId)){
                        throw new \Exception("已存在");
                    }
                    $this->info($pid." 创建成功  还剩：".($total-$n-1));
                }catch (\Exception $e){
                    $this->error($pid. "已存在");
                }
            }
        }
    }

    /**
     * 添加pid到数据库
     * @param $name
     * @param $memberId
     * @param $siteId
     * @param $adzoneId
     * @return bool
     */
    public function addPid($name, $memberId, $siteId, $adzoneId){
        $pid = "mm_{$memberId}_{$siteId}_{$adzoneId}";
        $data = [
            'name' => $name,
            'member_id' => $memberId,
            'site_id' => $siteId,
            'adzone_id' => $adzoneId,
            'pid' => $pid,
            'add_time' => Carbon::now(),
        ];

        try{
            if(SystemPids::create($data)){
                return $pid;
            }
        }catch (\Exception $e){
        }
        return false;
    }
}
