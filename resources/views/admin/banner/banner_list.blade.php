@extends('admin.layouts.app')

@section("content")

<script type="text/javascript">
  $(function(){
    setNav(".banner");
  });
</script>

<section class="content-header">
    <h1>Banner管理</h1>
</section>

<!-- Main content -->
<section class="content">
	<div class="box">
		<div class="box-header">
			<a href="javascript:;" class="btn btn-primary edit pull-right">新增</a>
		</div>
		<div class="box-body">
			<table id="list" class="table table-bordered table-hover">
				<thead>
					<tr>
                        <th>标题</th>
                        <th>图片</th>
                        <th>链接地址</th>
                        <th>显示位置</th>
                        <th>操作</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div><!-- /.box-body -->
	</div><!-- /.box -->
</section><!-- /.content -->

<script type="text/template" id="tpl-edit">
    @include("admin.banner.tpl_edit_banner")
</script>


<script src="/admin_resource/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="/admin_resource/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>
<script src="/admin_resource/plugins/plupload-2.1.8/plupload.full.min.js" type="text/javascript"></script>

<script type="text/javascript">
      window.g_data = [];

$(function () {

    var table_param = {
      "sAjaxSource": "/banner/list",
      "iDisplayLength": 10,
      'columns':[
        {'data': 'name'},
        {
          "data": "pic",
          "render": function(data, type, full) {
                return "<div class='banner_img_wrap'><a href='"+data+"' target='_blank' title='点击查看大图'><img src='"+data+"' class='banner_img'/></a></div>"
          }
        },
        {'data': 'click_url'},
        {'data': 'position'},
        {
          "data": "id",
          "render": function(data, type, full) {
            return "<a href='javascript:;' class='edit fa fa-pencil-square-o' data-id='" + data + "' title='编辑'>编辑</a>"+"&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:;' class='delete fa fa-close' data-id='" + data + "' title='删除'>删除</a>";
          }
        },
      ]
    };

    $('#list').dataTable($.extend({}, dataTable_param, table_param));


    $(document).on('click', '.edit', function(){
        var id = $(this).data('id');
        var data = g_data[id];
        if(!data){
            data = {
                id: '',
                name: '',
                click_url: '',
                position: '',
                pic:''
            };
        }

        layer.open({
            type: 1,
            anim: 2,
            maxWidth:1000,
            shadeClose: false,
            title: "编辑banner",
            content: _.template($("#tpl-edit").html())(data),
            btn: ['保存', '关闭'],
            success: function(layero, index){
                window.uploader = new plupload.Uploader({
                    browse_button : 'add_pic', // you can pass an id...
                    url : '{{route("admin.upload")}}',

                    filters : {
                        max_file_size : '5mb',
                        mime_types: [
                            {title : "Image files", extensions : "jpg,jpeg,bmp,gif,png"},
                        ]
                    },

                    file_data_name: 'upfile',

                    init: {
                        FilesAdded: function(up, files) {
                            plupload.each(files, function(file) {
                                previewImage(file,function(imgsrc){
                                    uploader.start();
                                });
                            });
                        },
                        UploadProgress: function(up, file) {
                            $("#add_pic").html("上传中...("+file.percent+"%)");
                        },
                        FileUploaded: function(uploader,file,responseObject){
                            var data = eval('('+responseObject.response+')');
                            if (data.code == 200) {
                                $("#pic").attr("src", data.data);
                                $("#pic_val").val(data.data);
                            }else{
                                layer.msg('图片上传失败，请重试!',{icon:5});
                            }
                            $("#add_pic").html("上传图片");
                        },
                        Error: function(up, err) {
                            var msg = "上传失败";
                            if (err.status == 413) {
                                msg = "文件太大";
                            }
                            layer.msg(msg,{icon:5});
                        }
                    }
                });
                uploader.init();
            },
            yes: function(index, layero){
                var loading = layer.load(1, {
                    shade: [0.3,'#000']
                });

                $.post("/banner/save", $("#edit-form").serialize(), function (resp) {
                    if(resp.code == 200){
                        layer.closeAll();
                        layer.msg("保存成功",{icon:1});
                        setTimeout(function () {
                            $("#list").dataTable().fnDraw();
                        }, 500);
                    }else{
                        layer.close(loading);
                        layer.msg("保存失败",{icon:5});
                    }
                }).fail(function () {
                    layer.close(loading);
                    layer.msg("保存失败",{icon:5});
                });
            },
            btn2:function(index){
                layer.closeAll();
            }
        });
    });

    //删除
    $(document).on('click', '.delete', function(){
        var id = $(this).data("id");
        layer.confirm("确定删除？", function () {
            $.post('/banner/del', {id: id}, function(res){
                if (res.code == 200) {
                    layer.msg("操作成功",{icon:1});
                    $("#list").dataTable().fnDraw(false);
                }else{
                    layer.msg(res.msg,{icon:5});
                }
            });
        })
    });

});



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

    #pic{
        display: block;
        margin-bottom: 20px;
        max-width: 500px;
        max-height: 300px;
    }

    .banner_img_wrap{
        width: 500px;
    }
    .banner_img{
        max-width: 500px;
        max-height:300px;
    }
</style>

@endsection

