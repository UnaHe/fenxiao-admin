<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/23
 * Time: 15:26
 */
namespace App\Helpers;


class TaobaoHelper
{
    /**
     * 检测pid合法性
     * @param $pid
     * @return bool
     */
    public function isPid($pid){
        return preg_match('/^mm_(\d{1,20})_(\d{1,20})_(\d{1,20})$/',$pid) ? true : false;
    }
}