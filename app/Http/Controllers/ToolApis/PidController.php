<?php

namespace App\Http\Controllers\ToolApis;

use App\Http\Controllers\Controller;
use App\Models\SystemPids;
use App\Services\PidService;
use App\Services\TaobaoTokenService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PidController extends Controller
{
    /**
     * 获取pid信息
     * @param Request $request
     * @return mixed
     */
    public function getInfo(Request $request){
        $memberId = $request->get('member_id');

        $data = (new PidService())->pidStatistics($memberId);
        if(!$data){
            return $this->ajaxError("获取pid使用信息失败");
        }
        return $this->ajaxSuccess($data);
    }

    /**
     * 保存pid
     * @param Request $request
     */
    public function save(Request $request){
        $data = $request->post("data");
        $data = str_replace(" ", "+", $data);
        $data = gzdecode(base64_decode($data));
        $data = json_decode($data, true);

        $memberId = $data['memberId'];
        $siteId = $data['siteId'];
        $adzoneId = $data['adzoneId'];
        $adzoneName = $data['adzoneName'];

        $pid = "mm_{$memberId}_{$siteId}_{$adzoneId}";
        $data = [
            'name' => $adzoneName,
            'member_id' => $memberId,
            'site_id' => $siteId,
            'adzone_id' => $adzoneId,
            'pid' => $pid,
            'add_time' => Carbon::now(),
        ];

        if(!SystemPids::create($data)){
            return $this->ajaxError("创建pid失败");
        }

        return $this->ajaxSuccess();
    }

}
