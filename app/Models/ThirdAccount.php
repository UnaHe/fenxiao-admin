<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 第三方账号
 * Class ThirdAccount
 * @package App\Models
 */
class ThirdAccount extends Model
{
    protected $table = "pytao_user_third_account";
    protected $guarded = ['id'];
    public $timestamps = false;

}
