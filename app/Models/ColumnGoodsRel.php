<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 栏目与商品关系
 * Class ColumnGoodsRel
 * @package App\Models
 */
class ColumnGoodsRel extends Model
{
    protected $connection = "pytdb";
    protected $table = "xmt_column_goods_rel";
    protected $guarded = ['id'];

}
