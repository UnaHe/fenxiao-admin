<?php

namespace App\Listeners;

use App\Events\CalculateOrderEvent;
use App\Models\SystemPids;
use App\Models\UserOrderIncome;
use App\Models\UserTree;
use App\Services\UserGradeService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * 订单分成
 * Class CalculateOrderListener
 * @package App\Listeners
 */
class CalculateOrderListener implements ShouldQueue
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
     * @param  CalculateOrderEvent  $event
     * @return void
     */
    public function handle(CalculateOrderEvent $event)
    {
        $order = $event->order;
        $orderId = $order['id'];
        //订单用户id
        $orderUserId = SystemPids::where([
            'site_id' => $order['site_id'],
            'adzone_id' => $order['adzone_id'],
        ])->pluck("user_id")->first();

        if(!$orderUserId){
            Log::error("订单号: ".$order['order_no']."找不到对应用户");
            throw new \Exception("订单号: ".$order['order_no']."找不到对应用户");
        }

        //当前用户节点
        $treeNode = UserTree::where(['user_id' => $orderUserId])->first();
        //用户关系路径
        $userPath = UserTree::where([
            ['left_val', "<=", $treeNode['left_val']],
            ['right_val', ">=", $treeNode['right_val']],
            ['user_id', "!=", 0]
        ])->orderBy("id", "desc")->get()->toArray();

        //测试数据
        if(0){
            $orderUserId = 25;
            $userPath = [
                ['grade' => 1, 'level'=>1, 'user_id' => 1],
                ['grade' => 1, 'level'=>2, 'user_id' => 2],
                ['grade' => 1, 'level'=>3, 'user_id' => 3],
                ['grade' => 1, 'level'=>4, 'user_id' => 4],
                ['grade' => 1, 'level'=>5, 'user_id' => 5],
                ['grade' => 3, 'level'=>6, 'user_id' => 6],
                ['grade' => 1, 'level'=>7, 'user_id' => 7],
                ['grade' => 3, 'level'=>8, 'user_id' => 8],
                ['grade' => 3, 'level'=>9, 'user_id' => 9],
                ['grade' => 1, 'level'=>10, 'user_id' => 10],
                ['grade' => 1, 'level'=>11, 'user_id' => 11],
                ['grade' => 2, 'level'=>12, 'user_id' => 12],
                ['grade' => 1, 'level'=>13, 'user_id' => 13],
                ['grade' => 1, 'level'=>14, 'user_id' => 14],
                ['grade' => 1, 'level'=>15, 'user_id' => 15],
                ['grade' => 1, 'level'=>16, 'user_id' => 16],
                ['grade' => 1, 'level'=>17, 'user_id' => 17],
                ['grade' => 1, 'level'=>18, 'user_id' => 18],
                ['grade' => 1, 'level'=>19, 'user_id' => 19],
                ['grade' => 1, 'level'=>20, 'user_id' => 20],
                ['grade' => 1, 'level'=>21, 'user_id' => 21],
                ['grade' => 1, 'level'=>22, 'user_id' => 22],
                ['grade' => 1, 'level'=>23, 'user_id' => 23],
                ['grade' => 1, 'level'=>24, 'user_id' => 24],
                ['grade' => 1, 'level'=>25, 'user_id' => 25],
            ];
            $userPath = array_reverse($userPath);
        }

        $userGrade = new UserGradeService();

        //查找返利上级配置  1.等级依次递增查找  2.大于等于订单用户等级查找  3.不限上级等级
        $findParentConfig = 3;

        //订单用户
        $orderUser = $userPath[0];
        //上个返利用户
        $prevUser = $orderUser;
        //上个返利用户等级信息
        $prevUserGrade = $userGrade->getGrade($prevUser['grade']);
        //返利层级
        $level = 0;
        //平行返利层级
        $sameLevel = 1;
        $sameLevelInc = false;
        //所有已返利比例
        $allRate = 0;

        //最高等级
        $topGrade = $userGrade->getTopGrade();
        //第一个最高等级用户
        $firstTopGradeUser = null;

        //用户收入数组
        $userIncomeData = [];

        foreach ($userPath as $user){
            $sameLevelInc = false;
            //返利比例
            $rate = 0;
            //当前用户等级信息
            $grade = $userGrade->getGrade($user['grade']);

            //已找到最高等级用户，等级小于最高等级的用户全部过滤
            if($firstTopGradeUser && $grade['sort'] < $topGrade['sort']){
                echo "user:".$user['user_id']."\tgrade:".$user['grade']." 等级低于最高等级用户，过滤\n";
                continue;
            }

            //订单用户处理
            if($user['user_id'] == $orderUserId){
                $rate = $userGrade->getSelfRate($user['grade'], $level);
            }else{
                //递增查找上级
                if($findParentConfig == 1){
                    if(($grade['sort'] > $prevUserGrade['sort']) && ($prevUser['level'] - $user['level'] <= $prevUserGrade['find_parent_level'])){
                        $rate = $userGrade->getSelfRate($user['grade'], $level);
                        $sameLevel = 1;
                    }else if(($grade['sort'] == $prevUserGrade['sort']) && ($prevUser['level'] - $user['level'] <= $prevUserGrade['find_same_level'])){
                        $rate = $userGrade->getSameRate($user['grade'], $sameLevel);
                        if($rate){
                            $sameLevelInc = true;
                        }
                    }
                }

                //大于等于订单用户等级查找
                else if($findParentConfig == 2){
                    //查找等级大于等于订单用户
                    if($grade['sort'] >= $prevUserGrade['sort']){
                        //在指定范围内找到用户并且计算返利
                        if($prevUser['level'] - $user['level'] <= $prevUserGrade['find_parent_level']){
                            $rate = $userGrade->getSelfRate($user['grade'], $level);
                            if($rate){
                                $sameLevel = 1;
                            }
                        }

                        //用户无间推返利，计算平行奖励
                        if(!$rate && ($prevUser['level'] - $user['level'] <= $prevUserGrade['find_same_level'])){
                            $rate = $userGrade->getSameRate($user['grade'], $sameLevel);
                            if($rate){
                                $sameLevelInc = true;
                            }
                        }
                    }
                }

                //不限上级等级
                else if($findParentConfig == 3){
                    $rate = $userGrade->getSelfRate($user['grade'], $level);
                    if($rate){
                        $sameLevel = 1;
                    }

                    if(!$rate && $grade['sort'] == $prevUserGrade['sort']){
                        $rate = $userGrade->getSameRate($user['grade'], $sameLevel);
                        if($rate){
                            $sameLevelInc = true;
                        }
                    }
                }

            }

            echo "user:".$user['user_id']."\tgrade:".$user['grade']."\trate:".$rate."\tlevel:".$level."\tsamelevel:".$sameLevel."\n";

            $user['rate'] = $rate;
            if($rate){
                $userIncomeData[$user['user_id']] = [
                    'order_id' => $orderId,
                    'order_user_id' => $orderUserId,
                    'user_id' => $user['user_id'],
                    'share_rate' => $rate
                ];

                if($sameLevelInc){
                    $sameLevel++;
                }else{
                    $level++;
                    $sameLevel = 1;
                }

                $prevUser = $user;
                $prevUserGrade = $grade;

                $allRate += $rate;
            }

            //确定最高等级用户
            if($topGrade['grade'] == $user['grade'] && !$firstTopGradeUser){
                $firstTopGradeUser = $user;
                //最高等级用户有返利
                $prevUser = $user;
                $prevUserGrade = $grade;
            }
        }

        //将剩余未分配的返利全部加到第一个最高等级用户
        if($firstTopGradeUser){
            $topUserRate = (1 - $allRate) + $firstTopGradeUser['rate'];
            $userIncomeData[$firstTopGradeUser['user_id']] = [
                'order_id' => $orderId,
                'order_user_id' => $orderUserId,
                'user_id' => $firstTopGradeUser['user_id'],
                'share_rate' => $topUserRate
            ];

            echo "最高等级用户：{$firstTopGradeUser['user_id']} 返利:{$topUserRate}\n";
        }

        //写入数据
        UserOrderIncome::insert($userIncomeData);

    }
}
