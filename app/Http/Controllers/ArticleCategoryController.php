<?php

namespace App\Http\Controllers;

use App\ArticleCategory;
use App\Tag;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Response;

class ArticleCategoryController extends Controller
{
    public function show(Request $request){
        return view("admin.article.category");
    }

    public function getList(Request $request){
        $start = $request->input('iDisplayStart');
        $limit = $request->input('iDisplayLength');


        $tags = ArticleCategory::skip($start)->take($limit)->get();
        $data = array();
        $data['recordsFiltered'] = $data['recordsTotal'] = Tag::count();
        $data['data'] = $tags;

        return Response::json($data);

    }

    public function save(Request $request){
        $id = $request->input("id");
        $name = $request->input("name");
        $pic = $request->input("pic");

        $data = [
            'name'=>$name,
            'pic'=> $pic
        ];

        $ret = false;
        if($id){
            $ret = ArticleCategory::where("id", $id)->update($data);
        }else{
            $ret = ArticleCategory::create($data);
        }

        if($ret){
            return $this->ajaxSuccess();
        }else{
            return $this->ajaxError("保存失败");
        }

    }

    public function del(Request $request){
        $id = $request->input("id");

        if(ArticleCategory::where("id", $id)->delete()){
            return $this->ajaxSuccess();
        }else{
            return $this->ajaxError("保存失败");
        }

    }
}
