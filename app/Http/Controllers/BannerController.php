<?php

namespace App\Http\Controllers;

use App\Banner;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Response;

class BannerController extends Controller
{
    public function show(Request $request){
        return view("admin.banner.index");
    }

    public function getList(Request $request){
        $start = $request->input('iDisplayStart');
        $limit = $request->input('iDisplayLength');


        $items = Banner::skip($start)->take($limit)->get();
        $data = array();
        $data['recordsFiltered'] = $data['recordsTotal'] = Banner::count();
        $data['data'] = $items;

        return Response::json($data);

    }

    public function save(Request $request){
        $id = $request->input("id");
        $title = $request->input("title");
        $type = $request->input("type");
        $pic = $request->input("pic");
        $link = $request->input("link");

        $data = [
            'title'=>$title,
            'type'=>$type,
            'pic'=> $pic,
            'link' => $link
        ];

        $ret = false;
        if($id){
            $ret = Banner::where("id", $id)->update($data);
        }else{
            $ret = Banner::create($data);
        }

        if($ret){
            return $this->ajaxSuccess();
        }else{
            return $this->ajaxError("保存失败");
        }

    }

    public function del(Request $request){
        $id = $request->input("id");

        if(Banner::where("id", $id)->delete()){
            return $this->ajaxSuccess();
        }else{
            return $this->ajaxError("保存失败");
        }

    }
}
