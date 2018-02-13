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
        $value = SysConfig::where('key', $key)->pluck("value")->first();
        return $value ?: $default;
    }

    /**
     * 获取所有配置信息
     */
    public function configs(){
        $configModels = SysConfig::get();
        $configs = [];
        foreach ($configModels as $configModel){
            $configs[$configModel['key']] = $configModel;
        }

        return $configs;
    }
}
