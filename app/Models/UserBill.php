<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * 用户账单
 * Class UserBill
 * @package App\Models
 */
class UserBill extends Model
{
    protected $table = "pytao_user_bill";
    protected $guarded = ['id'];
    public $timestamps = false;

}
