@extends('admin.layouts.app')

@section("content")

<script type="text/javascript">
  $(function(){
    setNav(".{{$menu_name}}");
  });
</script>

<script src="/admin_resource/plugins/Nestable/jquery.nestable.js"></script>
<script src="/admin_resource/plugins/underscore-min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    {{$title}}
  </h1>
</section>

<!-- Main content -->
<section class="content">
              <div class="box">
                <div class="box-header">
                </div><!-- /.box-header -->
                <div class="box-body">
                  <div class="col-md-6">
                    <div class="box">
                        <div class="box-header">
                          <a href="javascript:;" class="pull-right" id="addMenu"><i class="fa fa-plus"></i>添加</a>                        
                        </div>
                        <div class="box-body">
                          <div class="dd" id="wxmenu">
                              <ol class="dd-list wx-menu">
                              </ol>
                          </div>
                        </div><!-- /.box-body -->

                        <div class="box-footer">
                          <button class="btn btn-primary sub">保存</button>
                        </div>                      
                    </div>
                  </div>

                 <!--了解觅行导航链接设置-->
				          @if($menu_name == 'menu_about')
                  <div class="col-md-6">
                    <div class="box">
                        <div class="box-body">
            							了解觅行导航默认链接                       	
            							<input type="text" class="form-control" id="link" value="{{$menu_about_page_link}}">
                        </div><!-- /.box-body -->
                        <div class="box-footer">
                          <button class="btn btn-primary save_link">保存</button>
                        </div>                      
                    </div>
                  </div>
                  @endif
                 <!--/了解觅行导航链接设置-->

                </div><!-- /.box-body -->
              </div><!-- /.box -->

</section><!-- /.content -->
    <script type="text/template" id="menu_tpl">
      <li class="dd-item">

          <div class="dd-handle dd-handle">Drag</div>
          <div class="dd-content">
          <div class="box box-default box-solid <%=open?'':'collapsed-box'%>">

            <div class="box-header with-border">
              <h3 class="box-title"><%=name%></h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool remove"><i class="fa fa-times"></i></button>
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa <%=open?'fa-minus':'fa-plus'%>"></i></button>
              </div><!-- /.box-tools -->
            </div>                                  

            <div class="box-body">
                <div class="form-group">
                  <label>菜单名称</label>
                  <input type="text" class="form-control name" placeholder="菜单名称" value="<%=name%>">
                </div>

                <div class="form-group">
                  <label>链接</label>
                  <input type="text" class="form-control link" placeholder="链接" value="<%=link%>">
                </div>

            </div><!-- / .box-body -->
          </div><!-- /.box -->
          </div>
      <% if(sub_menu){%>
      </li>
      <%}%>
  </script>

    <!-- page script -->
    <script type="text/javascript">
      var menulist = {!!json_encode($menu_data)!!};
      var menuname = "{{$menu_name}}";

      $(function () {
        var menu_tpl = _.template($("#menu_tpl").html());
        
        $('#wxmenu').nestable({
          'maxDepth':2,
          'group':1
        });

        function init_menu(){
            var menuInsert ="";
            if(!menulist){
              return;
            }
            $.each(menulist.button, function(){
              var self = this;
              menuInsert += menu_tpl({name: self.name, link:self.url, sub_menu: 0, open: 0});

              if (this.sub_button) {
                menuInsert += '<ol class="dd-list">';
                $.each(this.sub_button, function(){
                  menuInsert += menu_tpl({name: this.name, link:this.url, sub_menu: 1, open: 0});              
                });
                menuInsert += '</ol>';
              };

              menuInsert += '</li>';
            });
            
            $("#wxmenu .wx-menu").html(menuInsert);          
        }

        init_menu();

        $(document).on('click', '#addMenu', function(){
          $("#wxmenu .wx-menu").prepend(menu_tpl({name: "菜单名称", link:"", sub_menu: 1, open: 1}));
        });

        $(document).on('click', '.dd-item .remove', function(){
          if (confirm("确认删除该菜单，如果有二级菜单也会一并删除？")) {
            var item = $(this).parents(".dd-item").first();
            item.slideUp(function(){item.remove()});
          }
        });

        $(document).on('keyup', '.dd-item .name', function(){
          var title = $(this).parents(".dd-item").first().find(".box-title").first();
          title.html($(this).val());
        });


        $(document).on('click', '.sub', function(){
          var list = $('#wxmenu .wx-menu>.dd-item');
          var menu_data = {button:[]};
          var is_error = false;
          $.each(list, function(){
            var $self = $(this);
            var menu = {
              name: $self.find(".name").val()
            }

            //检查一级菜单名称长度
            var _name = menu.name;
            var _len = _name.length;
            if(_len == 0){
              $.simplyToast('请填写一级菜单名称!', 'danger');
              is_error = true;
              return false;
            }

            //有子菜单
            if($self.find(".dd-list").length){
                  var sub_list = $self.find(".dd-list>.dd-item");
                  menu.sub_button = [];

                  //添加子菜单
                  $.each(sub_list, function(){
                    var $self = $(this);
                    var sub_menu = {
                      name: $self.find(".name").val(),
                      url : $self.find(".link").val(),
                    }
                    //检查一级菜单名称长度
                    var _name = sub_menu.name;
                    var _len = _name.length;
                    if(_len == 0){
                      $.simplyToast('请填写二级菜单名称!', 'danger');
                      is_error = true;
                      return false;
                    }

                    menu.sub_button.push(sub_menu);
                  })                

            }else{
              menu.url = $self.find(".link").val()
            }
            
            menu_data.button.push(menu);
          })

          if (is_error) {
            return;
          } 

          $.post("{{route('admin.menu.save')}}", {menu: JSON.stringify(menu_data), menu_name: menuname}, function(data){
            if (data.code == 200) {
                $.simplyToast('保存成功!', 'success');
            }else{
                $.simplyToast('操作失败，请重试!', 'danger');
            }

          });            

        });


		$(document).on("click", ".save_link", function(){
			var link = $("#link").val();
			$.post("/admin/menu/saveAboutPageLink", {link: link}, function(resp){
	            if (resp.code == 200) {
	                $.simplyToast('保存成功!', 'success');
	            }else{
	                $.simplyToast('操作失败，请重试!', 'danger');
	            }				
			});
		});

      });


      
    </script>


