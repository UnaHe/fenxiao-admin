@extends('admin.layouts.app')

@section("content")

<script type="text/javascript">
  $(function(){
    setNav(".order");
  });
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    订单管理
  </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="box">
        <div class="box-body">
            <form id="form">
                <div class="row col-sm-12">
                    <div class="row col-sm-12">
                        <label class="pull-left name">订单号</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm" name="order_no">
                        </div>

                        <label class="pull-left name">商品名称</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm" name="goods_title">
                        </div>

                        <label class="pull-left name">订单状态</label>
                        <div class="col-sm-2">
                            <select class="form-control" name="order_state">
                                <option value="">不限</option>
                                <?php foreach ($orderStates as $state=>$name):?>
                                    <option value="<?=$state?>"><?=$name?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>

                    <div class="row col-sm-12" style="margin-top: 5px">
                        <label class="pull-left name">订单用户</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm" name="order_user_id">
                        </div>

                        <label class="pull-left name">返利用户</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm" name="user_id">
                        </div>

                        <label class="pull-left name">返利状态</label>
                        <div class="col-sm-2">
                            <select class="form-control" name="deal_state">
                                <option value="">不限</option>
                                <option value="1">未返利</option>
                                <option value="2">已返利</option>
                            </select>
                        </div>

                    </div>

                    <div class="row col-sm-12" style="margin-top: 5px">
                        <label class="pull-left name">创建时间</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm select-time" name="create_time_start">
                        </div>
                        <label class="pull-left">至</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm select-time" name="create_time_end">
                        </div>
                    </div>

                    <div class="row col-sm-12" style="margin-top: 5px">
                        <label class="pull-left name">结算时间</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm select-time" name="settle_time_start">
                        </div>
                        <label class="pull-left">至</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm select-time" name="settle_time_end">
                        </div>
                    </div>

                    <div class="row col-sm-12" style="margin-top: 5px">
                        <label class="pull-left name">返利时间</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm select-time" name="deal_time_start">
                        </div>
                        <label class="pull-left">至</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm select-time" name="deal_time_end">
                        </div>
                    </div>

                    <div class="col-sm-1" style="margin-top: 10px">
                        <a class="btn btn-block btn-default btn-flat" id="search">搜索</a>
                    </div>
                </div>
            </form>

            <table id="orderList" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>淘宝订单号</th>
                    <th>订单状态</th>
                    <th>商品名称</th>
                    <th>返利用户</th>
                    <th>付款金额</th>
                    <th>效果预估</th>
                    <th>结算金额</th>
                    <th>结算预估</th>
                    <th>创建时间</th>
                    <th>结算时间</th>
                    <th>返利时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section><!-- /.content -->

<script type="text/template" id="tpl-order-detail">
    @include("admin.order.tpl_order_detail")
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
            "sAjaxSource": "/order/list",
            'columns':[
                {'data': 'order_no'},
                {'data': 'order_state_str'},
                {
                    "data": "goods_title",
                    "render": function(data, type, full) {
                        return "<div class='goods_name'><a href='http://item.taobao.com/item.htm?id="+full.goods_id+"' target='_blank'>"+data+"</></div>";
                    }
                },
                {'data': 'mobile'},
                {
                    "data": "pay_money",
                    "render": function(data, type, full) {
                        return "¥"+data;
                    }
                },
                {
                    "data": "predict_money",
                    "render": function(data, type, full) {
                        return "¥"+data;
                    }
                },
                {
                    "data": "settle_money",
                    "render": function(data, type, full) {
                        return "¥"+data;
                    }
                },
                {
                    "data": "predict_income",
                    "render": function(data, type, full) {
                        return "¥"+data;
                    }
                },
                {'data': 'create_time'},
                {'data': 'settle_time'},
                {'data': 'deal_time'},
                {
                    "data": "id",
                    "render": function(data, type, full) {
                        return "<a href='javascript:;' class='tools detail' data-id='" + data + "' title='查看详情'>详情</a>";
                    }
                },
            ]
        };
        $('#orderList').dataTable($.extend({}, dataTable_param, table_param));

        //搜索
        $(document).on('click', '#search', function(){
            search_param = $("#form").serializeArray();
            $("#orderList").dataTable().fnDraw();
        });

        //查看详情
        $(document).on("click", ".detail", function () {
            var loading = layer.load(1, {
                shade: [0.3,'#000']
            });
            $.get("/order/detail", {order_id: $(this).data("id")}, function (resp) {
                layer.close(loading);
                if(resp.code == 200){
                    layer.open({
                        type: 1,
                        anim: 2,
                        maxWidth:1000,
                        shadeClose: false,
                        title: "订单详情",
                        content: _.template($("#tpl-order-detail").html())(resp.data),
                        btn: ['关闭']
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

    form label.name{
        display: inline-block;
        width: 70px;
        text-align: right;
    }

    .goods_name{
        width: 300px;
    }

</style>
@endsection

