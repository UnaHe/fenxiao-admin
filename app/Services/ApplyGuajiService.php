<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/31
 * Time: 17:36
 */

namespace App\Services;


use App\Helpers\QueryHelper;
use App\Models\ApplyGuaji;
use App\Models\User;
use App\Models\UserTree;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ApplyGuajiService
{
    /**
     * 列表
     * @param \Illuminate\Http\Request $request
     */
    public function getList($request){

        $query = ApplyGuaji::query();

        //手机号
        if($mobile = trim($request->get('mobile'))){
            $query->where("mobile", '=' , $mobile);
        }
        //状态
        $status = trim($request->get('status'));
        if($status !== ''){
            $query->where("status", '=' , $status);
        }

        //开始时间
        if($startTime = trim($request->get('start_time'))){
            $query->where("add_time", '>=' , $startTime);
        }

        //结束时间
        if($endTime = trim($request->get('end_time'))){
            $query->where("add_time", '<=' , $endTime);
        }


        $query->orderBy("add_time", "desc");

        //分页数据
        $data  = (new QueryHelper())->pagination($query);

        foreach ($data['data'] as &$item){
            $item['status_str'] = $this->getStatus($item['status']);
        }
        return $data;
    }

    /**
     * 申请信息
     * @param $userId
     * @param $id
     */
    public function detail($id){
        $model = ApplyGuaji::find($id);
        if(!$model){
            throw new \Exception("申请不存在");
        }

        $model['status_str'] = $this->getStatus($model['status']);

        $data = [
            'apply' => $model,
        ];

        return $data;
    }

    /**
     * 处理状态
     * @param $status
     * @return string
     */
    public function getStatus($status){
        return isset(ApplyGuaji::$STATUS[$status]) ? ApplyGuaji::$STATUS[$status] : "";
    }

    /**
     * 同意申请
     * @param $id
     */
    public function confirm($id){
        $model = ApplyGuaji::find($id);
        if(!$model){
            throw new \Exception("申请不存在");
        }

        //需要续费的用户id
        $userId = User::where("mobile", $model['mobile'])->pluck("id")->first();
        if(!$userId){
            throw new \Exception("用户不存在");
        }

        try{
            DB::beginTransaction();
            if(!ApplyGuaji::where([
                ['id', '=', $id],
                ['status', '=', ApplyGuaji::STATUS_APPLY]
            ])->update(['status'=> ApplyGuaji::STATUS_SUCCESS, 'deal_time'=> Carbon::now()])){
                throw new \Exception("更新状态失败");
            }

            (new MessageService())->sendMessageToUser($model['user_id'], "恭喜您，您的挂机申请已通过！");

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return false;
        }
        return true;
    }


    /**
     * 拒绝申请
     * @param $id
     */
    public function refuse($id){
        $model = ApplyGuaji::find($id);
        if(!$model){
            throw new \Exception("申请不存在");
        }

        if(!ApplyGuaji::where([
            ['id', '=', $id],
            ['status', '=', ApplyGuaji::STATUS_APPLY]
        ])->update(['status'=> ApplyGuaji::STATUS_REFUSE, 'deal_time'=> Carbon::now()])){
            return false;
        }

        (new MessageService())->sendMessageToUser($model['user_id'], "很抱歉，您的挂机申请未通过！");

        return true;
    }

}