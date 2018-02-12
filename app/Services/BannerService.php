<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/18
 * Time: 15:51
 */
namespace App\Services;

use App\Helpers\CacheHelper;
use App\Models\Banner;

class BannerService
{
    /**
     * 获取指定位置广告
     * @return mixed
     */
    public function getBanner($position){
        if($cache = CacheHelper::getCache()){
            return $cache;
        }

        $data = Banner::select('name', 'pic', 'click_url')->where(['position'=>$position, 'is_delete'=>0])->get();
        if($data){
            CacheHelper::setCache($data, 5);
        }
        return $data;
    }
}
