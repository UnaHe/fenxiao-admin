@extends('admin.layouts.app')

@section("content")

<script type="text/javascript">
  $(function(){
    setNav(".user_bill");
  });
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
      账单管理
  </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">账单管理</h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <form id="form">
                <div class="">
                    <div class="row col-sm-12">
                        <label class="pull-left name">用户</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm" name="mobile">
                        </div>

                        <label class="pull-left name">收支类型</label>
                        <div class="col-sm-1">
                            <select class="form-control state"  name="type">
                                <option value="">不限</option>
                                <option value="1">收入</option>
                                <option value="0">支出</option>
                            </select>
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
                        <th>用户</th>
                        <th>金额</th>
                        <th>收支类型</th>
                        <th>收支备注</th>
                        <th>发生时间</th>
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
        "sAjaxSource": "/user_bill/list",
        'columns':[
            {'data': 'mobile'},
            {'data': 'amount'},
            {
                'data': 'type',
                'render': function (data) {
                    return data == 0 ? "支出": "收入";
                }
            },
            {'data': 'comment'},
            {'data': 'add_time'},
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

