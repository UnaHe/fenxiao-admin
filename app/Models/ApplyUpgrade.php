<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 直升等级申请
 * Class ApplyUpgrade
 * @package App\Models
 */
class ApplyUpgrade extends Model
{
    protected $table = "pytao_apply_upgrade";
    protected $guarded = ['id'];
    public $timestamps = false;


    /**
     * 申请中
     */
    const STATUS_APPLY = 0;

    /**
     * 已升级
     */
    const STATUS_SUCCESS = 1;

    /**
     * 拒绝
     */
    const STATUS_REFUSE = 2;

    /**
     * 处理状态
     * @var array
     */
    static $STATUS = [
        self::STATUS_APPLY => "未处理",
        self::STATUS_SUCCESS => "已升级",
        self::STATUS_REFUSE => '已拒绝',
    ];

}