<style type="text/css">
.box{
  margin-top: 5px;
  margin-bottom: 5px;
  margin-left: 0;
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
  box-shadow: 1px 1px 1px rgba(0,0,0,0.1);
}

.dd { position: relative; display: block; margin: 0; padding: 0; max-width: 600px; list-style: none; font-size: 13px; line-height: 20px; }

.dd-list { display: block; position: relative; margin: 0; padding: 0; list-style: none; }
.dd-list .dd-list { padding-left: 30px; }
.dd-collapsed .dd-list { display: none; }

.dd-item,
.dd-empty,
.dd-placeholder { display: block; position: relative; margin: 0; padding: 0; min-height: 20px; font-size: 13px; line-height: 20px; }


.dd-handle:hover {cursor:move;}

.dd-item > button { display: block; position: relative; cursor: pointer; float: left; width: 25px; height: 20px; margin: 5px 0; padding: 0; text-indent: 100%; white-space: nowrap; overflow: hidden; border: 0; background: transparent; font-size: 12px; line-height: 1; text-align: center; font-weight: bold; }
.dd-item > button:before { content: '+'; display: block; position: absolute; width: 100%; text-align: center; text-indent: 0; }
.dd-item > button[data-action="collapse"]:before { content: '-'; }

.dd-placeholder{ margin: 5px 0; padding: 0; min-height: 30px; background: #f2fbff; border: 1px dashed #b6bcbf; box-sizing: border-box; -moz-box-sizing: border-box; }

.dd-dragel { position: absolute; pointer-events: none; z-index: 9999; }
.dd-dragel > .dd-item .dd-handle { margin-top: 0; }
.dd-dragel .dd-handle {
    -webkit-box-shadow: 2px 4px 6px 0 rgba(0,0,0,.1);
            box-shadow: 2px 4px 6px 0 rgba(0,0,0,.1);
}

.dd-handle { position: absolute; margin: 0; left: 0; top: 0; cursor: pointer; width: 40px; height: 43px; line-height: 40px; text-indent: 100%; white-space: nowrap; overflow: hidden;
  border: 1px solid #d2d6de;
      box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    background: #d2d6de;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}
.dd-handle:before { content: '≡'; display: block; text-indent: 0; color: #fff; text-align: center; font-size: 30px; font-weight: normal; }
.dd-handle:hover { background: #ddd; }
.dd-content{
  padding-left:40px;
}
</style>
@endsection


