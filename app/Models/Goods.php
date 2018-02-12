<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * 商品表
 * Class Goods
 * @package App\Models
 */
class Goods extends Model
{
    protected $connection = "pytdb";
    protected $table = "xmt_goods_lib";
    protected $guarded = ['id'];
    public $timestamps = false;

}
