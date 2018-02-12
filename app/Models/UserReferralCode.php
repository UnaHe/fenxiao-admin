<?php

namespace App\Models;

use App\Helpers\BaseConvert;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * 用户推荐码
 * Class UserReferralCode
 * @package App\Models
 */
class UserReferralCode extends Model
{
    protected $table = "pytao_user_referral_code";
    protected $guarded = ['id'];
    public $timestamps = false;

    /**
     * 创建邀请码
     * @param $userId
     */
    public function createCode($userId){
        $nowTime = Carbon::now();

        //创建用户邀请码
        $codeNum = mt_rand(1, 9).str_pad($userId, 9, '0',STR_PAD_LEFT);
        $referralCode = BaseConvert::dec2str($codeNum, 62);
        return static::create([
            'user_id' => $userId,
            'referral_code' => $referralCode,
            'add_time' => $nowTime,
            'update_time' => $nowTime,
        ]);
    }

    /**
     * 查询推荐码用户id
     * @param $code
     * @return mixed
     */
    public function getByCode($code){
        return $this->where("referral_code", $code)->first();
    }

    /**
     * 获取推荐码
     * @param $userId
     * @return mixed
     */
    public function getByUserId($userId){
        return $this->where("user_id", $userId)->first();
    }

    /**
     * 更新用户推荐码
     * @param $code
     * @param $userId
     * @return mixed
     */
    public function updateCode($code, $userId){
        $now = Carbon::now();
        $data = [
            'user_id' => $userId,
            'referral_code' => $code,
            'update_time' => $now,
        ];
        $model = $this->getByUserId($userId);
        try{
            if(!$model){
                $data['add_time'] = $now;
                static::create($data);
            }else{
                $this->where("id", $model['id'])->update($data);
            }
        }catch (\Exception $e){
            return false;
        }
        return true;
    }
}
