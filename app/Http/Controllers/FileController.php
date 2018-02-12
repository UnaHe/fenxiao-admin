<?php

namespace App\Http\Controllers;

use App\Lib\Uploader;
use App\Page;
use Illuminate\Http\Request;

use App\Http\Requests;

class FileController extends Controller
{
    public function umeditorUpload(Request $request){
        //上传配置
        $config = array(
            "savePath" => 'upload/' ,             //存储文件夹
            "maxSize" => 1000 ,                   //允许的文件最大尺寸，单位KB
            "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" )  //允许的文件格式
        );

        $up = new Uploader( "upfile" , $config );
        $callback= $request->input('callback');

        $info = $up->getFileInfo();
        /**
         * 返回数据
         */
        if($callback) {
            return '<script>'.$callback.'('.json_encode($info).')</script>';
        } else {
            return json_encode($info);
        }

    }

    public function upload(Request $request){
        return $this->umeditorUpload($request);
    }
}
