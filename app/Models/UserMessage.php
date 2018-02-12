<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 用户消息状态表
 * Class UserMessage
 * @package App\Models
 */
class UserMessage extends Model
{
    protected $table = "xmt_user_message";
    protected $guarded = ['id'];
    public $timestamps = false;
}
