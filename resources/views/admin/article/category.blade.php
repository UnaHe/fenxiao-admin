@extends('admin.layouts.app')

@section("content")

<script type="text/javascript">
  $(function(){
    setNav(".article_category");
  });
</script>


<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    户外文化主题管理
  </h1>
</section>

<!-- Main content -->
<section class="content">
	<div class="box">
		<div class="box-header">
			<a href="javascript:;" class="btn btn-primary add_tag pull-right">新增</a>
		</div>
		<div class="box-body">
			<table id="example2" class="table table-bordered table-hover">
				<thead>
					<tr>
					<th>ID</th>
					<th>主题名称</th>
					<th>操作</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div><!-- /.box-body -->
	</div><!-- /.box -->
</section><!-- /.content -->

<div class="modal edit-dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">编辑主题</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
			<label for="title">主题名称</label>
			<input type="text" class="form-control" id="name" placeholder="请输入主题名称">
        </div>

        <div class="form-group">
          <label for="title">图片<span style="color:#999">(建议尺寸 230x140像素)</span></label>
          <div>
            <img src="" class="pic" id="pic" style="display: block;margin-bottom: 10px;" width="230" height="140"/>
            <div id="add_pic" class="btn btn-info">上传图片</div>       
          </div>
        </div>


      </div>
      <div class="modal-footer">
      	<input type="hidden" id="id">
        <button type="button" class="btn btn-primary save">保存</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

    <!-- DATA TABES SCRIPT -->
    <script src="/admin_resource/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="/admin_resource/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>
    <script src="/admin_resource/plugins/plupload-2.1.8/plupload.full.min.js" type="text/javascript"></script>

    <!-- page script -->
    <script type="text/javascript">
      window.g_data = [];

      $(function () {
        var dialog = $(".edit-dialog");

        var table_param = {        
          "sAjaxSource": "{{route('admin.articleCategory.list')}}",
          "iDisplayLength": 10,
          'columns':[
            {'aTargets': [0], 'data': 'id', 'orderable': true},
            {'aTargets': [1], 'data': 'name'},
            {
              "targets": [2],
              "data": "id",
              "render": function(data, type, full) {
              	
                return "<a href='javascript:;' class='edit fa fa-pencil-square-o' data-id='" + data + "' title='编辑'>编辑</a>"+"&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:;' class='delete fa fa-close' data-id='" + data + "' title='删除'>删除</a>";
              }
            },
          ]
        };
        // var param = $.extend({}, dataTable_param, table_param);
        // console.log(param);
        $('#example2').dataTable($.extend({}, dataTable_param, table_param));

        //搜索
        $(document).on('click', '#search', function(){
            $("#example2").dataTable().fnDraw();
        });        

        
        $(document).on('click', '.add_tag', function(){
          dialog.find("#id").val("");
          dialog.find("#name").val("");
          dialog.find("#pic").attr("src", "");
          dialog.modal();
        });

        $(document).on('click', '.edit', function(){
          var id = $(this).data('id');
          var data = g_data[id];
          dialog.find("#id").val(data['id']);
          dialog.find("#name").val(data['name']);
          dialog.find("#pic").attr("src", data['pic']);
          dialog.modal();
        });

        //删除
        $(document).on('click', '.delete', function(){
            if(!confirm("确定删除？")){
              return;
            }          
            var id = $(this).data("id");
            $.post('{{route("admin.articleCategory.del")}}', {id: id}, function(res){
                    if (res.code == 200) {
                        $.simplyToast('操作成功!', 'success');
                        $("#example2").dataTable().fnDraw(false);
                    }else{
                        $.simplyToast(res.msg, 'danger');
                    }
                });
        });

        //保存
        dialog.on('click', '.save', function(){
        	var $btn = $(this);
        	var data = {
        		id: dialog.find("#id").val(),
        		name: dialog.find("#name").val(),
            pic: dialog.find("#pic").attr("src")
        	};
        	$btn.prop("disabled", true).html("保存中...");
            $.post('{{route("admin.articleCategory.save")}}', data, function(res){
    		    	$btn.prop("disabled", false).html("保存");
                    if (res.code == 200) {
                        $.simplyToast('保存成功!', 'success');
                        $("#example2").dataTable().fnDraw(false);
                        dialog.modal("hide");
                    }else{
                        $.simplyToast(res.msg, 'danger');
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
        width: 230,
        height: 140,
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
              $("#pic").attr("src", "/"+data.url);
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


<style type="text/css">
  .edit-dialog .list li{
    list-style: none;
  }

  .edit-dialog .list li label{
    text-align: right;
    width: 120px;
  }

  .edit-dialog .list li span{
    padding-left: 20px;
  }
</style>

@endsection

