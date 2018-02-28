<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/18
 * Time: 15:51
 */
namespace App\Services;

use App\Events\RegisterUserEvent;
use App\Helpers\BaseConvert;
use App\Helpers\QueryHelper;
use App\Models\SystemPids;
use App\Models\UserBill;
use App\Models\UserLoginToken;
use App\Models\UserReferralCode;
use App\Models\UserTree;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\InviteCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class UserService
{
    /**
     * 用户列表
     * @param \Illuminate\Http\Request $request
     */
    public function userList($request){

        $query = User::query()->from((new User())->getTable()." as user")
            ->leftJoin((new UserTree())->getTable()." as tree", "user.id", "=", "tree.user_id")
            ->select(['user.id', 'user.mobile', 'user.balance', 'user.reg_time', 'tree.grade']);

        //搜索手机号
        if($mobile = trim($request->get('mobile'))){
            $query->where("user.mobile", 'like' , "%".$mobile."%");
        }
        //搜索等级
        if($grade = $request->get('grade')){
            $query->where("tree.grade", '=' , $grade);
        }

        //注册时间
        if($regTimeStart = $request->get('reg_time_start')){
            $query->where("user.reg_time", '>=' , $regTimeStart);
        }
        if($regTimeEnd = $request->get('reg_time_end')){
            $query->where("user.reg_time", '<=' , $regTimeEnd);
        }

        //搜索PID
        if($pid = trim($request->get('pid'))){
            $userId = SystemPids::where("pid", $pid)->first()['user_id'];
            $query->where("user.id", '=' , $userId);
        }

        //搜索邀请码
        if($inviteCode = trim($request->get('invite_code'))){
            $userId = (new UserReferralCode())->getByCode($inviteCode)['user_id'];
            $query->where("user.id", '=' , $userId);
        }

        $query->orderBy("id", "desc");

        //分页数据
        $data  = (new QueryHelper())->pagination($query);
        $gradeService = new UserGradeService();
        foreach ($data['data'] as &$item){
            $item['grade_str'] = $gradeService->getGrade($item['grade'])['grade_name'];
        }

        return $data;
    }

    /**
     * 获取用户基本信息
     * @param $userId
     */
    public function detail($userId){
        $user = User::query()->from((new User())->getTable()." as user")
            ->leftJoin((new UserTree())->getTable().' as tree', 'user.id', '=', 'tree.user_id')
            ->select([
                'user.*',
                'tree.grade'
            ])
            ->where("user.id", $userId)->first();

        $grade = $user['grade'];
        $gradeInfo = (new UserGradeService())->getGrade($grade);
        $user['grade_str'] = $gradeInfo['grade_name'];
        $user['pid'] = SystemPids::where("user_id", $userId)->first()['pid'];
        $user['referral_code'] = (new UserReferralCode())->getByUserId($userId)['referral_code'];

        $data = [
            'user' => $user
        ];
        return $data;
    }

    /**
     * 保存用户信息
     * @param \Illuminate\Http\Request $request
     */
    public function save($request){
        $userId = $request->post('id');
        $mobile = $request->post('mobile');
        $grade = $request->post('grade');
        $balance = $request->post('balance');
        $isForbid = $request->post('is_forbid');
        $forbidReason = $request->post('forbid_reason');
        $password = $request->post('password');

        $user = User::find($userId);
        if(!$user){
            throw new \Exception("用户不存在");
        }
        if($mobile){
            $user['mobile'] = $mobile;
        }
        if($balance){
            $user['balance'] = $balance;
        }
        if($isForbid){
            $user['is_forbid'] = 1;
            $user['forbid_reason'] = $forbidReason;
        }else{
            $user['is_forbid'] = 0;
            $user['forbid_reason'] = "";
        }

        if($password){
            $user['password'] = bcrypt($password);
        }

        try{
            DB::beginTransaction();
            if(!$user->save()){
                throw new \Exception("保存失败");
            }

            //修改用户等级
            if($grade){
                UserTree::where("user_id", $userId)->update(['grade'=>$grade]);
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            throw $e;
            return false;
        }

        return true;
    }

    /**
     * 用户统计
     */
    public function userStatistics(){
        //今日注册用户数
        $today = User::where([
            ['reg_time', ">=", Carbon::now()->startOfDay()],
        ])->count();
        //昨日注册用户数
        $yesterday = User::where([
            ['reg_time', ">=", Carbon::yesterday()->startOfDay()],
            ['reg_time', "<=", Carbon::yesterday()->endOfDay()],
        ])->count();
        //本周注册用户数
        $curWeek = User::where([
            ['reg_time', ">=", Carbon::now()->startOfWeek()->startOfDay()],
        ])->count();
        //本月注册用户数
        $curMonth = User::where([
            ['reg_time', ">=", Carbon::now()->startOfMonth()->startOfDay()],
        ])->count();
        //用户总数
        $total = User::count();

        $data = [
            'today' => $today,
            'yesterday' => $yesterday,
            'cur_week' => $curWeek,
            'cur_month' => $curMonth,
            'total' => $total,
        ];

        return $data;
    }

    /**
     * 用户关系
     * @param $userId
     */
    public function tree($userId, $type){
        $user = UserTree::where("user_id", $userId)->first();

        if($type == 'parent'){
            $where = [
                ['left_val', "<=", $user['left_val']],
                ['right_val', ">=", $user['right_val']],
                ['user_id', "!=", 0],
            ];
        }else{
            $where = [
                ['left_val', ">=", $user['left_val']],
                ['right_val', "<=", $user['right_val']],
//            ['level', "=", $user['level']+$level],
            ];
        }

        $userGrade = new UserGradeService();

        //等级统计信息
        $userGradeArray = [];
        //等级统计
        $gradeInfo = UserTree::where($where)->select(DB::raw("grade, count(1) as num"))->orderBy("grade", "desc")->groupBy("grade")->get()->toArray();
        foreach ($gradeInfo as &$grade){
            $grade['grade_str'] = $userGrade->getGrade($grade['grade'])['grade_name'];
            $userGradeArray[] = $grade['grade_str']."共计".$grade['num']."人";
        }
        //等级统计信息
        $userGradeStr = implode("，", $userGradeArray);

        //查询用户信息
        $query = User::query()->from((new User())->getTable()." as user")
            ->leftJoin((new UserTree())->getTable()." as tree", "user.id", "=", "tree.user_id")
            ->where($where)->select(["user.id", "user.mobile", "tree.grade", 'tree.parent_id as pId']);

        $teamUsers = $query->get()->toArray();

        foreach ($teamUsers as &$teamUser){
            $teamUser['grade_str'] = $userGrade->getGrade($teamUser['grade'])['grade_name'];
            $teamUser['name'] = $teamUser['mobile']." 等级:".$teamUser['grade_str'];
            $teamUser['open'] = true;
        }

        $data = [
            'user_grade_str' => $userGradeStr,
            'user_list' => $teamUsers,
        ];

        return $data;
    }

    /**
     * 计算用户分成金额
     * @param $money
     * @param $rate
     */
    public function getUserMoney($money, $rate){
        //系统扣款比例
        $systemRate = 0.16;
        //预估收入 = (订单预估 - 系统扣减手续费) * 用户分成比例
        return bcmul(bcmul($money, (1 - $systemRate), 5), $rate, 5);
    }

    /**
     * 增加用户余额
     * @param int $userId 用户id
     * @param float $amount 金额
     * @param string $comment 备注
     * @return bool
     */
    public function addBalance($userId, $amount, $comment){
        DB::beginTransaction();
        try{
            if(!User::where("id", $userId)->increment("balance", $amount)){
                throw new \Exception("更新用户余额失败");
            }
            UserBill::create([
                'user_id' => $userId,
                'amount' => $amount,
                'comment' => $comment,
                'type' => 1,
                'add_time' => Carbon::now(),
            ]);
        }catch (\Exception $e){
            DB::rollBack();
            return false;
        }

        DB::commit();
        return true;
    }


}
