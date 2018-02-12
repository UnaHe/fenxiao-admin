<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * 商品分类表
 * Class GoodsCategory
 * @package App\Models
 */
class GoodsCategory extends Model
{
    protected $connection = "pytdb";
    protected $table = "xmt_goods_category";
    protected $guarded = ['id'];

}
