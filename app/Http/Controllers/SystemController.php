<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SystemController extends Controller
{
    public function updatepwd(Request $request){
        $user = $request->user();

        return view("admin.system.updatepwd", [
            'user' => $user
        ]);
    }

    public function updatepwdSave(Request $request){
        $account = $request->input("account");
        $oldpass = $request->input("oldpass");
        $newpass = $request->input("newpass");

        $user = Admin::find($request->user()->id);

        if(!Hash::check($oldpass, $user['password'])){
            return $this->ajaxError("原密码错误", 301);
        }

        $user->mobile = $account;
        if($newpass){
            $user->password = bcrypt($newpass);
        }

        if(!$user->save()){
            return $this->ajaxError("修改失败");
        }

        return $this->ajaxSuccess("修改成功");
    }



}
