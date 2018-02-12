<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 用户订单收入表
 * Class UserOrderIncome
 * @package App\Models
 */
class UserOrderIncome extends Model
{
    protected $table = "pytao_user_order_income";
    protected $guarded = ['id'];
    public $timestamps = false;
}
