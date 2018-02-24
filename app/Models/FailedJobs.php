<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * 失败任务
 * Class FailedJobs
 * @package App\Models
 */
class FailedJobs extends Model
{
    protected $table = "failed_jobs";
    protected $guarded = ['id'];
    public $timestamps = false;

}
