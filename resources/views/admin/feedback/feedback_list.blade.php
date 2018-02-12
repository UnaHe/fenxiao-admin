@extends('admin.layouts.app')

@section("content")

<script type="text/javascript">
  $(function(){
    setNav(".feedback");
  });
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    意见反馈管理
  </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">意见反馈管理</h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <form id="form">
                <div class="">
                    <div class="row col-sm-12">
                        <label class="pull-left name">手机号</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm" name="mobile">
                        </div>

                        <div class="col-sm-1">
                            <a class="btn btn-block btn-default btn-flat" id="search">搜索</a>
                        </div>
                    </div>
                </div>
            </form>
            <table id="list" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>反馈内容</th>
                        <th>用户</th>
                        <th>反馈时间</th>
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
        "sAjaxSource": "/feedback/list",
        'columns':[
            {'data': 'content'},
            {'data': 'mobile'},
            {'data': 'create_time'},
        ]
    };
    $('#list').dataTable($.extend({}, dataTable_param, table_param));

    //搜索
    $(document).on('click', '#search', function(){
        search_param = $("#form").serializeArray();
        $("#list").dataTable().fnDraw();
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

