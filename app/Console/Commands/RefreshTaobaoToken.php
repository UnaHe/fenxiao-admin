<?php

namespace App\Console\Commands;

use App\Models\TaobaoToken;
use App\Services\TaobaoService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RefreshTaobaoToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh_taobao_token {--all=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '刷新淘宝授权token';

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
        //是否刷新所有token
        $refreshAll = $this->option('all');
        $where = [
            ['re_expires_at', ">", Carbon::now()],
        ];

        if(!$refreshAll){
            $where[] = ['expires_at', "<=", Carbon::now()->addMinute(15)];
        }

        $expiredUserIds = TaobaoToken::where($where)->pluck("member_id");

        foreach ($expiredUserIds as $userId){
            $ret = (new TaobaoService())->refreshUserToken($userId);
            if(!$ret){
                $this->error("{$userId}更新失败");
            }else{
                $this->info("{$userId}更新成功");
            }
        }
    }
}
