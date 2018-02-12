<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/10/18
 * Time: 15:51
 */
namespace App\Services;

use App\Helpers\CacheHelper;
use App\Helpers\ErrorHelper;
use App\Helpers\GoodsHelper;
use App\Helpers\ProxyClient;
use App\Helpers\UrlHelper;
use App\Models\Banner;
use App\Models\Goods;
use App\Models\GoodsCategory;
use App\Services\Requests\CouponGet;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class TransferService
{
    private $topClient;

    public function __construct(){
        include_once app_path("Librarys/Taobao/TopSdk.php");
        $this->topClient = new \TopClient(config('taobao.appkey'), config('taobao.secretkey'));
        $this->topClient->format="json";
    }

    /**
     * 高效转链
     * @param $taobaoGoodsId 淘宝商品id
     * @param $pid 用户联盟PID
     * @param $token 用户授权token
     * @return mixed
     * @throws \Exception
     */
    public function transferLink($taobaoGoodsId, $pid, $token){
        if($cache = CacheHelper::getCache()){
            return $cache;
        }

        $pids = explode('_',$pid);
        $req = new \TbkPrivilegeGetRequest;
        $req->setItemId($taobaoGoodsId);
        $req->setAdzoneId($pids[3]); //B pid 第三位
        $req->setPlatform("1");
        $req->setSiteId($pids[2]);//A pid 第二位
        $resp = $this->topClient->execute($req, $token);

        //转换失败
        if (!$resp){
            throw new \Exception("转链失败");
        }

        //判断结果
        if(isset($resp['code'])){
            if($resp['code'] == 26){
                throw new \Exception("授权过期", ErrorHelper::ERROR_TAOBAO_INVALID_SESSION);
            }

            if(isset($resp['sub_code'])) {
                if ('invalid-sessionkey' == $resp['sub_code']) {
                    //session过期
                    throw new \Exception("授权过期", ErrorHelper::ERROR_TAOBAO_INVALID_SESSION);
                } else if ('isv.item-not-exist' == $resp['sub_code']) {
                    //商品错误
                    throw new \Exception("宝贝已下架或非淘客宝贝", ErrorHelper::ERROR_TAOBAO_INVALID_GOODS);
                } else if ('isv.pid-not-correct' == $resp['sub_code']) {
                    //pid错误
                    throw new \Exception("PID错误", ErrorHelper::ERROR_TAOBAO_INVALID_PID);
                }
            }
            throw new \Exception("转链失败");
        }

        $result = $resp['result']['data'];
        //更新商品佣金
        if(isset($result['max_commission_rate'])){
            $time = Carbon::now();
            Goods::where("goodsid", $taobaoGoodsId)->update([
                'commission' => $result['max_commission_rate'],
                'commission_update_time' => $time,
            ]);
        }
        CacheHelper::setCache($result, 5);
        return $result;
    }


    /**
     * 淘宝短链接sclick转换
     * @param $url 原始url
     * @return mixed
     */
    public function transferSclick($url){
        if($cache = CacheHelper::getCache()){
            return $cache;
        }

        try{
            $req = new \TbkSpreadGetRequest;
            $requests = new \TbkSpreadRequest;
            $requests->url = $url;
            $req->setRequests(json_encode($requests));
            $resp = $this->topClient->execute($req);
            $result = (array)$resp;

            if(isset($result['code'])){
                switch($result['sub_code']){
                    case 'isv.appkey-not-exists':
                        $error = "官方接口数据出错，请稍后再试！";
                        break;
                    case 'PARAMETER_ERROR_TITLE_ILLEGAL':
                        $error = "标题中包含敏感词汇，请检查标题内容后重试。";
                        break;
                    default:
                        $error = "官方接口数据出错，请稍后再试！";
                        break;
                }
                throw new \Exception($error);
            }

            $data = $result['results']['tbk_spread'][0]['content'];
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }

        CacheHelper::setCache($data, 5);
        return $data;
    }

    /**
     * 淘客链接转淘口令
     * @param $title
     * @param $url
     * @return mixed
     */
    public function transferTaoCode($title, $url, $pic=""){
        if($cache = CacheHelper::getCache()){
            return $cache;
        }

        try{
            $req = new \TbkTpwdCreateRequest;
            $req->setUserId("1");
            $req->setText(trim($title, " \t\n\r\0\x0B@"));
            $req->setUrl($url);
            $req->setLogo($pic);
            $req->setExt("{}");
            $resp = $this->topClient->execute($req);
            $result = (array)$resp;
            $data = $result['data']['model'];
        }catch (\Exception $e){
            throw new \Exception('淘口令转换失败');
        }

        CacheHelper::setCache($data);
        return $data;
    }


    /**
     * 商品转链
     */
    public function transferGoodsByUser($goodsId, $couponId, $title, $description, $pic, $priceFull, $couponPrice, $sellNum, $userId){
        if($cache = CacheHelper::getCache()){
            return $cache;
        }

        try{
            $pidInfo = (new UserService())->getPidInfo($userId);
            $token = (new TaobaoTokenService())->getToken($pidInfo['member_id']);
            if(!$token){
                throw new \Exception("未授权");
            }
            if(!$pidInfo){
                throw new \Exception("PID错误");
            }
            $data = $this->transferGoods($goodsId, $couponId, $title, $pic, $pidInfo['pid'], $token);

            $goodsInfo = [
                'goods_id' => $goodsId,
                'tao_code' => $data['tao_code'],
                'url' => $data['url'],
                's_url' => $data['s_url'],
                'pic' => $pic,
                'title' => $title,
                'description' => $description,
                'coupon_price' => $couponPrice,
                'price_full' => $priceFull,
            ];
            $wechatUrl = (new WechatPageService())->createPage($goodsInfo, $userId);
            //使用短网址
            try{
                $wechatUrl = (new UrlHelper())->shortUrl($wechatUrl);
            }catch (\Exception $e){
            }
            $data['wechat_url'] = $wechatUrl;

            $shareData = [
                'title' => $title,
                'price' => $priceFull,
                'used_price' => bcsub($priceFull, $couponPrice, 2),
                'coupon_price' => $couponPrice,
                'description' => $description,
                'tao_code' => $data['tao_code'],
                'wechat_url' => $wechatUrl,
                'sell_num' => $sellNum,
            ];
            //分享描述
            $data['share_desc'] = (new GoodsService())->getShareDesc($shareData);

        }catch (\Exception $e){
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        CacheHelper::setCache($data, 5);
        return $data;
    }

    /**
     * 商品转链
     * @param $goodsId 淘宝商品id
     * @param $couponId 指定优惠券
     * @param $title 标题
     * @param $pid pid
     * @param $token 淘宝session
     * @return array
     * @throws \Exception
     */
    public function transferGoods($goodsId, $couponId, $title, $pic, $pid, $token){
        if($cache = CacheHelper::getCache()){
            return $cache;
        }

        try{
            $result = $this->transferLink($goodsId,$pid,$token);
            $url = $result['coupon_click_url'];
            //不是阿里妈妈券则指定优惠券id
            if(strlen($couponId) > 1){
                $url .= "&activityId=".$couponId;
            }
            //无券商品直接用商品链接
            if(!$couponId){
                $url = (new CouponGet())->initWithUlandUrl($url)->getItemClickUrl();
            }
            $url = UrlHelper::fixUrlPrefix($url);
            $slickUrl = $this->transferSclick($url);
            $taoCode = $this->transferTaoCode($title, $slickUrl, $pic);

            $data = [
                'goods_id' => $goodsId,
                'url' => $url,
                's_url' => $slickUrl,
                'tao_code' => $taoCode
            ];
        }catch (\Exception $e){
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        CacheHelper::setCache($data, 5);
        return $data;
    }


}
