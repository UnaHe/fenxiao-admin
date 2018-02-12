<?php

namespace App\Http\Controllers;

use App\Services\UserGradeService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    /**
     * 显示管理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request){
        $grades = (new UserGradeService())->getGrades();
        return view("admin.user.user_list", [
            'grades' => $grades
        ]);
    }

    /**
     * 用户列表
     * @param Request $request
     * @return mixed
     */
    public function getList(Request $request){
        $data = (new UserService())->userList($request);
        return Response::json($data);
    }

    /**
     * 用户详情
     * @param Request $request
     * @return mixed
     */
    public function detail(Request $request){
        $data = (new UserService())->detail($request->get('user_id'));
        return $this->ajaxSuccess($data);
    }

    /**
     * 保存用户信息
     * @param Request $request
     * @return static
     */
    public function save(Request $request){
        try{
            if(!(new UserService())->save($request)){
                throw new \Exception("保存失败");
            }
        }catch (\Exception $e){
            return $this->ajaxError($e->getMessage());
        }
        return $this->ajaxSuccess();
    }

    /**
     * 用户关系
     * @param Request $request
     * @return static
     */
    public function tree(Request $request){
        $data = (new UserService())->tree($request->get('user_id'), $request->get('type'));
        return $this->ajaxSuccess($data);
    }


}
