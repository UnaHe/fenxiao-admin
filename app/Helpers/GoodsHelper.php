<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/18
 * Time: 16:26
 */
namespace App\Helpers;


class GoodsHelper
{
    /**
     * 生成淘宝链接
     * @param $taobaoGoodsId
     * @param $isTmall
     */
    public function generateTaobaoUrl($taobaoGoodsId, $isTmall=0){
        $taobaoUrl = 'https://item.taobao.com/item.htm?id=%s';
        $tmallUrl = 'https://detail.tmall.com/item.htm?id=%s';
        $url = $isTmall ? $tmallUrl : $taobaoUrl;

        return sprintf($url, $taobaoGoodsId);
    }

    /**
     * 修改商品列表图片大小
     * @param $goodsList
     * @param array $sizes
     * @return array
     */
    public function resizeGoodsListPic($goodsList, $sizes=['pic' => '310x310']){
        if(!is_array($goodsList)){
            $goodsList = [$goodsList];
        }

        foreach ($goodsList as &$goods){
            foreach ($goods as $key=>$value){
                if(array_key_exists($key, $sizes)){
                    $goods[$key] = $this->resizePic($value, $sizes[$key]);
                }
            }
        }
        return $goodsList;
    }

    /**
     * 修改淘宝图片大小
     * @param $pic
     * @param $size
     * @return string
     */
    public function resizePic($pic, $size){
        return $this->resetPic($pic)."_".$size;
    }

    /**
     * 移除淘宝图片大小信息
     * @param $pic_url
     * @return mixed
     */
    public function resetPic($picUrl){
        return preg_replace('/_\d+x\d+([A-Za-z0-9]*?\.[A-Za-z]+)?/', '', $picUrl);
    }
}