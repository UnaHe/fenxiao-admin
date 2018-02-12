<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 系统消息表
 * Class Message
 * @package App\Models
 */
class Message extends Model
{
    protected $table = "xmt_message";
    protected $guarded = ['id'];

    /**
     * 消息类型:广播
     */
    CONST MSG_TYPE_BROADCAST = 1;

    /**
     * 消息类型:私信
     */
    CONST MSG_TYPE_PRIVATE = 2;

}
