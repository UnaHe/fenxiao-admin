<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 联盟订单
 * Class AlimamaOrder
 * @package App\Models
 */
class AlimamaOrder extends Model
{
    protected $table = "pytao_alimama_order";
    protected $guarded = ['id'];
    public $timestamps = false;

    /**
     * 订单付款
     */
    const ORDERSTATE_PAYED = 1;

    /**
     * 订单结算
     */
    const ORDERSTATE_SETTLE = 2;

    /**
     * 订单失效
     */
    const ORDERSTATE_INVALID = 3;

    /**
     * 订单成功
     * @todo 不清楚是什么意思
     */
    const ORDERSTATE_SUCCESS = 4;

    /**
     * 订单状态配置
     * @var array
     */
    static $ORDERSTATE = [
        self::ORDERSTATE_PAYED => '已付款',
        self::ORDERSTATE_SETTLE => '已结算',
        self::ORDERSTATE_INVALID => '已失效',
        self::ORDERSTATE_SUCCESS => '订单成功',
    ];

    /**
     * 获取订单状态字符串
     * @param $orderState
     * @return mixed|null
     */
    public static function getOrderStateStr($orderState){
        return isset(self::$ORDERSTATE[$orderState]) ? self::$ORDERSTATE[$orderState] : null;
    }
}
