@extends('admin.layouts.app')

@section("content")

<script type="text/javascript">
  $(function(){
    setNav(".notice");
  });
</script>

<section class="content-header">
    <h1>公告管理</h1>
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
                        <th>公告内容</th>
                        <th>开始时间</th>
                        <th>结束时间</th>
                        <th>操作</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div><!-- /.box-body -->
	</div><!-- /.box -->
</section><!-- /.content -->

<script type="text/template" id="tpl-edit">
    @include("admin.notice.tpl_edit_notice")
</script>


<script src="/admin_resource/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="/admin_resource/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="/admin_resource/plugins/jQuery.cxCalendar-1.5.3/css/jquery.cxcalendar.css">
<script src="/admin_resource/plugins/jQuery.cxCalendar-1.5.3/js/jquery.cxcalendar.min.js" type="text/javascript"></script>

<script type="text/javascript">
      window.g_data = [];

$(function () {

    var table_param = {
      "sAjaxSource": "/notice/list",
      "iDisplayLength": 10,
      'columns':[
        {'data': 'title'},
        {'data': 'start_time'},
        {'data': 'end_time'},
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
        var time_api1;
        var time_api2;

        if(!data){
            data = {
                id: '',
                title: '',
                start_time: '',
                end_time: '',
            };
        }


        layer.open({
            type: 1,
            anim: 2,
            maxWidth:1000,
            shadeClose: false,
            title: "编辑",
            content: _.template($("#tpl-edit").html())(data),
            btn: ['保存', '关闭'],
            success: function(layero, index){
                $(".select-time1").cxCalendar({
                    type: 'datetime',
                    format: 'YYYY-MM-DD HH:mm:ss',
                    baseClass: 'select-timer'
                }, function(api){
                    time_api1 = api;
                });
                $(".select-time2").cxCalendar({
                    type: 'datetime',
                    format: 'YYYY-MM-DD HH:mm:ss',
                    baseClass: 'select-timer'
                }, function(api){
                    time_api2 = api;
                });
            },
            yes: function(index, layero){
                time_api1.hide();
                time_api2.hide();

                var loading = layer.load(1, {
                    shade: [0.3,'#000']
                });

                $.post("/notice/save", $("#edit-form").serialize(), function (resp) {
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
                time_api1.hide();
                time_api2.hide();
            },
            cancel: function () {
                time_api1.hide();
                time_api2.hide();
            }
        });
    });

    //删除
    $(document).on('click', '.delete', function(){
        var id = $(this).data("id");
        layer.confirm("确定删除？", function () {
            var loading = layer.load(1, {
                shade: [0.3,'#000']
            });

            $.post('/notice/del', {id: id}, function(res){
                layer.close(loading);
                if (res.code == 200) {
                    layer.msg("操作成功",{icon:1});
                    $("#list").dataTable().fnDraw(false);
                }else{
                    layer.msg(res.msg,{icon:5});
                }
            }).fail(function(){
                layer.msg("网络错误",{icon:5});
            });
        })
    });

});

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

    .select-timer{
        z-index: 20000000;
    }

</style>

@endsection

