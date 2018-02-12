<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/24
 * Time: 15:11
 */
namespace App\Services;

use App\Helpers\ErrorHelper;
use App\Helpers\GoodsHelper;
use App\Models\Banner;
use App\Models\Goods;
use App\Models\GoodsCategory;
use App\Models\WechatPage;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class WechatPageService
{
    /**
     * 创建微信单页
     */
    public function createPage($goodsInfo, $userId){
        $id = Cache::increment('pytao_wechat_page_increase_id');
        if($id === 1){
            $id = Cache::increment('pytao_wechat_page_increase_id', WechatPage::max('id') + 1000);
        }
        $data = [
            'id'    => $id,
            'user_id' => $userId,
            'goods_id' => $goodsInfo['goods_id'],
            'url' => $goodsInfo['url'],
            'tao_code' => $goodsInfo['tao_code'],
            'create_time' => date('Y-m-d h:i:s'),
            'price_full' => $goodsInfo['price_full'] ?: 0,
            'coupon_price' => $goodsInfo['coupon_price']?:0,
            'coupon_time' => date('Y-m-d h:i:s'),
            'pic' => (new GoodsHelper())->resetPic($goodsInfo['pic']),
            'title' => $goodsInfo['title'],
            'description' => $goodsInfo['description'],
            'short_url' => $goodsInfo['s_url'],
        ];

        if(WechatPage::create($data)){
            return $this->getPageUrl($id);
        }
        return false;
    }

    /**
     * 获取微信单页链接
     * @param $id
     * @return mixed
     */
    public function getPageUrl($id){
        return "http://".config('domains.redirect_domain').URL::action('WechatPageController@redirect', ['id'=>$id], false);
    }

    /**
     * 查询单页商品信息
     * @param $id
     */
    public function getPage($id){
        return WechatPage::where("id", $id)->first();
    }
}
