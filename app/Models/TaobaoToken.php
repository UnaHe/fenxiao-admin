<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * 淘宝token
 * Class TaobaoToken
 * @package App\Models
 */
class TaobaoToken extends Model
{
    protected $table = "pytao_system_taobao_token";
    protected $guarded = ['id'];
    public $timestamps = false;
}
