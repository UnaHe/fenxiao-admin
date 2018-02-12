<?php

namespace App\Listeners;

use App\Events\RegisterUserEvent;
use App\Models\Grade;
use App\Models\User;
use App\Models\UserTree;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

/**
 * 用户升级程序
 * Class UserUpgradeListener
 * @package App\Listeners
 */
class UserUpgradeListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RegisterUserEvent  $event
     * @return void
     */
    public function handle(RegisterUserEvent $event)
    {
        $user = UserTree::where('user_id', $event->getUser()->id)->first();

        //用户关系路径
        $userPath = UserTree::where([
            ['left_val', "<", $user['left_val']],
            ['right_val', ">", $user['right_val']],
        ])->orderBy("id", "desc")->get();

        //获取等级配置
        $gradeModels = Grade::orderBy("sort", "desc")->get();
        $grades = [];
        foreach ($gradeModels as $gradeModel){
            $grades[$gradeModel['id']] = $gradeModel;
        }

        foreach ($userPath as $nextUser){
            $nextUserId = $nextUser['user_id'];
            $nextUserGrade = $nextUser['grade'];
            $nextUserGradeSort = $grades[$nextUserGrade]['sort'];

            $this->log("-------------------------");
            $this->log("当前检测用户：".$nextUserId);

            //过滤user_id为0的初始化用户
            if($nextUserId == 0){
                $this->log("初始化用户，已过滤");
                continue;
            }

            //查找用户直属下级等级状态
            $childUsers = UserTree::where("parent_id", $nextUserId)->groupBy("grade")->select(DB::raw("grade, count(1) as num"))->get()->toArray();
            //直属下级等级数量数组
            $childUserGrades = [];
            //直属下级等级数量整合(高等级下级数量包含低等级数量)
            $childUserGradesAll = [];
            foreach ($childUsers as $childUser){
                $gradeId = $childUser['grade'];
                $gradeNum = $childUser['num'];
                $gradeSort = $grades[$gradeId]['sort'];
                $childUserGrades[$gradeId] = $gradeNum;

                foreach ($grades as $grade) {
                    if($gradeSort >= $grade['sort']){
                        if(!isset($childUserGradesAll[$grade['grade']])){
                            $childUserGradesAll[$grade['grade']] = 0;
                        }
                        $childUserGradesAll[$grade['grade']] += $gradeNum;
                    }
                }
            }


            //新账号等级
            $newGrade = $nextUserGrade;
            //查找下级账号等级是否满足升级条件
            foreach ($grades as $grade){
                $this->log("--检查等级: ".$grade['grade']);
                $childGrade = $grade['child_grade'];
                if(!isset($childUserGradesAll[$childGrade])){
                    $this->log("----下级无此等级");
                    continue;
                }

                $this->log("----升级条件:".$grade['child_grade_num']." 实际：".$childUserGradesAll[$childGrade]);

                if($childUserGradesAll[$childGrade] >= $grade['child_grade_num']){
                    if($grade['sort'] < $nextUserGradeSort){
                        $this->log("----不能降低用户等级");
                    }else{
                        $this->log("----等级确定: {$grade['grade']}");
                        $newGrade = $grade['grade'];
                    }
                    break;
                }else{
                    $this->log("----该等级下级数量少于升级条件 {$childUserGradesAll[$childGrade]}<{$grade['child_grade_num']}");
                }
            }

            if($newGrade != $nextUserGrade){
                UserTree::where(['user_id' => $nextUserId])->update(['grade' => $newGrade]);
                $this->log("--升级:".$newGrade);
            }else{
                $this->log("--无需升级");
                break;
            }
        }
    }


    public function log($msg){
        echo $msg."\n";
    }
}
