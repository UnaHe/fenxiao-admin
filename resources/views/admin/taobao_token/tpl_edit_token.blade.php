<div style="width: 600px; height:500px;padding: 20px;">
	<form id="edit-form">
		<input type="hidden" name="id" value="<%=id%>">
		<div class="form-group">
			<label">联盟ID(member id)</label>
			<input name="member_id" class="form-control" value="<%=member_id%>"/>
		</div>

		<div class="form-group">
			<label">授权结果地址(请将授权完成后结果页地址复制到此处) <a href="<?=$auth_url?>" target="_blank">登陆授权</a></label>
			<textarea name="token_url" class="form-control"></textarea>
		</div>

	</form>
</div>