<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2018/2/7
 * Time: 11:51
 */

namespace App\Services;


use App\Helpers\QueryHelper;
use App\Models\User;
use App\Models\UserTree;
use Illuminate\Support\Facades\DB;

class TeamService
{
    /**
     * 团队成员列表
     * @param $userId
     * @param $level
     */
    public function userList($userId, $level){
        $user = UserTree::where("user_id", $userId)->first();

        $where = [
            ['left_val', ">", $user['left_val']],
            ['right_val', "<", $user['right_val']],
            ['level', "=", $user['level']+$level],
        ];

        $userGrade = new UserGradeService();

        //指定下级用户总数
        $totalUser = 0;
        //等级统计信息
        $userGradeArray = [];
        //等级统计
        $gradeInfo = UserTree::where($where)->select(DB::raw("grade, count(1) as num"))->orderBy("grade", "desc")->groupBy("grade")->get()->toArray();
        foreach ($gradeInfo as &$grade){
            $totalUser += $grade['num'];
            $grade['grade_str'] = $userGrade->getGrade($grade['grade'])['grade_name'];
            $userGradeArray[] = $grade['grade_str']."共计".$grade['num']."人";
        }
        //等级统计信息
        $userGradeStr = implode("，", $userGradeArray);

        //查询用户信息
        $query = User::query()->from((new User())->getTable()." as user")
            ->leftJoin((new UserTree())->getTable()." as tree", "user.id", "=", "tree.user_id")
            ->where($where)->select(["user.id", "user.mobile", "user.reg_time", "tree.grade"]);

        $teamUsers = (new QueryHelper())->pagination($query)->get()->toArray();

        foreach ($teamUsers as &$teamUser){
            $teamUser['mobile'] = substr_replace($teamUser['mobile'], "****", 3, 4);
            $teamUser['reg_time'] = substr($teamUser['reg_time'], 0, 10);
            $teamUser['grade_str'] = $userGrade->getGrade($teamUser['grade'])['grade_name'];
        }

        $data = [
            'total' => $totalUser,
            'user_grade' => $gradeInfo,
            'user_grade_str' => $userGradeStr,
            'user_list' => $teamUsers,
        ];

        return $data;
    }

    /**
     * 查询下级用户是否在上级用户的指定层级团队内
     * @param int $topUserId 上级用户id
     * @param int $subUserId 下级用户id
     * @param int $level 指定层级
     * @return bool
     */
    public function userInTeam($topUserId, $subUserId, $level=2){
        $user = UserTree::where("user_id", $topUserId)->first();

        return UserTree::where([
            ['left_val', ">", $user['left_val']],
            ['right_val', "<", $user['right_val']],
            ['level', "<=", $user['level']+$level],
            ['user_id', '=', $subUserId]
        ])->exists();
    }
}