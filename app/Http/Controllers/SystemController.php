<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class SystemController extends Controller
{
    public function updatepwd(Request $request){
        return view("admin.system.updatepwd");
    }

    public function updatepwdSave(Request $request){
        $account = $request->input("account");
        $oldpass = $request->input("oldpass");
        $newpass = $request->input("newpass");

        $guard = Auth::guard("admin");
        $user = $guard->user();

        $provider = $guard->getProvider();

        //验证密码
        if (!$provider->validateCredentials($user, ['password'=> $oldpass])) {
            return $this->ajaxError("原密码错误", 301);
        }

        $user->email = $account;
        if($newpass){
            $user->password = crypt($newpass);
        }

        if(!$user->save()){
            return $this->ajaxError("修改失败");
        }

        return $this->ajaxSuccess("修改成功");
    }



}
