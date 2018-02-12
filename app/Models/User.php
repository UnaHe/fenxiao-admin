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
class User extends Authenticatable
{
    protected $table = "pytao_user";
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

    /**
     * passport查找用户
     * @param $login
     * @return mixed
     */
    public function findForPassport($login){
        $user = $this->where('phone', $login)->first();
        if(!$user){
            throw  new OAuthServerException("用户未注册", 0, 'unregister_user');
        }
        if($user['is_forbid']){
            throw  new OAuthServerException("用户已禁用", 0, 'forbidden_user');
        }

        $expireTime = $user['expiry_time'];
        if($expireTime){
            $expireTime = new Carbon($expireTime);

            $timeDiff = (new Carbon())->diffInSeconds($expireTime, false);
            if($timeDiff <= 0){
                throw  new OAuthServerException("账号已过期", 0, 'user_expired');
            }
        }

        return $user;
    }
}
