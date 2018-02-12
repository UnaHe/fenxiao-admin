<?php
    include "TopSdk.php";
    date_default_timezone_set('Asia/Shanghai'); 
//	$content = @file_get_contents('/Users/xt/Downloads/json.txt');
//	var_dump(json_decode($content));
//	var_dump(urlencode(mb_convert_encoding('阿里发票商家答疑', 'gb2312', 'utf-8')));


//$c = new TopClient;
//$c->appkey ='23116944';// '23528053';
//$c->secretKey = '35040c63a1fc7cf3720487ce1afb1ea2';//6f92f6511833c1dd97cafc355b81c2a9
//$req = new TbkShopGetRequest;
//$req->setFields("user_id,shop_title,shop_type,seller_nick,pict_url,shop_url");
//$req->setQ("好想你官方旗舰店");
////$req->setSort("commission_rate_des");
////$req->setIsTmall("false");
////$req->setStartCredit("1");
////$req->setEndCredit("20");
////$req->setStartCommissionRate("2000");
////$req->setEndCommissionRate("123");
////$req->setStartTotalAction("1");
////$req->setEndTotalAction("100");
////$req->setStartAuctionCount("123");
////$req->setEndAuctionCount("200");
////$req->setPlatform("1");
////$req->setPageNo("1");
////$req->setPageSize("20");
//
//$resp = $c->execute($req);
//
//var_dump($resp);

//http://auth.open.taobao.com/?appkey=23116944

$id=$_GET['id'];
$c = new TopClient;
$c->appkey ='23225630';// '23528053';
$c->secretKey = '93fee8926d98bfb23c05628f701c4b0d';//6f92f6511833c1dd97cafc355b81c2a9
$req = new TbkPrivilegeGetRequest;
$req->setItemId($id);
$req->setAdzoneId("44112315"); //B pid 第三位
$req->setPlatform("1");
$req->setSiteId("12146127");//A pid 第二位
$resp = $c->execute($req, '700001003374d0f399aaa79a390c7f684d2a274650e12c89011a1a8c605442d9d13b89f390951785');

var_dump($resp);
















?>