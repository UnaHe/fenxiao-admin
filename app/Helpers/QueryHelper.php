<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/18
 * Time: 16:26
 */
namespace App\Helpers;


use Illuminate\Database\Eloquent\Builder;

class QueryHelper
{
    /**
     * 构建分页查询条件
     * @param Builder $query
     * @return Builder
     */
    public function pagination(Builder $query){
        $request = app('request');
        //分页参数
        $start = $request->input('iDisplayStart');
        $limit = $request->input('iDisplayLength');
        $limit = $limit ?: 20;

        $queryTotal = clone $query;
        //分页数据
        $list = $query->skip($start)->take($limit)->get();
        $data = array();
        $data['recordsFiltered'] = $data['recordsTotal'] = $queryTotal->count();
        $data['data'] = $list;

        return $data;
    }
}