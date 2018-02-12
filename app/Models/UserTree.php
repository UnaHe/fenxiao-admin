<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * 分销用户关系表
 * Class UserTree
 * @package App\Models
 */
class UserTree extends Model
{
    protected $table = "pytao_user_tree";
    protected $guarded = ['id'];
    public $timestamps = false;

}
