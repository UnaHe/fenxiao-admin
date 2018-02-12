<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * banner表
 * Class Banner
 * @package App\Models
 */
class Banner extends Model
{
    protected $table = "pytao_banner";
    protected $guarded = ['id'];
    public $timestamps = false;

}
