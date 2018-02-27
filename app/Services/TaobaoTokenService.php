<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/31
 * Time: 17:36
 */

namespace App\Services;


use App\Helpers\QueryHelper;
use App\Models\TaobaoToken;
use Carbon\Carbon;

class TaobaoTokenService
{
    /**
     * 获取token
     * @param $memberId
     * @return mixed
     */
    public function getAuthToken($memberId){
        return TaobaoToken::where("member_id", $memberId)->first();
    }

    /**
     * 获取token
     * @param $memberId
     * @return mixed
     */
    public function getToken($memberId){
        $token = $this->getAuthToken($memberId);
        if(!$token){
            return null;
        }
        return $token['access_token'];
    }

    /**
     * 列表
     * @param \Illuminate\Http\Request $request
     */
    public function tokenList($request){

        $query = TaobaoToken::query();

        //联盟id
        if($memberId = trim($request->get('member_id'))){
            $query->where("member_id", '=' , $memberId);
        }

        $query->orderBy("id", "desc");

        //分页数据
        $data  = (new QueryHelper())->pagination($query);

        return $data;
    }

    /**
     * 刷新token
     * @param $id
     */
    public function refreshToken($id){
        $token = TaobaoToken::find($id);
        if(!$token){
            throw new \Exception("token不存在");
        }

        if(Carbon::now()->diffInSeconds(new Carbon($token['re_expires_at']), false) < 0){
            throw new \Exception("刷新token已失效，无法刷新");
        }
        if(!(new TaobaoService())->refreshUserToken($token['member_id'])){
            throw new \Exception("刷新失败");
        }

        return true;
    }

    /**
     * 保存token
     * @param $memberId
     * @param $tokens
     */
    public function saveToken($memberId, $tokens){
        $time = time();
        $now  = date('Y-m-d H:i:s', $time);

        $token = TaobaoToken::where("member_id", $memberId)->first();
        if(!$token){
            if(!$tokens){
                throw new \Exception("联盟ID对应的授权不存在, 请填写授权结果地址");
            }
            $token = new TaobaoToken();
            $token['create_time'] = $now;
        }
        $token['member_id'] = $memberId;
        $token['update_time'] = $now;

        if($tokens){
            $token['access_token'] = $tokens['access_token'];
            $token['token_type'] = $tokens['token_type'];
            $token['expires_at'] = date('Y-m-d H:i:s', $time+$tokens['expires_in']);
            $token['refresh_token'] = $tokens['refresh_token'];
            $token['re_expires_at'] = $tokens['re_expires_in'] ? date('Y-m-d H:i:s', $time+$tokens['re_expires_in']) : null;
            $token['taobao_user_id'] = $tokens['taobao_user_id'];
            $token['taobao_user_nick'] = $tokens['taobao_user_nick'];
        }

        if(!$token->save()){
            return false;
        }

        return true;
    }


    /**
     * 删除授权
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function delete($id){
        $model = TaobaoToken::find($id);
        if(!$id){
            throw new \Exception("授权不存在");
        }

        if(!$model->delete()){
            throw new \Exception("删除失败");
        }
        return true;
    }

}