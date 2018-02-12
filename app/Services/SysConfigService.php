<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/27
 * Time: 10:55
 */
namespace App\Services;

use App\Helpers\CacheHelper;
use App\Models\SysConfig;

class SysConfigService
{
    /**
     * 获取配置
     * @param $key
     * @param null $default
     * @return null
     */
    public function get($key, $default=null){
        if(!$value = CacheHelper::getCache([$key])){
            $value = SysConfig::where('key', $key)->pluck("value")->first();
            CacheHelper::setCache($value, 1, [$key]);
        }

        return $value ?: $default;
    }
}
