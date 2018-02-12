<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/31
 * Time: 11:06
 */

namespace App\Services;


use App\Models\Grade;
use App\Models\UserTree;

class CommissionService
{
    private $userRate = 0;
    public function __construct($userId){
        if($userId){
            $this->userRate = $this->getUserCommissionRate($userId);
        }
    }


    /**
     * 计算商品列表用户返利信息
     * @param $goodsList
     * @param $userId
     * @return mixed
     */
    public function goodsCommisstion($goods){
        //券后价
        $usedPrice = bcsub($goods['price_full'], $goods['coupon_price'], 2);
        if($usedPrice < 0){
            $usedPrice = $goods['price_full'];
        }

        //所有佣金金额
        $commissionAmountFull = bcmul($usedPrice, $goods['commission']/100, 5);
        //用户实际佣金金额
        $commissionAmountUser = (new UserService())->getUserMoney($commissionAmountFull, $this->userRate);
        $commissionAmountUser = intval($commissionAmountUser*100)/100;

        return $commissionAmountUser;
    }


    /**
     * 获取用户返利比例
     * @param $userId
     */
    public function getUserCommissionRate($userId){
        $grade = UserTree::where("user_id", $userId)->pluck("grade")->first();
        $selfRates = Grade::where("grade", $grade)->pluck("rate")->first();
        $selfRates = explode(";", $selfRates);
        $userRate = [];
        foreach ($selfRates as $rate){
            $rateInfo = explode(":", $rate);
            if(count($rateInfo)==2){
                $userRate[$rateInfo[0]] = $rateInfo[1];
            }
        }

        return $userRate[0];
    }
}