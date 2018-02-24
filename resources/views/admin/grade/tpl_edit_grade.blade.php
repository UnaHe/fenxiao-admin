<div style="width: 600px; height:500px;padding: 20px;">
	<form id="edit-form">
		<input type="hidden" name="id" value="<%=data.id%>">
		<div class="form-group">
			<label for="title">等级名称</label>
			<input type="text" class="form-control" name="grade_name" placeholder="" value="<%=data.grade_name%>">
		</div>
		<div class="form-group">
			<label for="title">等级级别排序 从大到小</label>
			<input type="text" class="form-control" name="sort" placeholder="" value="<%=data.sort%>">
		</div>

		<div class="form-group">
			<label for="title">升级所需下级账号等级</label>
			<select class="form-control"  name="child_grade">
				<%_.each(grades, function(grade){%>
					<option value="<%=grade.grade%>" <%=data.child_grade == grade.grade ?"selected='tree'":''%>><%=grade.grade_name%></option>
				<%});%>
			</select>
		</div>

		<div class="form-group">
			<label for="title">升级所需下级账号个数</label>
			<input type="text" class="form-control" name="child_grade_num" placeholder="" value="<%=data.child_grade_num%>">
		</div>

		<div class="form-group">
			<label for="title">自买返利</label>
			<input type="text" class="form-control" name="rate[0]" placeholder="" value="<%=data.rates.rate_0%>">
		</div>

		<div class="form-group">
			<label for="title">直推1级返利</label>
			<input type="text" class="form-control" name="rate[1]" placeholder="" value="<%=data.rates.rate_1%>">
		</div>

		<div class="form-group">
			<label for="title">直推2级返利</label>
			<input type="text" class="form-control" name="rate[2]" placeholder="" value="<%=data.rates.rate_2%>">
		</div>

		<div class="form-group">
			<label for="title">平行1级返利</label>
			<input type="text" class="form-control" name="same_rate[1]" placeholder="" value="<%=data.same_rates.same_rate_1%>">
		</div>

		<div class="form-group">
			<label for="title">平行2级返利</label>
			<input type="text" class="form-control" name="same_rate[2]" placeholder="" value="<%=data.same_rates.same_rate_2%>">
		</div>

		<div class="form-group">
			<label for="title">找上级层次</label>
			<input type="text" class="form-control" name="find_parent_level" placeholder="" value="<%=data.find_parent_level%>">
		</div>

		<div class="form-group">
			<label for="title">找平级层次</label>
			<input type="text" class="form-control" name="find_same_level" placeholder="" value="<%=data.find_same_level%>">
		</div>

	</form>
</div>