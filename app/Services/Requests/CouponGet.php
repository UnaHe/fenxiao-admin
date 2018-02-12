<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/27
 * Time: 10:40
 */
namespace App\Services\Requests;

class CouponGet extends TaobaoRequest {

    private $result;

    /**
     * 获取二合一页面详情信息
     * @param $url
     */
    public function initWithUlandUrl($url){
        parse_str(parse_url($url)['query'], $lastUrlParams);
        //需要传递的参数
        $apiParamData = [];
        $params = ['e','me','dx','itemId','activityId','pid','src','scm','engpvid','mt','couponType','ptl'];
        foreach($params as $param){
            if(isset($lastUrlParams[$param])){
                $apiParamData[$param] = $lastUrlParams[$param];
            }
        }

        return $this->initWithParams($apiParamData);
    }

    /**
     * 初始化
     * @param $params
     * @return $this
     */
    public function initWithParams($params){
        $result = $this->requestWithH5tk('mtop.alimama.union.hsf.coupon.get', $params);
        if ($result && isset($result['result'])){
            $this->result = $result['result'];
        }
        return $this;
    }

    /**
     * 通过商品信息初始化
     * @param $goodsId
     * @param null $couponId
     * @return CouponGet
     */
    public function initWithItemInfo($goodsId, $couponId=null){
        $params = [
            'itemId' => $goodsId,
        ];
        if($couponId){
            $params['activityId'] = $couponId;
        }
        return $this->initWithParams($params);
    }

    /**
     * 获取详情
     * @return mixed
     */
    public function getResult(){
        return $this->result;
    }

    /**
     * 获取商品信息
     * @return mixed
     */
    public function getItem(){
        if($this->getResult()){
            return $this->getResult()['item'];
        }else{
            return null;
        }
    }

    /**
     * 获取商品字段属性
     * @param $field
     */
    public function getItemAttr($field){
        if(!$this->getItem()){
            return null;
        }
        return isset($this->getItem()[$field]) ? $this->getItem()[$field] : null;
    }

    /**
     * 获取详情字段
     * @param $field
     * @return null
     */
    public function getResultAttr($field){
        if(!$this->getResult()){
            return null;
        }
        return isset($this->getResult()[$field]) ? $this->getResult()[$field] : null;
    }

    /**
     * 商品id
     * @return null
     */
    public function getItemId(){
        return $this->getItemAttr('itemId');
    }

    /**
     * 是否天猫商品
     * @return int
     */
    public function getIsTmall(){
        return $this->getItemAttr('tmall') ? 1 : 0;
    }

    /**
     * 商品价格
     * @return null
     */
    public function getPrice(){
        return $this->getItemAttr('discountPrice');
    }

    /**
     * 商品名称
     * @return null
     */
    public function getTitle(){
        return $this->getItemAttr('title');
    }

    /**
     * 店铺名称
     * @return null
     */
    public function getShopName(){
        return $this->getResultAttr('shopName');
    }

    /**
     * 店铺logo
     * @return null
     */
    public function getShopLogo(){
        return $this->getResultAttr('shopLogo');
    }

    /**
     * 商品图片
     * @return null
     */
    public function getPicUrl(){
        return $this->getItemAttr('picUrl');
    }

    /**
     * 月销量
     * @return null
     */
    public function getSellNum(){
        return $this->getItemAttr('biz30Day');
    }

    /**
     * 优惠价金额
     * @return null
     */
    public function getCouponPrice(){
        return $this->getResultAttr('amount') ?: 0;
    }

    /**
     * 优惠券开始时间
     * @return int
     */
    public function getCouponStartTime(){
        return $this->getResultAttr('effectiveStartTime');
    }

    /**
     * 优惠券结束时间
     * @return int
     */
    public function getCouponEndTime(){
        return $this->getResultAttr('effectiveEndTime');
    }

    /**
     * 优惠券使用条件
     * @return null
     */
    public function getCouponPrerequisite(){
        return $this->getResultAttr('startFee')?:0;
    }

    /**
     * 获取商品链接
     * @return null
     */
    public function getItemClickUrl(){
        return $this->getItemAttr('clickUrl');
    }

    /**
     * 获取优惠券状态
     */
    public function getStatus(){
        return $this->getResultAttr('retStatus');
    }
}