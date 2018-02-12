<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/27
 * Time: 13:00
 */
namespace App\Helpers;

class UtilsHelper
{
    /**
     * 数组值
     * @param $array
     * @param $key
     * @param null $default
     * @return null
     */
    public static function arrayValue($array, $key, $default=null){
        return isset($array[$key]) ? $array[$key] : $default;
    }

    /**
     * 二维数组根据字段进行排序
     * @params array $array 需要排序的数组
     * @params string $field 排序的字段
     * @params string $sort 排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
     */
    public static function arraySort($array, $field, $sort = SORT_DESC){
        $arrSort = array();
        foreach ($array as $uniqid => $row) {
            foreach ($row as $key => $value) {
                $arrSort[$key][$uniqid] = $value;
            }
        }
        array_multisort($arrSort[$field], $sort, $array);
        return $array;
    }

    /**
     * 随机字符串
     * @param int $length
     * @param string $char
     * @return bool|string
     */
    public static function randStr($length = 32, $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        if(!is_int($length) || $length < 0) {
            return false;
        }

        $string = '';
        for($i = $length; $i > 0; $i--) {
            $string .= $char[mt_rand(0, strlen($char) - 1)];
        }

        return $string;
    }
}