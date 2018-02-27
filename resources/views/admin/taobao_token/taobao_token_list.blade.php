@extends('admin.layouts.app')

@section("content")

<script type="text/javascript">
  $(function(){
    setNav(".taobaotoken");
  });
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
      系统授权管理
  </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">系统授权管理</h3>
            <a href="javascript:;" class="btn btn-primary edit pull-right">添加授权账号</a>
        </div><!-- /.box-header -->
        <div class="box-body">
            <form id="form">
                <div class="">
                    <div class="row col-sm-12">
                        <label class="pull-left name">联盟ID</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm" name="member_id">
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
                        <th>联盟ID</th>
                        <th>淘宝昵称</th>
                        <th>淘宝用户id</th>
                        <th>过期时间</th>
                        <th>刷新过期时间</th>
                        <th>更新时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section><!-- /.content -->

<script type="text/template" id="tpl-edit">
    @include("admin.taobao_token.tpl_edit_token")
</script>


<!-- DATA TABES SCRIPT -->
<script src="/admin_resource/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="/admin_resource/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>

<!-- page script -->
<script type="text/javascript">
$(function () {

    var table_param = {
        "sAjaxSource": "/taobaotoken/list",
        'columns':[
            {'data': 'member_id'},
            {'data': 'taobao_user_nick'},
            {'data': 'taobao_user_id'},
            {'data': 'expires_at'},
            {'data': 're_expires_at'},
            {'data': 'update_time'},
            {
                'data': 'id',
                'render': function (data) {
                    return "<a href='javascript:;' class='tools refresh_token' data-id='"+data+"'>刷新授权</a>"
                        +"<a href='javascript:;' class='tools edit' data-id='"+data+"'>重新授权</a>"
                        +"<a href='javascript:;' class='tools delete' data-id='"+data+"'>删除</a>";
                }
            },
        ]
    };
    $('#list').dataTable($.extend({}, dataTable_param, table_param));

    //搜索
    $(document).on('click', '#search', function(){
        search_param = $("#form").serializeArray();
        $("#list").dataTable().fnDraw();
    });

    //刷新token
    $(document).on("click", ".refresh_token", function () {
        var loading = layer.load(1, {
            shade: [0.3,'#000']
        });

        $.post("/taobaotoken/refresh", {id: $(this).data("id")}, function (resp) {
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

    //编辑授权
    $(document).on('click', '.edit', function(){
        var id = $(this).data('id');
        var data = g_data[id];

        if(!data){
            data = {
                id: '',
                member_id: '',
            };
        }


        layer.open({
            type: 1,
            anim: 2,
            maxWidth:1000,
            shadeClose: false,
            title: "编辑授权",
            content: _.template($("#tpl-edit").html())(data),
            btn: ['保存', '关闭'],
            yes: function(index, layero){
                var loading = layer.load(1, {
                    shade: [0.3,'#000']
                });

                $.post("/taobaotoken/save", $("#edit-form").serialize(), function (resp) {
                    if(resp.code == 200){
                        layer.closeAll();
                        layer.msg("保存成功",{icon:1});
                        setTimeout(function () {
                            $("#list").dataTable().fnDraw();
                        }, 500);
                    }else{
                        layer.close(loading);
                        layer.msg(resp.msg,{icon:5});
                    }
                }).fail(function () {
                    layer.close(loading);
                    layer.msg("保存失败",{icon:5});
                });
            },
            btn2:function(index){
                layer.closeAll();
            },
        });
    });

    //删除
    $(document).on('click', '.delete', function(){
        var id = $(this).data("id");
        layer.confirm("确定删除？", function () {
            var loading = layer.load(1, {
                shade: [0.3,'#000']
            });

            $.post('/taobaotoken/del', {id: id}, function(res){
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

