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
                <div class="box-header">
                </div><!-- /.box-header -->
                <div class="box-body">
                  <div class="col-md-6">
                  <form role="form" id="form" onsubmit="return false;">
                    <div class="box-body">

                      <div class="form-group">
                        <label>分享文案</label>
                        <textarea class="form-control" name="share_desc" style="height: 300px;"><?=$configs['share_desc']['value']?></textarea>
                      </div>

                      <div class="form-group">
                        <label>微信域名展示类型</label>
                        <select class="form-control" name="wechat_show_type">
                            <option value="1" <?=$configs['wechat_show_type']['value'] == 1?'selected=true':''?>>域名方式</option>
                            <option value="2" <?=$configs['wechat_show_type']['value'] == 2?'selected=true':''?>>快站</option>
                            <option value="3" <?=$configs['wechat_show_type']['value'] == 3?'selected=true':''?>>百度翻译</option>
                        </select>
                      </div>

                      <div class="form-group">
                        <label>是否开启新浪短域名</label>
                        <select class="form-control" name="sina_short_url">
                          <option value="1" <?=$configs['sina_short_url']['value'] == 1?'selected=true':''?>>开启</option>
                          <option value="0" <?=$configs['sina_short_url']['value'] == 0?'selected=true':''?>>关闭</option>
                        </select>
                      </div>

                    </div><!-- /.box-body -->

                    <div class="box-footer">
                      <button type="submit" class="btn btn-primary save">确认</button>
                    </div>
                  </form>
                  </div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->

</section><!-- /.content -->


<script type="text/javascript">
  $(function () {

      $(document).on('click', '.save', function(){
          $.post("/system_config/save", $("#form").serialize(), function(data){
              if (data.code == 200) {
                $.simplyToast('保存成功!', 'success');
              }
          });
      });

  });
</script>


@endsection