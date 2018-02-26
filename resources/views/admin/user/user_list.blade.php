@extends('admin.layouts.app')

@section("content")

<script type="text/javascript">
  $(function(){
    setNav(".member");
  });
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    用户管理
  </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">注册用户</h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <form id="form">
                <div class="">
                    <div class="row col-sm-12">
                        <label class="pull-left name">手机号</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm" name="mobile">
                        </div>

                        <label class="pull-left name">注册时间</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm select-time" name="reg_time_start">
                        </div>
                        <label class="pull-left">至</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm select-time" name="reg_time_end">
                        </div>
                    </div>

                    <div class="row col-sm-12" style="margin-top: 10px;">
                        <label class="pull-left name">PID</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm" name="pid">
                        </div>

                        <label class="pull-left name">邀请码</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control input-sm" name="invite_code">
                        </div>

                        <label class="pull-left name">用户等级</label>
                        <div class="col-sm-1">
                            <select class="form-control state"  name="grade">
                                <option value="">不限</option>
                                <?php foreach ($grades as $grade):?>
                                <option value="<?=$grade['grade']?>"><?=$grade['grade_name']?></option>
                                <?php endforeach;?>
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

<script type="text/template" id="tpl-user-detail">
    @include("admin.user.tpl_user_detail")
</script>

<script type="text/template" id="tpl-send-message">
    @include("admin.message.tpl_edit_message")
</script>


<script type="text/template" id="tpl-user-tree">
    <div style="width: 600px; height:500px;padding: 20px;">
        <div class="user-tree-info"></div>
        <ul id="user-tree" class="ztree"></ul>
    </div>
</script>


<!-- DATA TABES SCRIPT -->
<script src="/admin_resource/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="/admin_resource/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>

<link rel="stylesheet" href="/admin_resource/plugins/jQuery.cxCalendar-1.5.3/css/jquery.cxcalendar.css">
<script src="/admin_resource/plugins/jQuery.cxCalendar-1.5.3/js/jquery.cxcalendar.min.js" type="text/javascript"></script>

<link rel="stylesheet" href="/admin_resource/plugins/zTree_v3/css/zTreeStyle/zTreeStyle.css">
<script src="/admin_resource/plugins/zTree_v3/js/jquery.ztree.core.min.js" type="text/javascript"></script>

<!-- page script -->
<script type="text/javascript">
$(function () {
    $(".select-time").cxCalendar({
        type: 'datetime',
        format: 'YYYY-MM-DD HH:mm:ss',
    });

    var table_param = {
        "sAjaxSource": "/user/list",
        'columns':[
            {'data': 'id'},
            {'data': 'mobile'},
            {'data': 'grade_str'},
            {'data': 'balance'},
            {'data': 'reg_time'},
            {
                "data": "id",
                "render": function(data, type, full) {
                    return "<a href='javascript:;' class='tools detail' data-id='" + data + "' title='查看'>查看</a>"
                        +"<a href='javascript:;' class='tools user_tree' data-type='parent' data-id='" + data + "' title='编辑'>上级用户</a>"
                        +"<a href='javascript:;' class='tools user_tree' data-type='children' data-id='" + data + "' title='编辑'>下级用户</a>"
                        +"<a href='javascript:;' class='tools send_message' data-mobile='" + full.mobile + "' title='编辑'>发送通知</a>";
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
        $.get("/user/detail", {user_id: $(this).data("id")}, function (resp) {
            layer.close(loading);
            if(resp.code == 200){
                layer.open({
                    type: 1,
                    anim: 2,
                    maxWidth:1000,
                    shadeClose: false,
                    title: "用户信息",
                    content: _.template($("#tpl-user-detail").html())(resp.data),
                    btn: ['保存', '关闭'],
                    yes: function(index, layero){
                        var loading = layer.load(1, {
                            shade: [0.3,'#000']
                        });
                        $.post("/user/save", $("#edit-form").serialize(), function (resp) {
                            if(resp.code == 200){
                                layer.closeAll();
                                layer.msg("保存成功",{icon:1});
                                setTimeout(function () {
                                    $("#userList").dataTable().fnDraw();
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
            }else{
                layer.msg("查询失败，请重试");
            }
        }).fail(function () {
            layer.msg("查询失败，请重试");
        });
    });
    
    //上级用户
    $(document).on("click", ".user_tree", function () {
        var user_id = $(this).data("id");

        var setting = {
            view: {
                fontCss: getFont,
                showIcon: showIconForTree
            },
            data: {
                simpleData: {
                    enable: true
                }
            }
        };

        function getFont(treeId, node) {
            return node.id == user_id ? {color:'red'} : {};
        }

        function showIconForTree(treeId, treeNode) {
            return false;
        };

        $(".user-tree-info").html("");
        $.fn.zTree.destroy("#user-tree");

        var loading = layer.load(1, {
            shade: [0.3,'#000']
        });
        $.get("/user/tree", {user_id: $(this).data("id"), type:$(this).data('type')}, function (resp) {
            layer.close(loading);
            if(resp.code == 200){
                layer.open({
                    type: 1,
                    anim: 2,
                    maxWidth:1000,
                    shadeClose: false,
                    title: "用户关系",
                    content: $("#tpl-user-tree").html(),
                    btn: ['关闭']
                });
                $(".user-tree-info").html(resp.data.user_grade_str);
                $.fn.zTree.init($("#user-tree"), setting, resp.data.user_list);
            }else{
                layer.msg("查询失败，请重试");
            }
        }).fail(function () {
            layer.msg("查询失败，请重试");
        });
    });


    //发送通知
    $(document).on('click', '.send_message', function(){
        var mobile = $(this).data('mobile');
        var data = {
            id: '',
            title: '',
            content: '',
            mobile: mobile,
        };

        layer.open({
            type: 1,
            anim: 2,
            maxWidth:1000,
            shadeClose: false,
            title: "发送通知",
            content: _.template($("#tpl-send-message").html())(data),
            btn: ['发送', '关闭'],
            yes: function(index, layero){
                var loading = layer.load(1, {
                    shade: [0.3,'#000']
                });

                $.post("/message/save", $("#send-message-form").serialize(), function (resp) {
                    if(resp.code == 200){
                        layer.closeAll();
                        layer.msg("发送成功",{icon:1});
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

