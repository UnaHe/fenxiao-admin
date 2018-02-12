<?php

namespace App\Http\Controllers;

use App\Page;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Response;

class PageController extends Controller
{
    public function index(Request $request){
        return view("admin.page.index");
    }

    public function getList(Request $request){
        $start = $request->input('iDisplayStart');
        $limit = $request->input('iDisplayLength');


        $tags = Page::skip($start)->take($limit)->get();
        $data = array();
        $data['recordsFiltered'] = $data['recordsTotal'] = Page::count();
        $data['data'] = $tags;

        return Response::json($data);
    }

    /**
     * 编辑页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request){
        $id = $request->input("id", 0);
        $alias = $request->input("alias");

        $page_class = ".page_manage";
        $page_title = "编辑页面";
        $data = [];

        if($id){
            $page = Page::find($id);
            $data['page'] = $page;
            $alias = $page['alias'];
        }else if($alias){
            if($alias == 'agreement'){
                $page_title = "编辑注册条款";
                $page_class = ".page_agreement";
            }else if($alias == 'buynotice'){
                $page_title = "编辑购买说明";
                $page_class = ".page_buynotice";
            }

            $data['page'] = Page::where("alias", $alias)->first();
        }

        return view("admin.page.edit", $data, ['page_title'=>$page_title, "alias"=>$alias, "page_class"=>$page_class]);
    }


    /**
     * 保存页面,如果页面不存在则新增
     * @param Request $request
     * @return mixed
     */
    public function save(Request $request){
        $id = $request->input("id");
        $alias = $request->input("alias");
        $title= $request->input("title");
        $content = $request->input("content");

        $page = null;
        //根据条件查找页面
        if($id){
            $page = Page::find($id);
        }else if($alias){
            $page = Page::where("alias", $alias)->first();
        }

        if(!$page){
            $page = new Page();
        }

        $page->alias = $alias;
        $page->title = $title;
        $page->content = $content;

        if($page->save()){
            return $this->ajaxSuccess(['id'=>$page->id]);
        }else{
            return $this->ajaxError("保存失败");
        }
    }

    public function del(Request $request){
        $id = $request->input("id");

        if(Page::where("id", $id)->delete()){
            return $this->ajaxSuccess();
        }else{
            return $this->ajaxError();
        }

    }


}
