<?php

namespace App\Http\Controllers;

use App\Tag;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class TagController extends Controller
{
    public function show(Request $request){
        $all_tag_pic = Cache::get("all_tag_pic");
        return view("admin.tag.index", ['all_tag_pic'=>$all_tag_pic]);
    }

    public function getList(Request $request){
        $start = $request->input('iDisplayStart');
        $limit = $request->input('iDisplayLength');


        $tags = Tag::skip($start)->take($limit)->get();
        $data = array();
        $data['recordsFiltered'] = $data['recordsTotal'] = Tag::count();
        $data['data'] = $tags;

        return Response::json($data);

    }

    public function save(Request $request){
        $id = $request->input("id");
        $name = $request->input("name");
        $icon_class = $request->input("icon_class");
        $pic = $request->input("pic");

        $data = [
            'name'=>$name,
            'icon_class'=>$icon_class,
            'pic'=> $pic
        ];

        $ret = false;
        if($id){
            $ret = Tag::where("id", $id)->update($data);
        }else{
            $ret = Tag::create($data);
        }

        if($ret){
            return $this->ajaxSuccess();
        }else{
            return $this->ajaxError("保存失败");
        }

    }

    /**
     * 保存首页所有主题图片
     * @param Request $request
     * @return mixed
     */
    public function saveAllTagPic(Request $request){
        $all_tag_pic = $request->input("all_tag_pic");

        try{
            Cache::forever('all_tag_pic', $all_tag_pic);
        }catch (\Exception $e){
            return $this->ajaxError();
        }

        return $this->ajaxSuccess();
    }

    public function del(Request $request){
        $id = $request->input("id");

        if(Tag::where("id", $id)->delete()){
            return $this->ajaxSuccess();
        }else{
            return $this->ajaxError("保存失败");
        }

    }
}
