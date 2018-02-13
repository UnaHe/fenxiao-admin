<?php

namespace App\Http\Controllers;

use App\Services\SysConfigService;
use Illuminate\Http\Request;


class SystemConfigController extends Controller
{
    /**
     * 显示
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request){
        $configs = (new SysConfigService())->configs();
        return view("admin.system_config.system_config", [
            'configs' => $configs,
        ]);
    }

    /**
     * 保存配置
     * @param Request $request
     */
    public function saveConfig(Request $request){
        if(!(new SysConfigService())->saveConfig($request)){
            return $this->ajaxError("保存失败");
        }
        return $this->ajaxSuccess();
    }
}
