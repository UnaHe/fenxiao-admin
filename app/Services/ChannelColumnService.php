<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/18
 * Time: 15:51
 */
namespace App\Services;

use App\Helpers\GoodsHelper;
use App\Helpers\QueryHelper;
use App\Models\ChannelColumn;
use App\Models\ColumnGoodsRel;
use App\Models\Goods;

/**
 * 商品栏目
 * Class ChannelColumnService
 * @package App\Services
 */
class ChannelColumnService
{
    /**
     * 通过栏目代码查询栏目
     * @param $columnCode
     * @return mixed
     */
    public function getByCode($columnCode){
        return ChannelColumn::where('code', $columnCode)->first();
    }

    /**
     * 获取秒杀时间点
     * @param $startTime
     * @param $endTime
     */
    public function miaoshaTimes($startTime, $endTime){
        $times = ColumnGoodsRel::where([
            ["column_code", "zhengdianmiaosha"],
            ["active_time", ">=", $startTime],
            ["active_time", "<=", $endTime],
        ])->select("active_time")->distinct('active_time')->orderby("active_time", "asc")->get();
        if($times){
            foreach ($times as &$time){
                $time['time'] = date('H:i', strtotime($time['active_time']));
            }
        }
        return $times;
    }

    /**
     * 秒杀商品列表
     * @param $startTime
     * @param $endTime
     * @return mixed
     */
    public function miaoshaGoods($activeTime, $userId){
        $query = Goods::query()->from((new Goods())->getTable().' as goods');
        $query->leftJoin((new ColumnGoodsRel())->getTable().' as ref', 'goods.id', '=', 'ref.goods_id');
        $query->where('ref.column_code', 'zhengdianmiaosha');

        $query->select('goods.*', 'ref.goods_col_title', 'ref.goods_col_pic', 'ref.goods_col_des', 'ref.active_time');
        $query->where("goods.is_del", 0);
        $query->where("ref.active_time", '=', $activeTime);
        $query->where(function($query) use($activeTime){
            $query->where("goods.starttime", '>=', $activeTime);
            $query->orWhere("goods.starttime", null);
        });

        $query->orderBy('ref.id', 'desc');

        $list = (new QueryHelper())->pagination($query)->get();
        if($list){
            $commissionService = new CommissionService($userId);
            foreach ($list as &$item){
                $item['is_miaosha'] = 1;
                $item['pic'] = (new GoodsHelper())->resizePic($item['pic'], '240x240');
                //用户返利金额
                $item['commission_amount'] = $commissionService->goodsCommisstion($item);
            }
        }

        return $list;
    }

}