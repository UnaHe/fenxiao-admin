@extends('admin.layouts.app')

@section("content")

<script type="text/javascript">
  $(function(){
    setNav(".remittance");
  });
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    汇款管理
  </h1>
</section>

<!-- Main content -->
<section class="content">
              <div class="box">
                <div class="box-body">

                  <div class="row col-sm-12">                    
                    
                    <label class="pull-left">订单号：</label>
                    <div class="col-sm-2">
                      <input type="text" class="form-control input-sm" id="search_order_id">                      
                    </div>

                    <label class="pull-left">线路名称：</label>
                    <div class="col-sm-2">
                      <input type="text" class="form-control input-sm" id="search_order_name">                      
                    </div>

                    <label class="pull-left">联系人：</label>
                    <div class="col-sm-2">
                      <input type="text" class="form-control input-sm" id="search_contact_name">                      
                    </div>

                    <label class="pull-left">联系电话：</label>
                    <div class="col-sm-2">
                      <input type="text" class="form-control input-sm" id="search_contact_mobile">                      
                    </div>

                    <div class="col-sm-1">
                      <button class="btn btn-block btn-default btn-flat" id="search">搜索</button>
                    </div>

                  </div>

                  <table id="example2" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>订单号</th>
                        <th style="width: 200px;">线路名称</th>
                        <th>人数</th>
                        <th>联系人</th>
                        <th>联系电话</th>
                        <th>总价</th>
                        <th>订单状态</th>
                        <th>订单创建时间</th>
                        <th>操作</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
</section><!-- /.content -->

<div class="modal edit-dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">订单详情</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" id="id">
        <ul class="list">
            <li><label>订单号:</label><span class="id"></span></li>
            <li><label>用户id</label><span class="user_id"></span></li>
            <li><label>用户名</label><span class="user_name"></span></li>
            <li><label>联系人</label><span class="contact_name"></span></li>
            <li><label>联系电话</label><span class="contact_mobile"></span></li>
            <li><label>商品名称</label><span class="body"></span></li>
            <li><label>总价格</label><span class="price"></span></li>
            <li><label>订单状态</label><span class="state"></span></li>
            <li><label>订单创建时间</label><span class="create_at"></span></li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary auditpass">确认收到汇款</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

    <!-- DATA TABES SCRIPT -->
    <script src="/admin_resource/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="/admin_resource/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>

    <!-- page script -->
    <script type="text/javascript">
      window.g_data = [];

      $(function () {
        var dialog = $(".edit-dialog");

        var table_param = {
          "sAjaxSource": "{{route('admin.remittance.list')}}",
          'columns':[
            {'aTargets': [0], 'data': 'order_no'},
            {'aTargets': [1], 'data': 'name'},
            {'aTargets': [2], 'data': 'production_number'},
            {'aTargets': [3], 'data': 'contact_name'},
            {'aTargets': [4], 'data': 'contact_mobile'},
            {'aTargets': [5], 'data': 'price'},
            {
              "targets": [6],
              "data": "state",
              "render": function(data, type, full) {
                return getOrderState(data);
              }
            },

            {'aTargets': [7], 'data': 'created_at'},
            {
              "targets": [8],
              "data": "id",
              "render": function(data, type, full) {
                return "<a href='javascript:;' class='audit' data-id='" + data + "'>收到汇款</a>";
              }
            },
          ]
        };

        set_param();
        $('#example2').dataTable($.extend({}, dataTable_param, table_param));

        //搜索
        $(document).on('click', '#search', function(){
            set_param();
            $("#example2").dataTable().fnDraw();
        });        

        //设置查询参数
        function set_param(){
            search_param = [];
            var search_order_id = {};
            search_order_id.name = "search_order_id";
            search_order_id.value = $("#search_order_id").val();
            search_param.push(search_order_id);

            var search_order_name = {};
            search_order_name.name = "search_order_name";
            search_order_name.value = $("#search_order_name").val();
            search_param.push(search_order_name);

            var search_contact_name = {};
            search_contact_name.name = "search_contact_name";
            search_contact_name.value = $("#search_contact_name").val();
            search_param.push(search_contact_name);

            var search_contact_mobile = {};
            search_contact_mobile.name = "search_contact_mobile";
            search_contact_mobile.value = $("#search_contact_mobile").val();
            search_param.push(search_contact_mobile);          
        }

        $(document).on('click', '.audit', function(){
          var id = $(this).data('id');
          var data = g_data[id];
          var $list = dialog.find(".list");

          $("#id").val(id);
          $list.find(".id").html(data['order_no']);
          $list.find(".user_id").html(data['user_id']);
          $list.find(".user_name").html(data['user_name']);
          $list.find(".body").html(data['name']);
          $list.find(".total_price").html(data['total_price']);
          $list.find(".balance").html(data['balance']);
          $list.find(".price").html(data['price']);
          $list.find(".contact_name").html(data['contact_name']);
          $list.find(".contact_mobile").html(data['contact_mobile']);


          var state = data['state'];
          var state_txt = getOrderState(state);

          $list.find(".state").html(state_txt);
          $list.find(".create_at").html(data['created_at']);
          dialog.modal();
        });

        $(document).on('click', '.auditpass', function(){
          var id = $("#id").val();
          if(!id){
            $.simplyToast('系统错误，请刷新重试！', 'danger');
            return;
          }
          if (!confirm("确认收到汇款并审核通过？")) {
            return;
          }

          $.post("/admin/order/setState", {id: id, state: 1}, function(resp){
                if(resp.code == 200){
                    $.simplyToast('操作成功!', 'success');
                      dialog.modal("hide");
                      $("#example2").dataTable().fnDraw();
                }else{
                    $.simplyToast('操作失败，请重试!', 'danger');
                }
          });
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
</style>

@endsection

