<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/31
 * Time: 17:36
 */

namespace App\Services;


use App\Helpers\QueryHelper;
use App\Models\FailedJobs;
use App\Models\TaobaoToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class FailedJobsService
{
    /**
     * 列表
     * @param \Illuminate\Http\Request $request
     */
    public function jobList($request){
        $query = FailedJobs::query();
        $query->orderBy("id", "desc");

        //分页数据
        $data  = (new QueryHelper())->pagination($query);
        foreach ($data['data'] as &$item){
            $item['displayName'] = json_decode($item['payload'], true)['displayName'];
            preg_match("/Exception:(.*?)in/", $item['exception'], $match);
            $item['exception_msg'] = $match[1];
        }
        return $data;
    }

    /**
     * 重试任务
     * @param $id
     */
    public function retry($id){
        $exitCode = Artisan::call('queue:retry', ["id" => $id]);
        return $exitCode;
    }

}