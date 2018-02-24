@extends('admin.layouts.app')

@section("content")

<script type="text/javascript">
  $(function(){
    setNav(".grade");
  });
</script>

<section class="content-header">
    <h1>等级管理</h1>
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
                        <th>等级名称</th>
                        <th>等级排序</th>
                        <th>直推返利</th>
                        <th>平行返利</th>
                        <th>升级条件</th>
                        <th>操作</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div><!-- /.box-body -->
	</div><!-- /.box -->
</section><!-- /.content -->

<script type="text/template" id="tpl-edit">
    @include("admin.grade.tpl_edit_grade")
</script>

<script src="/admin_resource/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="/admin_resource/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>

<script type="text/javascript">
  window.g_data = {};

$(function () {

    var table_param = {
      "sAjaxSource": "/grade/list",
      "iDisplayLength": 10,
      'columns':[
        {'data': 'grade_name'},
        {'data': 'sort'},
        {
            "data": "rate",
            "render": function(data, type, full) {
                var rate_str="";
                if(data){
                    _.each(data.split(";"), function(rate){
                        if(rate){
                            var rate_info = rate.split(":");
                            if(rate_info[0] == 0){
                                rate_str += "自买"+rate_info[1]+"；";
                            }else{
                                rate_str += "直推"+rate_info[0]+"级"+rate_info[1]+"；";
                            }
                        }
                    });
                }

                return rate_str;
            }
        },
        {
          "data": "same_rate",
          "render": function(data, type, full) {
              var rate_str="";
              if(data){
                  _.each(data.split(";"), function(rate){
                      if(rate){
                          var rate_info = rate.split(":");
                          rate_str += "平行"+rate_info[0]+"级"+rate_info[1]+"；";
                      }
                  });
              }

              return rate_str;
          }
        },
        {
            "data": "child_grade_name",
            "render": function(data, type, full) {
                if(full.child_grade_num){
                    return full.child_grade_name+"("+full.child_grade_num+"个)";
                }else{
                    return "";
                }
            }
        },
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

        if(!data){
            data = {
                id: '',
                grade_name: '',
                grade: "",
                child_grade: "",
                child_grade_num: 0,
                sort: 0,
                rate: "",
                same_rate: "",
                find_parent_level: 0,
                find_same_level: 0,
            };
        }

        var rates = {
            rate_0: 0,
            rate_1: 0,
            rate_2: 0,
        };

        var same_rates = {
            same_rate_1: 0,
            same_rate_2: 0,
        };

        if(data.rate){
            _.each(data.rate.split(";"), function(rate){
                if(rate){
                    var rate_info = rate.split(":");
                    rates["rate_"+rate_info[0]] = rate_info[1];
                }
            });
        }

        if(data.same_rate){
            _.each(data.same_rate.split(";"), function(rate){
                if(rate){
                    var rate_info = rate.split(":");
                    same_rates["same_rate_"+rate_info[0]] = rate_info[1];
                }
            });
        }

        data.same_rates = same_rates;
        data.rates = rates;

        layer.open({
            type: 1,
            anim: 2,
            maxWidth:1000,
            shadeClose: false,
            title: "编辑",
            content: _.template($("#tpl-edit").html())({data:data, grades: g_data}),
            btn: ['保存', '关闭'],
            yes: function(index, layero){
                var loading = layer.load(1, {
                    shade: [0.3,'#000']
                });

                $.post("/grade/save", $("#edit-form").serialize(), function (resp) {
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

            $.post('/grade/del', {id: id}, function(res){
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

</style>

@endsection

