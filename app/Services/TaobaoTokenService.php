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


}