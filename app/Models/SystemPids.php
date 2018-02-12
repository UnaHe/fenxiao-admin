<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 系统pid信息
 * Class SystemPids
 * @package App\Models
 */
class SystemPids extends Model
{
    protected $table = "pytao_system_taobao_pids";
    protected $guarded = ['id'];
    public $timestamps = false;

}
