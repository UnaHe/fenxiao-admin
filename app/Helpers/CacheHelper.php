<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/31
 * Time: 11:10
 */
namespace App\Helpers;


class CacheHelper
{
    /**
     * 查询缓存
     * @param array $cacheKeyArgs
     * @return \Illuminate\Cache\CacheManager|mixed
     */
    public static function getCache($cacheKeyArgs=[]){
        $caller = debug_backtrace()[1];
        $cacheKey = static::getCacheKey($caller, $cacheKeyArgs);
        return cache($cacheKey);
    }

    /**
     * 设置缓存
     * @param $data
     * @param int $expireTime
     * @param array $cacheKeyArgs
     * @return \Illuminate\Cache\CacheManager|mixed
     */
    public static function setCache($data, $expireTime=5, $cacheKeyArgs=[]){
        $caller = debug_backtrace()[1];
        $cacheKey = static::getCacheKey($caller, $cacheKeyArgs);
        return cache([$cacheKey=> $data], $expireTime);
    }

    /**
     * 获取缓存key
     * @param $caller
     * @param $cacheKeyArgs
     * @return string
     */
    public static function getCacheKey($caller, $cacheKeyArgs){
        $callerArgs = $cacheKeyArgs ?: $caller['args'];
        return config('app.name').":".$caller['class']."::".$caller['function'].":".md5(json_encode($callerArgs));
    }
}