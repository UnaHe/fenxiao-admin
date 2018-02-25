<div style="width: 600px; height:500px;padding: 20px;">
	<form id="edit-form">
		<input type="hidden" name="id" value="<%=id%>">
		<div class="form-group">
			<label for="title">公告内容</label>
			<textarea name="title" class="form-control"><%=title%></textarea>
		</div>

		<div class="form-group">
			<label for="title">开始时间</label>
			<input type="text" class="form-control select-time1" name="start_time" placeholder="请输入开始时间" value="<%=start_time%>">
		</div>

		<div class="form-group">
			<label for="title">结束时间</label>
			<input type="text" class="form-control select-time2" name="end_time" placeholder="请输入结束时间" value="<%=end_time%>">
		</div>

	</form>
</div>