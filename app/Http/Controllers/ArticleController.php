<?php

namespace App\Http\Controllers;

use App\Article;
use App\ArticleCategory;
use App\Page;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Response;

class ArticleController extends Controller
{
    public function index(Request $request){
        return view("admin.article.index");
    }

    public function getList(Request $request){
        $start = $request->input('iDisplayStart');
        $limit = $request->input('iDisplayLength');


        $tags = Article::skip($start)->take($limit)->get();
        $data = array();
        $data['recordsFiltered'] = $data['recordsTotal'] = Article::count();
        $data['data'] = $tags;

        return Response::json($data);
    }

    public function edit(Request $request){
        $id = $request->input("id", 0);

        //所有主题
        $category = ArticleCategory::all();

        $data = [];
        $data["category_id"] = 0;
            //已经设置的标签
        $selected_tag = [];

        if($id){
            $article = Article::withTrashed()->find($id);
            $data['article'] = $article;
            $data["category_id"] = $article["category_id"];
        }

        $data["category"] = $category;

        return view("admin.article.edit", $data);
    }

    public function save(Request $request){
        $id = $request->input("id", 0);
        $main_pic = $request->input("main_pic");
        $file = "../public/".$main_pic;
        if(!is_file($file)){
            return $this->ajaxError("请选择图片上传");
        }

        list($width, $height, $type, $attr) = getimagesize($file);

        $data = [
            'title'=> $request->input("title"),
            'category_id'=> $request->input("category_id"),
            'main_pic'=> $main_pic,
            'main_pic_width'=> $width,
            'main_pic_height'=> $height,
            'content'=> $request->input("content"),
        ];

        $article = Article::firstOrNew(['id'=>$id]);
        try{
            if(!$article->fill($data)->save()){
                throw new \Exception("保存产品失败");
            }

            return $this->ajaxSuccess(['id'=> $article->id]);

        }catch(\Exception $e){
            return $this->ajaxError($e->getMessage());
        }
    }


    public function del(Request $request){
        $id = $request->input("id");

        if(Article::where("id", $id)->delete()){
            return $this->ajaxSuccess();
        }else{
            return $this->ajaxError();
        }

    }

}
