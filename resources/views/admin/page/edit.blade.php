@extends('admin.layouts.app')
@section("content")

<script type="text/javascript">
  $(function(){
    setNav("{{$page_class}}");
  });
</script>
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          	{{$page_title or '编辑页面'}}
          </h1>
        </section>


        <!-- Main content -->
        <section class="content">
        	<div class="box">
        		
	          <input type="hidden" id="id"  value="{{$page['id'] or ''}}">
	          <input type="hidden" id="alias"  value="{{$alias or ''}}">

	          <div class="box-body">
	            <div class="form-group">
	              <label for="title">标题</label>
	              <input type="text" class="form-control" id="title" placeholder="请输入标题" value="{{$page['title'] or ''}}">
	            </div>
								
				<div class="form-group">
	              <label>内容</label>
	              <script type="text/plain" id="content" style="width:100%;height:360px;">{!!$page['content'] or ''!!}</script>
	            </div>
	          </div><!-- /.box-body -->

	          <div class="box-footer">
	            <button type="submit" class="btn btn-primary save">保存</button>
	          </div>
        	</div>

        </section><!-- /.content -->

    <script type="text/javascript" charset="utf-8" src="/admin_resource/plugins/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/admin_resource/plugins/ueditor/ueditor.all.min.js"> </script>
    <script type="text/javascript" charset="utf-8" src="/admin_resource/plugins/ueditor/lang/zh-cn/zh-cn.js"></script>


    <script type="text/javascript">
      $(function () {
	      	 var um = UE.getEditor('content');

	        $(document).on("click", ".save", function(resp){
	        	var $btn = $(this);
	        	var data = {
	        		id: $("#id").val(),
	        		alias: $("#alias").val(),
	        		title: $("#title").val(),
	        		content: um.getContent()
	        	};
	        	$btn.prop("disabled", true).html("保存中...");
		        $.post("{{route("admin.page.save")}}", data, function(resp){
		        	$btn.prop("disabled", false).html("保存");
	                if (resp.code == 200) {
	                	$("#id").val(resp.data.id);
	                    $.simplyToast('操作成功!', 'success');
	                }else{
	                    $.simplyToast('操作失败，请重试!', 'danger');
	                }
		        });

	        });



      });
    </script>

@endsection
