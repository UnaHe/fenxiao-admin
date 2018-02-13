<?php

namespace App\Http\Controllers;

use App\Lib\Uploader;
use App\Page;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

use App\Http\Requests;

class FileController extends Controller
{
    /**
     * 上传文件
     * @param Request $request
     * @return static
     */
    public function upload(Request $request){
        try{
            $url = (new FileUploadService())->uploadForm("upfile");
        }catch (\Exception $e){
            return $this->ajaxError();
        }
        return $this->ajaxSuccess($url);
    }
}
