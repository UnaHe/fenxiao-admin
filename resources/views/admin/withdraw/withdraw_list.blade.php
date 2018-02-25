@extends('admin.layouts.app')

@section("content")

<script type="text/javascript">
  $(function(){
    setNav(".withdraw");
  });
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    提现申请
  </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="box">
        <div class="box-header">
        </div><!-- /.box-header -->
        <div class="box-body">
            <form id="form">
                <div class="">
                    <div class="row col-sm-12">
                        <label class="pull-left name">手机号</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm" name="mobile">
                        </div>

                        <label class="pull-left name">申请时间</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm select-time" name="start_time">
                        </div>
                        <label class="pull-left">至</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm select-time" name="end_time">
                        </div>

                        <label class="pull-left name">处理状态</label>
                        <div class="col-sm-1">
                            <select class="form-control"  name="status">
                                <option value="">不限</option>
                                <option value="0">未处理</option>
                                <option value="1">已成功</option>
                                <option value="2">已拒绝</option>
                            </select>
                        </div>

                        <div class="col-sm-1">
                            <a class="btn btn-block btn-default btn-flat" id="search">搜索</a>
                        </div>
                    </div>

                </div>
            </form>
            <table id="userList" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>手机号</th>
                        <th>提现金额</th>
                        <th>状态</th>
                        <th>申请时间</th>
                        <th>处理时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section><!-- /.content -->

<script type="text/template" id="tpl-withdraw-detail">
    @include("admin.withdraw.tpl_withdraw_detail")
</script>

<!-- DATA TABES SCRIPT -->
<script src="/admin_resource/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="/admin_resource/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>

<link rel="stylesheet" href="/admin_resource/plugins/jQuery.cxCalendar-1.5.3/css/jquery.cxcalendar.css">
<script src="/admin_resource/plugins/jQuery.cxCalendar-1.5.3/js/jquery.cxcalendar.min.js" type="text/javascript"></script>

<!-- page script -->
<script type="text/javascript">
$(function () {
    $(".select-time").cxCalendar({
        type: 'datetime',
        format: 'YYYY-MM-DD HH:mm:ss',
    });

    var table_param = {
        "sAjaxSource": "/withdraw/list",
        'columns':[
            {'data': 'mobile'},
            {'data': 'amount'},
            {'data': 'status_str'},
            {'data': 'add_time'},
            {'data': 'deal_time'},
            {
                "data": "id",
                "render": function(data, type, full) {
                    return "<a href='javascript:;' class='tools detail' data-id='" + data + "' title='查看'>查看</a>";
                }
            },
        ]
    };
    $('#userList').dataTable($.extend({}, dataTable_param, table_param));

    //搜索
    $(document).on('click', '#search', function(){
        search_param = $("#form").serializeArray();
        $("#userList").dataTable().fnDraw();
    });

    //查看详情
    $(document).on("click", ".detail", function () {
        var loading = layer.load(1, {
            shade: [0.3,'#000']
        });
        var withdraw_id = $(this).data("id");
        $.get("/withdraw/detail", {id: withdraw_id}, function (resp) {
            layer.close(loading);
            if(resp.code == 200){
                layer.open({
                    type: 1,
                    anim: 2,
                    maxWidth:1000,
                    shadeClose: false,
                    title: "提现详情",
                    content: _.template($("#tpl-withdraw-detail").html())(resp.data),
                    yes: function(index, layero){
                        layer.confirm("确认已经打款给用户？",{icon:3}, function () {
                            var loading = layer.load(1, {
                                shade: [0.3,'#000']
                            });
                            $.post("/withdraw/confirm", {id: withdraw_id}, function (resp) {
                                if(resp.code == 200){
                                    layer.closeAll();
                                    layer.msg("操作成功",{icon:1});
                                    setTimeout(function () {
                                        $("#userList").dataTable().fnDraw();
                                    }, 500);
                                }else{
                                    layer.close(loading);
                                    layer.msg("操作失败，请重试",{icon:5});
                                }
                            }).fail(function () {
                                layer.close(loading);
                                layer.msg("操作失败，请重试",{icon:5});
                            });
                        });
                    },
                    btn2:function(index){
                        layer.confirm("确认拒绝用户申请？",{icon:3}, function () {
                            var loading = layer.load(1, {
                                shade: [0.3,'#000']
                            });
                            $.post("/withdraw/refuse", {id: withdraw_id}, function (resp) {
                                if(resp.code == 200){
                                    layer.closeAll();
                                    layer.msg("操作成功",{icon:1});
                                    setTimeout(function () {
                                        $("#userList").dataTable().fnDraw();
                                    }, 500);
                                }else{
                                    layer.close(loading);
                                    layer.msg("操作失败，请重试",{icon:5});
                                }
                            }).fail(function () {
                                layer.close(loading);
                                layer.msg("操作失败，请重试",{icon:5});
                            });
                        });
                        return false;
                    }
                });
            }else{
                layer.msg("查询失败，请重试");
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

