<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/18
 * Time: 14:48
 */
namespace  App\Traits;

use Illuminate\Http\JsonResponse;

trait AjaxResponse
{
    /**
     * 成功返回
     * @param array $data
     * @return static
     */
    protected function ajaxSuccess($data=array()){
        $ret = array(
            'code'=>200,
            'msg'=>'success',
            'data'=>$data
        );
        return $this->ajaxReturn($ret);
    }

    /**
     * 失败返回
     * @param string $msg
     * @param int $code
     * @param array $data
     * @return static
     */
    protected function ajaxError($msg='error', $code=300, $data= array()){
        $ret = array(
            'code'=>$code,
            'msg'=>$msg,
            'data'=>$data
        );
        return $this->ajaxReturn($ret);
    }

    /**
     * ajax返回
     * @param $data
     * @return static
     */
    protected function ajaxReturn($data){
        $data = json_encode($data, JSON_NUMERIC_CHECK);
        $data = str_replace(":null", ':""', $data);
        $data = json_decode($data, true);
        $response = JsonResponse::create($data);
        if(config('app.debug')){
            $response->header('environment', config('app.env'));
        }
        return $response;
    }

}