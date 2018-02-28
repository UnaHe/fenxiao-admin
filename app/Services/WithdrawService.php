<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/31
 * Time: 17:36
 */

namespace App\Services;


use App\Helpers\QueryHelper;
use App\Models\ThirdAccount;
use App\Models\User;
use App\Models\UserBill;
use App\Models\Withdraw;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WithdrawService
{
    /**
     * 列表
     * @param \Illuminate\Http\Request $request
     */
    public function getList($request){

        $query = Withdraw::query()->from((new Withdraw())->getTable()." as withdraw")
            ->leftJoin((new User())->getTable()." as user", "user.id", "=", "withdraw.user_id")
            ->select([
                "withdraw.*",
                "user.mobile"
            ]);

        //手机号
        if($mobile = trim($request->get('mobile'))){
            $query->where("user.mobile", '=' , $mobile);
        }
        //状态
        $status = trim($request->get('status'));
        if($status !== ''){
            $query->where("withdraw.status", '=' , $status);
        }

        //开始时间
        if($startTime = trim($request->get('start_time'))){
            $query->where("withdraw.add_time", '>=' , $startTime);
        }

        //结束时间
        if($endTime = trim($request->get('end_time'))){
            $query->where("withdraw.add_time", '<=' , $endTime);
        }


        $query->orderBy("add_time", "desc");

        //分页数据
        $data  = (new QueryHelper())->pagination($query);

        foreach ($data['data'] as &$item){
            $item['status_str'] = $this->getWithdrawStatus($item['status']);
        }
        return $data;
    }

    /**
     * 提现详情信息
     * @param $userId
     * @param $id
     */
    public function detail($id){
        $withDraw = Withdraw::find($id);
        if(!$withDraw){
            throw new \Exception("提现申请不存在");
        }

        $user = User::where("id", $withDraw['user_id'])->select(['id', 'mobile'])->first();
        $thirdAccount = ThirdAccount::where(['user_id' => $withDraw['user_id']])->first();

        $withDraw['status_str'] = $this->getWithdrawStatus($withDraw['status']);
        $data = [
            'user' => $user,
            'withdraw' => $withDraw,
            'pay_account' => $thirdAccount
        ];

        return $data;
    }

    /**
     * 提现状态
     * @param $status
     * @return string
     */
    public function getWithdrawStatus($status){
        return isset(Withdraw::$STATUS[$status]) ? Withdraw::$STATUS[$status] : "";
    }

    /**
     * 同意提现申请
     * @param $id
     */
    public function confirm($id){
        if(Withdraw::where([
            ['id', '=', $id],
            ['status', '=', Withdraw::STATUS_APPLY]
        ])->update(['status'=> Withdraw::STATUS_SUCCESS, 'deal_time'=> Carbon::now()])){
            return true;
        }
        return false;
    }

    /**
     * 拒绝提现申请
     * @param $id
     */
    public function refuse($id){
        $withdraw = Withdraw::find($id);

        try{
            DB::beginTransaction();
            if(!Withdraw::where([
                ['id', '=', $id],
                ['status', '=', Withdraw::STATUS_APPLY]
            ])->update(['status'=> Withdraw::STATUS_REFUSE, 'deal_time'=> Carbon::now()])){
                throw new \Exception("更新提现状态失败");
            }

            if(!(new UserService())->addBalance($withdraw['user_id'], $withdraw['amount'], "提现拒绝")){
                throw new \Exception("增加用户余额失败");
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return false;
        }
        return true;
    }

    /**
     * 未处理提现数量
     */
    public function unDealNum(){
        return Withdraw::where('status', Withdraw::STATUS_APPLY)->count();
    }
}