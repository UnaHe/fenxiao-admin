<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * 等级配置表
 * Class Grade
 * @package App\Models
 */
class Grade extends Model
{
    protected $table = "pytao_grade";
    protected $guarded = ['id'];
    public $timestamps = false;

}
