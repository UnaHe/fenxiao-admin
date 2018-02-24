@extends('admin.layouts.app')

@section("content")

<script type="text/javascript">
  $(function(){
    setNav(".system_config");
  });
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    系统配置
  </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="box">
        <div class="box-header bg-gray color-palette">
            <button class="btn btn-primary pull-right save-config">保存配置</button>
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="col-md-6">
                <form role="form" id="form" class="form-horizontal" onsubmit="return false;">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">分享文案</label>
                        <div class="col-md-10">
                            <textarea class="form-control" name="share_desc" style="height: 300px;"><?=$configs['share_desc']['value']?></textarea>
                            <div class="form-tips">
                                分享到社交渠道的文案模板，支持变量：商品名称{title}，原价{price}，券后价{used_price}，优惠券金额{coupon_price}，销量{sell_num}，描述{description}，淘口令{tao_code}，微信单页地址{wechat_url}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">微信展示类型</label>
                        <div class="col-md-10">
                            <select class="form-control" name="wechat_show_type">
                                <option value="1" <?=$configs['wechat_show_type']['value'] == 1?'selected=true':''?>>域名方式</option>
                                <option value="2" <?=$configs['wechat_show_type']['value'] == 2?'selected=true':''?>>快站</option>
                                <option value="3" <?=$configs['wechat_show_type']['value'] == 3?'selected=true':''?>>百度翻译</option>
                            </select>
                            <div class="form-tips">分享微信单页展示类型</div>
                        </div>
                    </div>

                    <div class="form-group" style="height: 1000px;">
                        <label class="col-sm-2 control-label">新浪短域名</label>
                        <div class="col-md-10">
                            <select class="form-control" name="sina_short_url">
                                <option value="1" <?=$configs['sina_short_url']['value'] == 1?'selected=true':''?>>开启</option>
                                <option value="0" <?=$configs['sina_short_url']['value'] == 0?'selected=true':''?>>关闭</option>
                            </select>
                            <div class="form-tips">选择是否开启新浪短域名服务</div>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section><!-- /.content -->

<script src="/admin_resource/plugins/jquery.pinBox.min.js" type="text/javascript"></script>
<style type="text/css">
    .form-tips{
        color: #666;
        padding: 5px 0;
    }
</style>

<script type="text/javascript">
  $(function () {
      $(".box-header").pinBox({
          Top : '50px',
          Container : '.content',
      });

      $(document).on('click', '.save-config', function(){
          var loading = layer.load(1, {
              shade: [0.3,'#000']
          });
          $.post("/system_config/save", $("#form").serialize(), function(data){
              if (data.code == 200) {
                  layer.close(loading);
                  layer.msg("保存成功",{icon:1});
              }else{
                  layer.close(loading);
                  layer.msg("保存失败",{icon:5});
              }
          }).fail(function () {
              layer.close(loading);
              layer.msg("保存失败",{icon:5});
          });
      });

  });
</script>


@endsection