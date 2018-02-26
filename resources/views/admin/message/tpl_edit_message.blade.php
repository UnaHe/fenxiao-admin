<div style="width: 600px; height:500px;padding: 20px;">
	<form id="send-message-form">
		<input type="hidden" name="id" value="<%=id%>">
		<div class="form-group">
			<label">通知内容</label>
			<textarea name="title" class="form-control"><%=title%></textarea>
		</div>

		<div class="form-group">
			<label">通知详情</label>
			<textarea name="content" class="form-control"><%=content%></textarea>
		</div>

		<div class="form-group" style="<%=id ?"display:none;":""%>">
			<label">接收用户手机号（留空则发送到所有用户）</label>
			<input name="mobile" class="form-control" value="<%=mobile%>" <%=mobile?'readonly="readonly"':''%>/>
		</div>

	</form>
</div>