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
use Carbon\Carbon;

class BannerService
{
    /**
     * banner列表
     * @param \Illuminate\Http\Request $request
     */
    public function bannerList($request){

        $query = Banner::query();

        $query->where("is_delete", '=' , 0);
        $query->orderBy("id", "desc");

        //分页数据
        $data  = (new QueryHelper())->pagination($query);

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
            $banner = Banner::find($id);
        }else{
            $banner = new Banner();
            $banner['create_time'] = Carbon::now();
        }

        $banner['position'] = $request->post('position');
        $banner['name'] = $request->post('name');
        $banner['click_url'] = $request->post('click_url');
        $banner['pic'] = $request->post('pic');
        $banner['update_time'] = Carbon::now();

        if(!$banner->save()){
            throw new \Exception("保存失败");
        }

        return true;
    }

    /**
     * 删除banner
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function delete($id){
        $banner = Banner::find($id);
        if(!$id){
            throw new \Exception("banner不存在");
        }

        $banner['is_delete'] = 1;
        if(!$banner->save()){
            throw new \Exception("删除失败");
        }
        return true;
    }

}
