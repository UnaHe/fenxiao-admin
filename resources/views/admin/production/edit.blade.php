@extends('admin.layouts.app')
@section("content")

<script type="text/javascript">
  $(function(){
    setNav(".product_product");
  });
</script>
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          	编辑产品
          </h1>
        </section>


        <!-- Main content -->
        <section class="content">

			<ul class="nav nav-tabs">
				<li class="active"><a href="#baseinfo" class="nav_tab baseinfo_tab">基本信息</a></li>
				<li><a href="#calendar_panel" class="nav_tab calendar_tab">价格日历</a></li>
			</ul>

            <div class="tab-content">
            	
				<div class="tab-pane active" id="baseinfo">
		        	<div class="box">		        		
			          <input type="hidden" id="id"  value="{{$production['id'] or ''}}">

			          <div class="box-body">

							<div class="row">
								<div class="form-group col-sm-6">
									<label for="name">产品名称</label>
									<input type="text" class="form-control" id="name" placeholder="请输入产品名称" value="{{$production['name'] or ''}}">
								</div>						
							</div>

							<div class="row">
								<div class="form-group col-sm-6">
									<label for="name">主图<span style="color:#999">(建议尺寸 500x500像素)</span></label>
									<div>
										<img src="{{$production['main_pic'] or ''}}" class="pic" id="main_pic" width="300" height="300"/>
										<div id="add_pic" class="btn btn-info">上传图片</div>				
									</div>
								</div>						
							</div>

							<div class="row">
								<div class="form-group col-sm-6">
									<label>简介</label>
									<textarea class="form-control" rows="3" id="summary" placeholder="请输入简介">{{$production['summary'] or ''}}</textarea>
								</div>						
							</div>

							<div class="row">
								<div class="form-group col-sm-6">
									<label for="name">集合地点</label>
									<input type="text" class="form-control" id="origin" placeholder="请输入集合地点" value="{{$production['origin'] or ''}}">
								</div>						
							</div>

							<div class="row">
								<div class="form-group col-sm-6">
									<label for="name">目的地</label>
									<input type="text" class="form-control" id="destination" placeholder="请输入目的地" value="{{$production['destination'] or ''}}">
								</div>						
							</div>

							<div class="row">
								<div class="form-group col-sm-6">
									<label for="name">天数</label>

		                            <div class="number-input">
		                                <a href="javascript:;" class="decr">-</a>
		                                <input type="number" class="input_num" name="person_num" id="day_info" value="{{$production['day_info'] or '1'}}">
		                                <a href="javascript:;" class="incr">+</a>
		                            </div>                     
								</div>						
							</div>


							<div class="row">
								<div class="form-group set_location">
									<label for="name">位置</label>	
									<div>
						                <ul class="list">
						                    <li class="item">
						                        <label class="radio is_abroad">
						                            <input type="radio" name="is_abroad" class="is_abroad" value="0" {{(isset($production['is_abroad']) && $production['is_abroad']==0) ? "checked" : ""}}>
						                            <span class="txt">国内游</span>
						                        </label>
						                    </li>
						                    <li class="item">
						                        <label class="radio is_abroad">
						                            <input type="radio" name="is_abroad" class="is_abroad" value="1" {{(isset($production['is_abroad']) && $production['is_abroad']==1) ? "checked" : ""}}>
						                            <span class="txt">国外游</span>
						                        </label>
						                    </li>

						                </ul>								
									</div>						
								</div>						
							</div>			

							<div class="row">
								<div class="form-group set_tag">
									<label for="name">设置产品标签</label>	
									<div>
						                <ul class="list">
										@foreach ($tags as $tag)
						                    <li class="item">
						                        <label class="checkbox tag">
						                            <input type="checkbox" name="tags[]" class="tagval" value="{{$tag->id}}" {{isset($selected_tag[$tag->id]) ? "checked" : ""}}>
						                            <span class="txt">{{$tag->name}}</span>
						                        </label>
						                    </li>
										@endforeach								
						                </ul>								
									</div>						
								</div>						
							</div>

						  <div class="row">
							  <div class="form-group col-sm-6">
								  <label for="name">销售状态</label>
								  <select id="is_online">
									  <option value="1" {{isset($production['is_online']) && $production['is_online'] == 1 ? 'selected="selected"' : ''}}>上线销售</option>
									  <option value="0" {{isset($production['is_online']) && $production['is_online'] == 0 ? 'selected="selected"' : ''}}>停售</option>
								  </select>
							  </div>
						  </div>

						  <div class="row">
							  <div class="form-group col-sm-6">
								  <label for="name">排序<small>(序号越大越靠前)</small></label>
								  <input type="number" class="form-control" id="sort" value="{{$production['sort'] or '1'}}">
							  </div>
						  </div>



						  <div class="form-group">
			              <label>景点介绍</label>
			              <script type="text/plain" id="description" style="width:100%;height:360px;">{!!$production['description'] or ''!!}</script>
			            </div>

						<div class="form-group">
			              <label>行程安排</label>
			              <script type="text/plain" id="schedule" style="width:100%;height:360px;">{!!$production['schedule'] or ''!!}</script>
			            </div>

						<div class="form-group">
			              <label>费用说明</label>
			              <script type="text/plain" id="price_info" style="width:100%;height:360px;">{!!$production['price_info'] or ''!!}</script>
			            </div>

						<div class="form-group">
			              <label>注意事项</label>
			              <script type="text/plain" id="notice" style="width:100%;height:360px;">{!!$production['notice'] or ''!!}</script>
			            </div>

						<div class="form-group">
			              <label>报名须知</label>
			              <script type="text/plain" id="buy_notice" style="width:100%;height:360px;">{!!$production['buy_notice'] or ''!!}</script>
			            </div>

			          </div><!-- /.box-body -->

			          <div class="box-footer">
			            <button type="submit" class="btn btn-info save">下一步</button>
			          </div>
		        	</div>
				</div><!-- /.tab-pane #baseinfo-->

				<div class="tab-pane" id="calendar_panel">
		        	<div class="box">		  
		        		<div id="calendar"></div>
		        	</div><!--/.box-->
				</div><!--/.tab-pane #calendar-->

            </div><!-- /.tab-content -->
        </section><!-- /.content -->

		
		<div class="modal edit-price-dialog">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">编辑价格</h4>
		      </div>
		      <div class="modal-body">
		        <div class="form-group">
					<label for="title">日期</label>
					<input type="text" class="form-control" id="edit_date" placeholder="请输入日期">
		        </div>
		        <div class="form-group">
					<label for="title">价格</label>
					<input type="number" class="form-control" id="edit_price" placeholder="请输入价格">
		        </div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-danger edit_delete" style="display:none">删除</button>
		        <button type="button" class="btn btn-primary edit_save">保存</button>
		        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
		      </div>
		    </div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</div><!-- /.modal -->


    <script type="text/javascript" charset="utf-8" src="/admin_resource/plugins/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/admin_resource/plugins/ueditor/ueditor.all.min.js"> </script>
    <script type="text/javascript" charset="utf-8" src="/admin_resource/plugins/ueditor/lang/zh-cn/zh-cn.js"></script>



    <link href="/admin_resource/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" />
    <link href="/admin_resource/plugins/fullcalendar/fullcalendar.print.css" rel="stylesheet" type="text/css" media='print' />
    <script src="/admin_resource/plugins/fullcalendar/moment.min.js" type="text/javascript"></script>    
    <script src="/admin_resource/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
    <script src="/admin_resource/plugins/plupload-2.1.8/plupload.full.min.js" type="text/javascript"></script>


    <script type="text/javascript">
      $(function () {
      		$(document).on("click", ".nav_tab", function(){
      			var $tab = $(this);
      			if ($tab.hasClass("baseinfo_tab")) {

      			}else if($tab.hasClass("calendar_tab")) {
      				if (!$("#id").val()) {
      					alert("请先保存基本信息");
      					$(".baseinfo_tab").tab("show");
      					return;
      				}
      				setTimeout(function(){
	  					$('#calendar').fullCalendar("today");      					
      				}, 100);
      			}

      			$tab.tab("show");
      		});

		    //数字输入控件
		    $(document).on("click", ".number-input .decr, .number-input .incr", function(){
		    	var $btn = $(this);
		    	var $input = $(this).parent(".number-input").find(".input_num");

		    	var number = parseInt($input.val());

		    	if($btn.hasClass("incr")){
		    		number++;
		    	}else{
			    	if (number<=1) {
			    		return;
			    	}
		    		number--;
		    	}
		    	$input.val(number);
		    });      		

	        var um_description = init_editor("description");
	        var um_price_info = init_editor("price_info");
	        var um_notice = init_editor("notice");
	        var um_buy_notice = init_editor("buy_notice");
	        var um_schedule = init_editor("schedule");

	        //初始化编辑器
	        function init_editor(id){
		        return UE.getEditor(id);
	        }

	        $(document).on("click", ".save", function(resp){
	        	var $btn = $(this);
	        	var $selected_tag = $(".tagval:checked");
	        	var selected_tag = [];
	        	$.each($selected_tag, function(){
	        		selected_tag.push($(this).val());
	        	});

	        	var data = {
	        		id: $("#id").val(),
					name: $("#name").val(),
					summary: $("#summary").val(),
					main_pic: $("#main_pic").attr("src"),
					origin: $("#origin").val(),
					destination: $("#destination").val(),
					day_info: $("#day_info").val(),
					is_abroad: $(".is_abroad:checked").val(),

					tags: selected_tag,
	        		description: um_description.getContent(),
	        		price_info: um_price_info.getContent(),
	        		notice: um_notice.getContent(),
	        		buy_notice: um_buy_notice.getContent(),
	        		schedule: um_schedule.getContent(),


	        		is_online: $("#is_online").val(),
	        		sort: $("#sort").val(),
	        	};

	        	$btn.prop("disabled", true).html("保存中...");
		        $.post("{{route("admin.production.save")}}", data, function(resp){
		        	$btn.prop("disabled", false).html("下一步");
	                if (resp.code == 200) {
	                	if (typeof resp.data.id != 'undefined') {
		                	$("#id").val(resp.data.id);	                		
	                	}
      					$(".calendar_tab").tab("show");
	      				setTimeout(function(){
		  					$('#calendar').fullCalendar("today");      					
	      				}, 100);      					
	                    $.simplyToast('操作成功!', 'success');
	                }else{
	                    $.simplyToast('操作失败，请重试!', 'danger');
	                }
		        });

	        });



		//初始化日历
        var date = new Date();
        var d = date.getDate(),
                m = date.getMonth(),
                y = date.getFullYear();
        $('#calendar').fullCalendar({
          header: {
            left: 'prev,next today',
            center: 'title',
            right: false
          },
          buttonText: {
            today: '今天',
            prev: "上月",
            next: "下月",
          },
          firstDay: 1,
          weekMode: 'variable',
          monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
		  dayNames: ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'],
		  dayNamesShort: ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'],
          events: function(start, end, timezone, callback) {
          	$.getJSON("{{route('admin.production.getPrice')}}", {id: $("#id").val(), start: start.toISOString(), end: end.toISOString()}, function(result) {   
          		console.log(result.data);       		
          		var events = [];    
          		var data = result.data;
          		$.each(data,function(k, e){
          			e.title = "¥"+e.price;
          			e.start = e.date;
          			e.backgroundColor = "#f39c12";
          			e.borderColor = "#e08e0b";
          			e.textColor = "#ffffff";
          			e.className = "price";
          			events.push(e);
          		});      		
          		callback(events);
          	});
          },

          editable: false,
          droppable: false, // this allows things to be dropped onto the calendar !!!
          loading: function(isLoading, view){
          		calendar_loading(isLoading);
          },
          dayClick: function(date, allDay, jsEvent, view) {
          		var da = new Date(date);
          		var date_str = da.getFullYear()+"-"+(da.getMonth()+1)+"-"+da.getDate();
				editPrice(date_str);
			},
			eventClick: function( event, jsEvent, view ) {
				editPrice(event.date);
			}
        });
		
		
		var $edit_price_dialog = $(".edit-price-dialog");
		var $edit_date = $edit_price_dialog.find("#edit_date");
		var $edit_price = $edit_price_dialog.find("#edit_price");
		//删除按钮
		var $btn_edit_delete = $edit_price_dialog.find(".edit_delete");
		var $production_id = $("#id");
		//编辑价格
		function editPrice(date){
      		var id = $production_id.val();

      		calendar_loading(true);
      		$.get("{{route('admin.production.getPriceInfo')}}", {id: id, date: date}, function(resp){
      			calendar_loading(false);

      			var price = "";
      			if (resp.code == 200) {
      				price = resp.data.price;
      				$btn_edit_delete.show();
      			}else{
      				$btn_edit_delete.hide();      				
      			}
      			$edit_date.val(date);
      			$edit_price.val(price);      				

	      		$edit_price_dialog.modal();
      		});
		}

		$(document).on("click", ".edit_save", function(){
      		var id = $production_id.val();
      		var data = {
      			production_id: id,
      			date: $edit_date.val(),
      			price: $edit_price.val()
      		};

      		$.post("{{route('admin.production.savePriceInfo')}}", data, function(resp){
      			if (resp.code == 200) {
      				$.simplyToast("保存成功", "success");
      				$('#calendar').fullCalendar( 'refetchEvents');
      				clear_dialog();
			      	$edit_price_dialog.modal("hide");			      				
      			}else{
      				$.simplyToast("保存失败", "error");
      			}
      		});
		});

		$(document).on("click", ".edit_delete", function(){
      		var id = $production_id.val();
      		var data = {
      			production_id: id,
      			date: $edit_date.val(),
      			price: $edit_price.val()
      		};

      		if(!confirm("确定删除？")){
      			return;
      		}

      		$.post("{{route('admin.production.delPriceInfo')}}", data, function(resp){
      			if (resp.code == 200) {
      				$.simplyToast("保存成功", "success");
      				$('#calendar').fullCalendar( 'refetchEvents');
      				clear_dialog();
			      	$edit_price_dialog.modal("hide");			      				
      			}else{
      				$.simplyToast("保存失败", "error");
      			}
      		});
		});


		function clear_dialog(){
			$btn_edit_delete.hide();
			$edit_date.val("");
			$edit_price.val("");
		}

		//日历加载层
		function calendar_loading(isLoading){
      		if (isLoading) {
      			$("#calendar_panel .box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
      		}else{
      			$("#calendar_panel .box .overlay").remove();
      		}
		}



      });



  window.uploader = new plupload.Uploader({
      runtimes : 'html5,flash,silverlight,html4',
      browse_button : 'add_pic', // you can pass an id...
//      container: document.getElementById('add_pic_wrap'), // ... or DOM Element itself
      drop_element: 'drop_pic',
      url : '{{route("admin.upload")}}',
      flash_swf_url : './Moxie.swf',
      silverlight_xap_url : './Moxie.xap',
      
      filters : {
          max_file_size : '5mb',
          mime_types: [
              {title : "Image files", extensions : "jpg,gif,png,jpeg"},
          ]
      },

      file_data_name: 'upfile',

      //压缩图片
      resize: {
        width: 500,
        height: 500,
        crop: true,
        preserve_headers: false
      },

      init: {
          PostInit: function() {
          },

          FilesAdded: function(up, files) {
              // var tpl = _.template($("#pic_tpl").html());
              // var pics_list = $(".pics .list .piclist");

              plupload.each(files, function(file) {
                  previewImage(file,function(imgsrc){
//                    pics_list.append(tpl({id: file.id, img:imgsrc, main:0}));       
                          uploader.start();
                  });           
              });
          },

          UploadProgress: function(up, file) {
              $("#add_pic").html("上传中...("+file.percent+"%)");
//            $("#"+file.id).find(".p").html(file.percent + "%");
          },

          FileUploaded: function(uploader,file,responseObject){
            var data = eval('('+responseObject.response+')');
            if (data.state == "SUCCESS") {
              $("#main_pic").attr("src", "/"+data.url);
            }else{
              $.simplyToast('图片上传失败，请重试!', 'danger');
            }
            $("#add_pic").html("上传图片");

          },

          //移除事件
          FilesRemoved: function(uploader,files){
          },

          Error: function(up, err) {

            var msg = "上传失败";
            if (err.status == 413) {
              msg = "文件太大";
            }
            $.simplyToast(msg, 'danger');
          }
      }
  });

  uploader.init();

  function previewImage(file,callback){//file为plupload事件监听函数参数中的file对象,callback为预览图片准备完成的回调函数
    if(!file || !/image\//.test(file.type)) return; //确保文件是图片
    if(file.type=='image/gif'){//gif使用FileReader进行预览,因为mOxie.Image只支持jpg和png
      var fr = new mOxie.FileReader();
      fr.onload = function(){
        callback(fr.result);
        fr.destroy();
        fr = null;
      }
      fr.readAsDataURL(file.getSource());
    }else{
      var preloader = new mOxie.Image();
      preloader.onload = function() {
        preloader.downsize( 300, 300 );//先压缩一下要预览的图片,宽300，高300
        var imgsrc = preloader.type=='image/jpeg' ? preloader.getAsDataURL('image/jpeg',80) : preloader.getAsDataURL(); //得到图片src,实质为一个base64编码的数据
        callback && callback(imgsrc); //callback传入的参数为预览图片的url
        preloader.destroy();
        preloader = null;
      };
      preloader.load( file.getSource() );
    } 
  }


    </script>

    <style type="text/css">
		.set_tag, .set_location{
			padding: 15px;
		}
		.set_tag .list .item, .set_location .list .item{
			float: left;
			width: 150px;
		}
		.fc-event.price{
			background-color: #f39c12;
			border-color: #e08e0b;
			color: #ffffff;
			text-align: center;
			height: 20px;
			line-height: 20px;
			font-size: 16px;
		}
		#main_pic{
			display: block;
			width: 200px;
			height: 200px;
		}
		#add_pic{
			margin-top: 10px;
		}

.number-input {
  display: inline-block;
  height: 24px;
  vertical-align: middle;
  display: block;
}
.number-input .decr,
.number-input .incr {
  border: 1px solid #cacbcb;
  height: 24px;
  line-height: 22px;
  width: 16px;
  text-align: center;
  color: #666;
  margin: 0;
  background: #fff;
  display: block;
  float: left;
}
.number-input .decr.decr,
.number-input .incr.decr {
  border-right: 0;
}
.number-input .decr.incr,
.number-input .incr.incr {
  border-left: 0;
}
.number-input .input_num {
  float: left;
  padding: 0 !important;
  text-align: center;
}

    </style>

@endsection
