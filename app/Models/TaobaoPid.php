<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 淘宝pid
 * Class TaobaoPid
 * @package App\Models
 */
class TaobaoPid extends Model
{
    protected $table = "xmt_taobao_pid";
    protected $guarded = ['id'];
    public $timestamps = false;
}
