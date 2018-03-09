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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    /**
     * 保存配置
     * @param Request $request
     */
    public function saveConfig($request){
        $configs = $request->post();
        DB::beginTransaction();
        try{
            $tableName = (new SysConfig)->getTable();
            foreach ($configs as $key=>$value){
                DB::insert("replace into {$tableName}(`name`, `key`, `value`) values('{$key}', '{$key}', '{$value}')");
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return false;
        }
        return true;
    }
}
