@extends('admin.layouts.app')

@section("content")

<script type="text/javascript">
  $(function(){
    setNav(".failed_jobs");
  });
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
      失败任务管理
  </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="box">
        <div class="box-header">
        </div><!-- /.box-header -->
        <div class="box-body">
            <table id="list" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>连接</th>
                        <th>队列</th>
                        <th>任务</th>
                        <th>异常</th>
                        <th>失败时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section><!-- /.content -->


<!-- DATA TABES SCRIPT -->
<script src="/admin_resource/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="/admin_resource/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>

<!-- page script -->
<script type="text/javascript">
$(function () {

    var table_param = {
        "sAjaxSource": "/failed_jobs/list",
        'columns':[
            {'data': 'id'},
            {'data': 'connection'},
            {'data': 'queue'},
            {'data': 'displayName'},
            {
                'data': 'exception_msg',
                'render': function (data) {
                    return data;
                }
            },
            {'data': 'failed_at'},
            {
                'data': 'id',
                'render': function (data) {
                    return "<a href='javascript:;' class='retry' data-id='"+data+"'>重试</a>";
                }
            },
        ]
    };
    $('#list').dataTable($.extend({}, dataTable_param, table_param));

    //重试任务
    $(document).on("click", ".retry", function () {
        var loading = layer.load(1, {
            shade: [0.3,'#000']
        });

        $.post("/failed_jobs/retry", {id: $(this).data("id")}, function (resp) {
            layer.close(loading);
            if(resp.code == 200){
                layer.closeAll();
                layer.msg("刷新成功",{icon:1});
                setTimeout(function () {
                    $("#list").dataTable().fnDraw();
                }, 500);
            }else{
                layer.msg(resp.msg);
            }
        }).fail(function () {
            layer.msg("查询失败，请重试");
        });
    });
});



</script>


<style type="text/css">
    .tools{
        margin-right: 10px;
    }

    label small{
        color: #666;
        margin-left:10px;
    }
    form label.name{
        display: inline-block;
        width: 70px;
        text-align: right;
    }
</style>
@endsection

