<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 用户登录token
 * Class UserLoginToken
 * @package App\Models
 */
class UserLoginToken extends Model
{
    protected $table = "pytao_user_login_token";
    protected $guarded = ['id'];
    public $timestamps = false;
}
