<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 微信中转单页
 * Class WechatPage
 * @package App\Models
 */
class WechatPage extends Model
{
    protected $table = "pytao_wechat_page";
    protected $guarded = [];
    public $timestamps = false;
}
