<form id="edit-form">
    <input type="hidden" name="id" value="<%=user.id%>">
    <table class="table table-striped">
        <tbody>
            <tr>
                <td>用户id</td>
                <td><%=user.id%></td>
            </tr>
            <tr>
                <td>手机号</td>
                <td><input type="text" class="form-control input-sm" name="mobile" value="<%=user.mobile%>"></td>
            </tr>
            <tr>
                <td>等级</td>
                <td>
                    <select class="form-control state"  name="grade">
                        <?php foreach ($grades as $grade):?>
                        <option value="<?=$grade['grade']?>" <%=user.grade == <?=$grade['grade']?> ?"selected='tree'":''%>><?=$grade['grade_name']?></option>
                        <?php endforeach;?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>账户余额(元)</td>
                <td><input type="text" class="form-control input-sm" name="balance" value="<%=user.balance%>"></td>
            </tr>
            <tr>
                <td>是否禁用</td>
                <td>
                    <select class="form-control state"  name="is_forbid">
                        <option value="0" <%=user.is_forbid == 0 ?"selected='tree'":''%>>否</option>
                        <option value="1" <%=user.is_forbid == 1 ?"selected='tree'":''%>>是</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>禁用原因</td>
                <td><input type="text" class="form-control input-sm" name="forbid_reason" value="<%=user.forbid_reason%>"></td>
            </tr>
            <tr>
                <td>注册时间</td>
                <td><%=user.reg_time%></td>
            </tr>
            <tr>
                <td>注册IP</td>
                <td><%=user.reg_ip%></td>
            </tr>
            <tr>
                <td>最后一次登录时间</td>
                <td><%=user.last_login_time%></td>
            </tr>
            <tr>
                <td>最后一次登录IP</td>
                <td><%=user.last_login_ip%></td>
            </tr>
            <tr>
                <td>已绑定PID</td>
                <td><%=user.pid%></td>
            </tr>
            <tr>
                <td>邀请码</td>
                <td><%=user.referral_code%></td>
            </tr>
            <tr>
                <td>修改密码</td>
                <td><input type="text" class="form-control input-sm" name="password" value="" placeholder="如不修改请勿填写"></td>
            </tr>
        </tbody>
    </table>
</form>
