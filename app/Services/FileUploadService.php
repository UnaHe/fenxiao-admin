<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2018/2/13
 * Time: 9:13
 */

namespace App\Services;

// 引入鉴权类
use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;

class FileUploadService
{
    /**
     * 上传表单文件
     * @param string $formName 表单名称
     * @return bool
     */
    public function uploadForm($formName){
        if(!isset($_FILES[$formName])){
            return false;
        }
        $file = base64_encode(microtime().mt_rand(1000, 9999));
        $ext = pathinfo($_FILES[$formName]['name'], PATHINFO_EXTENSION);
        if($ext){
            $file .= ".".$ext;
        }

        return $this->upload($_FILES[$formName]['tmp_name'], $file);
    }

    /**
     * 上传文件
     * @param $file 文件内容
     * @param $fileName 文件名称
     * @throws Exception
     */
    public function upload($file, $fileName){
        // 构建鉴权对象
        $auth = new Auth(config('qiniu.access_key'), config('qiniu.secret_key'));
        // 生成上传 Token
        $token = $auth->uploadToken(config('qiniu.bucket'));

        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $fileName, $file);

        if ($err !== null) {
            throw new \Exception($err);
            return false;
        }

        return config('qiniu.bucket_url')."/".$fileName;
    }
}