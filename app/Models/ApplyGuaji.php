<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 挂机续费申请
 * Class ApplyGuaji
 * @package App\Models
 */
class ApplyGuaji extends Model
{
    protected $table = "pytao_apply_guaji";
    protected $guarded = ['id'];
    public $timestamps = false;


    /**
     * 申请中
     */
    const STATUS_APPLY = 0;

    /**
     * 已续费
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
        self::STATUS_SUCCESS => "已续费",
        self::STATUS_REFUSE => '已拒绝',
    ];

}