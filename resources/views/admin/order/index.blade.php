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

                  <div class="row col-sm-12">                    
                    
                    <div class="row1">
                        <label class="pull-left">订单号：</label>
                        <div class="col-sm-2">
                          <input type="text" class="form-control input-sm" id="search_order_id">                      
                        </div>

                        <label class="pull-left">线路名称：</label>
                        <div class="col-sm-2">
                          <input type="text" class="form-control input-sm" id="search_order_name">                      
                        </div>

                        <label class="pull-left">用户名称：</label>
                        <div class="col-sm-2">
                          <input type="text" class="form-control input-sm" id="search_user_name">                      
                        </div>
                        
                    </div>

                    <div class="row2">
                        <label class="pull-left">联系人：</label>
                        <div class="col-sm-2">
                          <input type="text" class="form-control input-sm" id="search_contact_name">                      
                        </div>

                        <label class="pull-left">联系电话：</label>
                        <div class="col-sm-2">
                          <input type="text" class="form-control input-sm" id="search_contact_mobile">                      
                        </div>

                        <label class="pull-left">订单状态：</label>
                        <div class="col-sm-2">
                            <select class="form-control state" id="search_order_state">
                                <option value="0">待支付</option>
                                <option value="1" selected>已支付</option>
                                <option value="2">已消费</option>
                            </select>
                        </div>


                        <div class="col-sm-1">
                          <button class="btn btn-block btn-default btn-flat" id="search">搜索</button>
                        </div>                        
                    </div>

                  </div>

                    <table id="userList" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>用户ID</th>
                            <th>手机号</th>
                            <th>等级</th>
                            <th>余额</th>
                            <th>注册时间</th>
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
      window.g_data = [];

      $(function () {
        var dialog = $(".edit-dialog");

        var table_param = {
          "sAjaxSource": "{{route('admin.orderlist.list')}}",
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
                if (full.state == 1) {
                    return "<a href='javascript:;' class='detail' data-id='" + data + "' >消费</a>";                  
                }
                return "<a href='javascript:;' class='detail' data-id='" + data + "'>详细信息</a>";
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

            var search_user_name = {};
            search_user_name.name = "search_user_name";
            search_user_name.value = $("#search_user_name").val();
            search_param.push(search_user_name);

            var search_order_state = {};
            search_order_state.name = "search_order_state";
            search_order_state.value = $("#search_order_state").val();
            search_param.push(search_order_state);          

            var search_contact_name = {};
            search_contact_name.name = "search_contact_name";
            search_contact_name.value = $("#search_contact_name").val();
            search_param.push(search_contact_name);

            var search_contact_mobile = {};
            search_contact_mobile.name = "search_contact_mobile";
            search_contact_mobile.value = $("#search_contact_mobile").val();
            search_param.push(search_contact_mobile);          

        }

        $(document).on('click', '.detail', function(){
          var id = $(this).data('id');
          var data = g_data[id];
          var $list = dialog.find(".list");

          $list.find(".id").html(data['order_no']);
          $list.find(".user_id").html(data['user_id']);
          $list.find(".user_name").html(data['user_name']);
          $list.find(".body").html(data['name']);
          $list.find(".total_price").html(data['total_price']);
          $list.find(".balance").html(data['balance']);
          $list.find(".price").html(data['price']);
          $list.find(".contact_name").html(data['contact_name']);
          $list.find(".contact_mobile").html(data['contact_mobile']);
          $("#id").val(id);
          var state = data['state'];

          if(state == 1){
              dialog.find(".consume").show();            
          }else{
              dialog.find(".consume").hide();                        
          }

          var state_txt =  getOrderState(state);
          $list.find(".state").html(state_txt);
          $list.find(".create_at").html(data['created_at']);
          dialog.modal();
        });

        $(document).on('click', '.consume', function(){
          var id = $("#id").val();
          if(!id){
            $.simplyToast('系统错误，请刷新重试！', 'danger');
            return;
          }
          if (!confirm("确认消费？")) {
            return;
          }

          $.post("/admin/order/setState", {id: id, state: 2}, function(resp){
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
  .row1, .row2{
    overflow: hidden;
  }
  .row2{
    margin-top: 10px;
  }
</style>

@endsection

