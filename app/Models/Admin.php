<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Passport;
use League\OAuth2\Server\Exception\OAuthServerException;

/**
 * 用户表
 * Class User
 * @package App\Models
 */
class Admin extends Authenticatable
{
    protected $table = "pytao_admin";
    public $timestamps = false;

    use Notifiable, HasApiTokens;

    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
