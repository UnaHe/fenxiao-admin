<?php

namespace App\Console\Commands;

use App\Models\AlimamaOrder;
use App\Models\SystemPids;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class SyncPid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync_pid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步PID';

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
        $siteId = 42138501;
        $pageSize = 1000;
        $getUrl = "http://pub.alimama.com/common/adzone/adzoneManage.json?tab=3&perPageSize={$pageSize}&gcid=8";

        $cookie = (new SyncOrder())->getCookie();
        if(!$cookie){
            throw new \Exception("获取cookie失败");
        }

        $client = (new \GuzzleHttp\Client([
            'headers' => [
                'cookie' => $cookie,
            ]
        ]));

        $page = 1;
        do{
            $response = $client->get($getUrl."&toPage=".$page)->getBody()->getContents();
            $result = json_decode($response, true);
            if(json_last_error()){
                $this->error("访问受限");
                return;
            }
            $zoneList = $result['data']['pagelist'];
            $this->info("page:".$page. "  count:".count($zoneList));
            if(!count($zoneList)){
                break;
            }

            foreach ($zoneList as $zone){
                if($zone['siteid'] == $siteId){
                    try{
                        $result = (new CreatePid())->addPid($zone['name'], $zone['memberid'], $zone['siteid'], $zone['adzoneid']);
                        if(!$result){
                            throw new \Exception("已存在");
                        }
                        $this->info($zone['adzonePid']. "同步成功");
                    }catch (\Exception $e){
                        $this->error($zone['adzonePid']. "已存在");
                    }
                }
            }
            $page++;
        }while(true);

    }
    
}
