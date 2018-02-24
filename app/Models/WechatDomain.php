<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 微信域名
 * Class WechatDomain
 * @package App\Models
 */
class WechatDomain extends Model
{
    protected $table = "pytao_wechat_domain";
    protected $guarded = ['id'];
    public $timestamps = false;

    public static $domainType = [
        1 => '直接打开',
        2 => '快站方式'
    ];

}
