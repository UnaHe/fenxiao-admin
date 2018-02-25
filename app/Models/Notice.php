<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 公告表
 * Class Notice
 * @package App\Models
 */
class Notice extends Model
{
    protected $table = "pytao_notice";
    protected $guarded = ['id'];
    public $timestamps = false;

}
