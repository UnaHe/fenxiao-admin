<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    protected $loginView = 'admin.auth.login';

    public function showLoginForm()
    {
        return view($this->loginView);
    }

    public function username()
    {
        return "mobile";
    }

    public function doLogin(Request $request)
    {
        try{
            $this->login($request);
            return $this->ajaxSuccess();
        }catch (\Exception $e){
            return $this->ajaxError('用户名或密码错误');
        }
    }
}
