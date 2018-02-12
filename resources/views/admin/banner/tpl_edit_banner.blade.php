<div style="width: 600px; height:500px;padding: 20px;">
	<form id="edit-form">
		<input type="hidden" name="id" value="<%=id%>">
		<div class="form-group">
			<label for="title">展示位置</label>
			<input type="text" class="form-control" name="position" placeholder="" value="<%=position%>">
		</div>

		<div class="form-group">
			<label for="title">标题</label>
			<input type="text" class="form-control" name="name" placeholder="请输入标题" value="<%=name%>">
		</div>

		<div class="form-group">
			<label for="title">链接网址</label>
			<input type="text" class="form-control" name="click_url" placeholder="请输入链接网址" value="<%=click_url%>">
		</div>

		<div class="form-group">
			<label for="title">图片</label>
			<div>
				<img src="<%=pic%>" class="pic" id="pic" style="max-width: 500px; max-height: 300px;"/>
				<div id="add_pic" class="btn btn-info">上传图片</div>
				<input type="hidden" id="pic_val" name="pic" value="<%=pic%>">
			</div>
		</div>
	</form>
</div>