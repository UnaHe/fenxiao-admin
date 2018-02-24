<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/18
 * Time: 15:51
 */
namespace App\Services;

use App\Helpers\CacheHelper;
use App\Helpers\QueryHelper;
use App\Models\Banner;
use App\Models\WechatDomain;
use Carbon\Carbon;

class WechatDomainService
{
    /**
     * 列表
     * @param \Illuminate\Http\Request $request
     */
    public function bannerList($request){
        $query = WechatDomain::query();
        $query->orderBy("id", "desc");

        //分页数据
        $data  = (new QueryHelper())->pagination($query);
        foreach ($data['data'] as &$item){
            $item['type_str'] = $this->getDomainType($item['type']);
        }

        return $data;
    }

    /**
     * 保存
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function save($request){
        $id = $request->post("id");
        if($id){
            $model = WechatDomain::find($id);
        }else{
            $model = new WechatDomain();
        }

        $model['domain'] = $request->post('domain');
        $model['type'] = $request->post('type');

        if(!$model->save()){
            throw new \Exception("保存失败");
        }

        return true;
    }

    /**
     * 删除
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function delete($id){
        $model = WechatDomain::find($id);
        if(!$id){
            throw new \Exception("域名不存在");
        }

        if(!$model->delete()){
            throw new \Exception("删除失败");
        }
        return true;
    }

    /**
     * 域名类型
     * @param $type
     * @return string
     */
    public function getDomainType($type){
        return isset(WechatDomain::$domainType[$type]) ? WechatDomain::$domainType[$type] : "其他";
    }

}
