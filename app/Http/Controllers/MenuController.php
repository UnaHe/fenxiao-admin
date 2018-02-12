<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{

    public function edit($menu_name){
        $menu_name = "menu_".$menu_name;
        $menu_data = Cache::get($menu_name);
        $menu_data = $menu_data ? $menu_data : "";
        $menu = json_decode( $menu_data, true);

        switch($menu_name){
            case "menu_main":
                $title = "主导航配置";
                break;
            case "menu_about":
                $title = "了解觅行导航配置";
                break;
            case "menu_footer":
                $title = "页脚导航配置";
                break;
            default:
                $title = "导航配置";
        }
        $menu_about_page_link = Cache::get("menu_about_page_link");
        $data = [
            'menu_data'=>$menu,
            'menu_name'=>$menu_name,
            'menu_about_page_link'=>$menu_about_page_link,
            'title'=>$title
        ];
        return view("admin.menu.edit",  $data);
    }

    public function  save(Request $request){
        $menu_data = $request->input("menu");
        $menu_name = $request->input("menu_name");

        try{
            if(!$menu_name){
                throw new \Exception("菜单名称为空");
            }

            if(!$menu_data){
                throw new \Exception("菜单数据为空");
            }

            Cache::forever($menu_name, $menu_data);
        }catch (\Exception $e){
            return $this->ajaxError();
        }

        return $this->ajaxSuccess();
    }


    public function  saveAboutPageLink(Request $request){
        $link = $request->input("link");
        try{

            Cache::forever("menu_about_page_link", $link);
        }catch (\Exception $e){
            return $this->ajaxError();
        }
        return $this->ajaxSuccess();
    }
}
