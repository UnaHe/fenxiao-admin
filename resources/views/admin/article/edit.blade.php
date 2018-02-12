@extends('admin.layouts.app')
@section("content")

<script type="text/javascript">
  $(function(){
    setNav(".article_article");
  });
</script>
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          	编辑户外文化
          </h1>
        </section>


        <!-- Main content -->
        <section class="content">
        	<div class="box">
	          <input type="hidden" id="id"  value="{{$article['id'] or ''}}">

	          <div class="box-body">
	            <div class="form-group">
	              <label for="title">标题</label>
	              <input type="text" class="form-control" id="title" placeholder="请输入标题" value="{{$article['title'] or ''}}">
	            </div>

	            <div class="form-group">
	              <label for="title">分类</label>
	              <select id="category">
	            		@foreach($category as $cate)
	            			<option value="{{$cate['id']}}" {{$category_id == $cate['id'] ? "selected" : ""}}>{{$cate['name']}}</option>
	            		@endforeach
	              </select>
	            </div>
				
				<div class="row">
					<div class="form-group col-sm-6">
						<label for="name">主图<span style="color:#999">(建议尺寸 宽度不大于300像素)</span></label>
						<div>
							<img src="{{$article['main_pic'] or ''}}" class="pic" id="main_pic" style="max-width: 200px;max-height: 400px;display: block;margin-bottom: 20px;"/>
							<div id="add_pic" class="btn btn-info">上传图片</div>				
						</div>
					</div>						
				</div>

				<div class="form-group">
	              <label>内容</label>
	              <script type="text/plain" id="content" style="width:100%;height:360px;">{!!$article['content'] or ''!!}</script>
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


    <script src="/admin_resource/plugins/plupload-2.1.8/plupload.full.min.js" type="text/javascript"></script>

    <script type="text/javascript">
      $(function () {
	        var um = UE.getEditor('content');
          
	        $(document).on("click", ".save", function(resp){
	        	var $btn = $(this);
	        	var data = {
	        		id: $("#id").val(),
	        		title: $("#title").val(),
	        		category_id : $("#category").val(),
	        		main_pic: $("#main_pic").attr("src"),
	        		content: um.getContent()
	        	};
	        	$btn.prop("disabled", true).html("保存中...");
		        $.post("{{route("admin.article.save")}}", data, function(resp){
		        	$btn.prop("disabled", false).html("保存");
	                if (resp.code == 200) {
	                    $.simplyToast('操作成功!', 'success');
	                	if (typeof resp.data.id != 'undefined') {
		                	$("#id").val(resp.data.id);	                		
	                	}
	                }else{
	                    $.simplyToast('操作失败，请重试!', 'danger');
	                }
		        });

	        });



      });

  window.uploader = new plupload.Uploader({
      runtimes : 'html5,flash,silverlight,html4',
      browse_button : 'add_pic', // you can pass an id...
//      container: document.getElementById('add_pic_wrap'), // ... or DOM Element itself
      drop_element: 'drop_pic',
      url : '{{route("admin.upload")}}',
      flash_swf_url : './Moxie.swf',
      silverlight_xap_url : './Moxie.xap',
      
      filters : {
          max_file_size : '5mb',
          mime_types: [
              {title : "Image files", extensions : "jpg,gif,png,jpeg"},
          ]
      },

      file_data_name: 'upfile',

      //压缩图片
      resize: {
        width: 300,
        height: 1000,
        crop: true,
        preserve_headers: false
      },

      init: {
          PostInit: function() {
          },

          FilesAdded: function(up, files) {
              // var tpl = _.template($("#pic_tpl").html());
              // var pics_list = $(".pics .list .piclist");

              plupload.each(files, function(file) {
                  previewImage(file,function(imgsrc){
//                    pics_list.append(tpl({id: file.id, img:imgsrc, main:0}));       
                          uploader.start();
                  });           
              });
          },

          UploadProgress: function(up, file) {
              $("#add_pic").html("上传中...("+file.percent+"%)");
//            $("#"+file.id).find(".p").html(file.percent + "%");
          },

          FileUploaded: function(uploader,file,responseObject){
            var data = eval('('+responseObject.response+')');
            if (data.state == "SUCCESS") {
              $("#main_pic").attr("src", "/"+data.url);
            }else{
              $.simplyToast('图片上传失败，请重试!', 'danger');
            }
            $("#add_pic").html("上传图片");

          },

          //移除事件
          FilesRemoved: function(uploader,files){
          },

          Error: function(up, err) {

            var msg = "上传失败";
            if (err.status == 413) {
              msg = "文件太大";
            }
            $.simplyToast(msg, 'danger');
          }
      }
  });

  uploader.init();

  function previewImage(file,callback){//file为plupload事件监听函数参数中的file对象,callback为预览图片准备完成的回调函数
    if(!file || !/image\//.test(file.type)) return; //确保文件是图片
    if(file.type=='image/gif'){//gif使用FileReader进行预览,因为mOxie.Image只支持jpg和png
      var fr = new mOxie.FileReader();
      fr.onload = function(){
        callback(fr.result);
        fr.destroy();
        fr = null;
      }
      fr.readAsDataURL(file.getSource());
    }else{
      var preloader = new mOxie.Image();
      preloader.onload = function() {
        preloader.downsize( 300, 300 );//先压缩一下要预览的图片,宽300，高300
        var imgsrc = preloader.type=='image/jpeg' ? preloader.getAsDataURL('image/jpeg',80) : preloader.getAsDataURL(); //得到图片src,实质为一个base64编码的数据
        callback && callback(imgsrc); //callback传入的参数为预览图片的url
        preloader.destroy();
        preloader = null;
      };
      preloader.load( file.getSource() );
    } 
  }


    </script>

@endsection
