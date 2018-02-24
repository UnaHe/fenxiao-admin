<div style="width: 600px; height:500px;padding: 20px;">
	<form id="edit-form">
		<input type="hidden" name="id" value="<%=id%>">
		<div class="form-group">
			<label for="title">域名</label>
			<input type="text" class="form-control" name="domain" placeholder="" value="<%=domain%>">
		</div>

		<div class="form-group">
			<label for="title">类型</label>
			<select class="form-control state"  name="type">
				<option value="1" <%=type == 1 ?"selected='tree'":''%>>直接打开</option>
				<option value="2" <%=type == 2 ?"selected='tree'":''%>>快站域名</option>
			</select>
		</div>

	</form>
</div>