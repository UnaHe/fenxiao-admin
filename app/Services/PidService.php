<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/18
 * Time: 15:51
 */
namespace App\Services;

use App\Helpers\QueryHelper;
use App\Models\SystemPids;
use App\Models\User;

class PidService
{
    /**
     * PID列表
     * @param \Illuminate\Http\Request $request
     */
    public function pidList($request){

        $query = SystemPids::query()->from((new SystemPids())->getTable()." as pid")
            ->leftJoin((new User())->getTable()." as user", "user.id", "=", "pid.user_id")
            ->select(['pid.*', 'user.mobile']);

        //搜索手机号
        if($mobile = trim($request->get('mobile'))){
            $query->where("user.mobile", '=' , $mobile);
        }

        //搜索PID
        if($pid = trim($request->get('pid'))){
            $query->where("pid.pid", '=' , $pid);
        }

        //联盟id
        if($memberId = trim($request->get('member_id'))){
            $query->where("pid.member_id", '=' , $memberId);
        }

        //网站id
        if($siteId = trim($request->get('site_id'))){
            $query->where("pid.site_id", '=' , $siteId);
        }

        $query->orderBy("id", "desc");

        //分页数据
        $data  = (new QueryHelper())->pagination($query);

        return $data;
    }

    /**
     * PID统计
     */
    public function pidStatistics(){
        //未使用PID数量
        $notUsed = SystemPids::where("user_id", 0)->count();
        //PID总数
        $total = SystemPids::count();

        $data = [
            'total' => $total,
            'not_used' => $notUsed,
            'used' => $total-$notUsed,
        ];

        return $data;
    }


}
