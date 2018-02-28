<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/31
 * Time: 17:36
 */

namespace App\Services;


use App\Helpers\QueryHelper;
use App\Models\ApplyUpgrade;
use App\Models\User;
use App\Models\UserTree;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ApplyUpgradeService
{
    /**
     * 列表
     * @param \Illuminate\Http\Request $request
     */
    public function getList($request){

        $query = ApplyUpgrade::query();

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

        $userGrade = (new UserGradeService());
        foreach ($data['data'] as &$item){
            $item['status_str'] = $this->getStatus($item['status']);
            $item['grade_str'] = $userGrade->getGrade($item['grade'])['grade_name'];
        }
        return $data;
    }

    /**
     * 申请信息
     * @param $userId
     * @param $id
     */
    public function detail($id){
        $model = ApplyUpgrade::find($id);
        if(!$model){
            throw new \Exception("申请不存在");
        }

        $userGrade = (new UserGradeService());
        $model['status_str'] = $this->getStatus($model['status']);
        $model['grade_str'] = $userGrade->getGrade($model['grade'])['grade_name'];

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
        return isset(ApplyUpgrade::$STATUS[$status]) ? ApplyUpgrade::$STATUS[$status] : "";
    }

    /**
     * 同意申请
     * @param $id
     */
    public function confirm($id){
        $model = ApplyUpgrade::find($id);
        if(!$model){
            throw new \Exception("申请不存在");
        }

        //需要升级的等级
        $upgradeGrade = $model['grade'];

        //需要升级的用户id
        $userId = User::where("mobile", $model['mobile'])->pluck("id")->first();
        if(!$userId){
            throw new \Exception("用户不存在");
        }

        $userInfo = UserTree::where("user_id", $userId)->first();
        $userGradeService = new UserGradeService();
        //当前用户等级信息
        $curGradeInfo = $userGradeService->getGrade($userInfo['grade']);
        //需要升级的等级信息
        $upgradeGradeInfo = $userGradeService->getGrade($upgradeGrade);

        //判断等级关系
        if($curGradeInfo['sort'] >= $upgradeGradeInfo['sort']){
            throw new \Exception("用户无需升级");
        }

        try{
            DB::beginTransaction();
            if(!ApplyUpgrade::where([
                ['id', '=', $id],
                ['status', '=', ApplyUpgrade::STATUS_APPLY]
            ])->update(['status'=> ApplyUpgrade::STATUS_SUCCESS, 'deal_time'=> Carbon::now()])){
                throw new \Exception("更新状态失败");
            }

            if(!UserTree::where("user_id", $userId)->update(['grade'=>$upgradeGrade])){
                throw new \Exception("更新用户等级失败");
            }

            (new MessageService())->sendMessageToUser($model['user_id'], "恭喜您，您的账号直升申请已通过！");

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
        $model = ApplyUpgrade::find($id);
        if(!$model){
            throw new \Exception("申请不存在");
        }

        if(!ApplyUpgrade::where([
            ['id', '=', $id],
            ['status', '=', ApplyUpgrade::STATUS_APPLY]
        ])->update(['status'=> ApplyUpgrade::STATUS_REFUSE, 'deal_time'=> Carbon::now()])){
            return false;
        }

        (new MessageService())->sendMessageToUser($model['user_id'], "很抱歉，您的账号直升申请未通过！");

        return true;
    }

    /**
     * 未处理数量
     * @return mixed
     */
    public function unDealNum(){
        return ApplyUpgrade::where('status', ApplyUpgrade::STATUS_APPLY)->count();
    }


}