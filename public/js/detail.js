var loadEle = ".detail-load-failed";
var contentEle = ".detail-content";
var mskKeyBox= ".detail-mask-command-box";
var detailKeyBox= ".detail-command-box";
var digBtn = ".dialog .copy-tao-words-btn";
var loading = true;
var taoKeyNum = "";
var clipboard = null;
var timer = null;


//加载详情
function loadData() {
    layer.closeAll();
    if(loading == false) { return } //阻止多次请求加载事件
    loading = false;
    $.ajax({
        url:"http://hws.m.taobao.com/cache/mtop.wdetail.getItemDescx/4.1/?data=%7B%22item_num_id%22%3A%22"+window.goods_id+"%22%7D&type=jsonp",
        dataType: "jsonp",
        timeout: 3000,
        success : function (data) {
            var imgData = data.data.images;
            var imgLen = imgData.length;
            var imgArr = [];
            for(var i = 0; i < imgLen; i++) {
                imgArr.push('<img class="lazy" src="/images/default-pic2.jpg" data-img="'+imgData[i]+'" alt="">');
            }
            $("#content").html(imgArr);
            $(loadEle).hide();
            $(contentEle).fadeIn(300);
            loading = true;
        },
        error: function () {
            layer.msg("加载失败");
            $(loadEle).show();
            $(loadEle+" a").text("点击重新加载");
            loading = true;
        }
    });
}

//是否是微信浏览器
function isWeixn(){
    var ua = navigator.userAgent.toLowerCase();
    if(ua.match(/MicroMessenger/i)=="micromessenger") {
        return true;
    } else {
        return false;
    }
}

//打开提示框
function openTips() {
    if(!isWeixn()){
        window.location.href = redirect_url;
        return;
    }
    var a  = oneKeyCopy(13);
    if(a != null) {
        a.clipboardAction.trigger.disabled = false;
        clearTimeout(timer);
    }
    $(digBtn).addClass("copied-sta");
    $(digBtn).text("一键复制");
    if(isIOS()) {
        $(".detail-mask-command-ios").show();
        $(mskKeyBox+" textarea").hide();
        $(mskKeyBox+" span").show();
    } else {
        $(".detail-mask-command-android").show();
        $(mskKeyBox+" span").hide();
        $(mskKeyBox+" textarea").show();
    }
    $(".dialog").fadeIn(100);
    //判断打开淘口令时  页面不滚动
    document.body.addEventListener('touchmove', function (event) {
        if($(".dialog").css("display") == "block") {
            event.preventDefault();
        }
    }, false);
}

function iosOpen() {
    window.location.href="https://t.asczwa.com/taobao?backurl="+encodeURIComponent(redirect_url);
}

//文字输入
function iptNum(ths,sta) {
    if(sta) {
        taoKeyNum = ths.value;
    }
    if(ths.value != taoKeyNum) {
        ths.value = taoKeyNum;
    }
}

//判断是安卓还是苹果
function isIOS() {
    var ua = navigator.userAgent.toLowerCase();
    if (/iphone|ipad|ipod/.test(ua)) {
        return true;
    } else if (/android/.test(ua)) {
        return false;
    }
}

//一键复制代码
var fontSize;
function oneKeyCopy(num) {
    //一键复制功能 -- 待整理
    var isShow = 0;//控制是否显示一键复制按钮
    var ua = navigator.userAgent.toLowerCase();
    fontSize = num;

    if(ua.match(/iphone/i) == "iphone"){
        //获取iOS版本
        var iphoneInfo = ua.match(/iphone os (\d{1,})/i);
        var iosVersion = iphoneInfo[1];
        //ipad或IOS10以上显示一键复制按钮
        if(iosVersion >= 10 || ua.match(/ipad/i) == 'ipad') {
            isShow = 1;
        } else {
            $(".media-tkl").show();
        }
    } else {
        isShow = 1;
    }

    if(isShow == 1) {
        $('.copy-tao-words').show();
        $('.self-copy-area').hide();
        $(".copy-tao-words .copy-tao-words-btn").on("click", function () {
            clipboard = new Clipboard('.copy-tao-words-btn', {
                //动态设置复制内容
                text:function(trigger) {
                    return trigger.getAttribute('data-taowords');
                }
            });

            clipboard.on('success', function(e){
                if(e.trigger.disabled == false || e.trigger.disabled == undefined) {
                    e.trigger.innerHTML="复制成功，请打开【手机淘宝】购买";
                    e.trigger.style.backgroundColor="#9ED29E";
                    e.trigger.style.borderColor="#9ED29E";
                    e.clearSelection();
                    e.trigger.disabled = true;
                    if(fontSize != undefined) { //判断为 蒙版显示成功字体为 12px
                        e.trigger.style.fontSize = fontSize + "px";
                        $(digBtn).removeClass("copied-sta");

                        //10秒后按钮恢复原状
                        timer = setTimeout(function() {
                            e.trigger.innerHTML="一键复制";
                            e.trigger.style.backgroundColor="#f54d23";
                            e.trigger.style.borderColor="#f54d23";
                            e.trigger.style.fontSize = "16px";
                            e.trigger.disabled = false;
                        },10000);
                    } else {
                        setTimeout(function() {
                            e.trigger.innerHTML="一键复制";
                            e.trigger.style.backgroundColor="#f54d23";
                            e.trigger.style.borderColor="#f54d23";
                            e.trigger.style.fontSize = "16px";
                            e.trigger.disabled = false;
                        },10000);
                    }
                }
            });

            clipboard.on('error', function(e) {
                e.trigger.innerHTML="复制失败";
                e.trigger.style.backgroundColor="#8f8f8f";
                e.trigger.style.borderColor="#8f8f8f";
                setTimeout(function() {
                    $(".copy-tao-words").hide();
                    $(".media-tkl").show();
                    $('.self-copy-area').show();
                },2000);
            });
        });
    }
    return clipboard;
}


//$(function () {
    //详情加载失败点击
    $(".detail-load-failed").on("click", function () {
        loadData();
    });

    //详情页面图片懒加载
    var imgLoad = lazyload.init({
        anim:true,
        selectorName:".lazy",
        extend_height: 200
    });

    //关闭蒙版
    $(".detail-mask").on("click", function () {
        $(".dialog").fadeOut(100,function () {
            $(".detail-mask-command-ios, .detail-mask-command-android").hide();
        });
    });

    //苹果安卓复制文案
    document.addEventListener("selectionchange", function (e) {
        if (window.getSelection().anchorNode.parentNode.id == 'code1_ios' && document.getElementById('code1_ios').innerText != window.getSelection()) {
            var key = document.getElementById('code1_ios');
            window.getSelection().selectAllChildren(key);
        }
        if (window.getSelection().anchorNode.parentNode.id == 'code2_ios' && document.getElementById('code2_ios').innerText != window.getSelection()) {
            var key = document.getElementById('code2_ios');
            window.getSelection().selectAllChildren(key);
        }
    }, false);

    //判断安卓苹果显示
    if(isIOS()) {
        $(detailKeyBox+" textarea").hide();
        $(detailKeyBox+" input").hide();
        $(detailKeyBox+" span").show();
    } else {
        $(detailKeyBox+" span").hide();
        $(detailKeyBox+" textarea").show();
        $(detailKeyBox+" input").show();
    }

    $(detailKeyBox+" input").on("click", function () {
        $(this).querySelectorAll();
    });

    //一键复制
    oneKeyCopy();

//});