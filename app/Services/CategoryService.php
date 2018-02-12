<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/18
 * Time: 15:51
 */
namespace App\Services;

use App\Helpers\CacheHelper;
use App\Models\GoodsCategory;

class CategoryService
{
    /**
     * 获取所有商品分类
     * @return mixed
     */
    public function getAllCategory(){
        if($cache = CacheHelper::getCache()){
            return $cache;
        }

        $data = GoodsCategory::select("id", "name", "icon_url")->orderBy("sort", "desc")->get();
        if($data){
            CacheHelper::setCache($data, 5);
        }
        return $data;
    }
}
