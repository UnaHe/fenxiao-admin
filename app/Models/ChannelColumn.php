<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * 频道栏目
 * Class ChannelColumn
 * @package App\Models
 */
class ChannelColumn extends Model
{
    protected $connection = "pytdb";
    protected $table = "xmt_channel_column";
    protected $guarded = ['id'];

}
