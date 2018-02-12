<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 邀请码
 * Class InviteCode
 * @package App\Models
 */
class InviteCode extends Model
{
    protected $table = "xmt_user_invitecode";
    protected $guarded = ['id'];
    public $timestamps = false;

    /**
     * 未使用
     */
    const STATUS_UNUSE = 1;

    /**
     * 已使用
     */
    const STATUS_USED = 2;

    /**
     * 邀请码是否可用
     * @param $code
     * @return mixed
     */
    public function checkUsable($code){
        return $this->where("invite_code", $code)->where("status", self::STATUS_UNUSE)->first();
    }

    /**
     * 使用邀请码
     * @param $code
     * @return mixed
     */
    public function useCode($code){
        return $this->where("invite_code", $code)->where("status", self::STATUS_UNUSE)->update(["status"=>self::STATUS_USED]);
    }

}
