<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 意见反馈
 * Class Feedback
 * @package App\Models
 */
class Feedback extends Model
{
    protected $table = "pytao_feedback";
    protected $guarded = ['id'];
    public $timestamps = false;
}