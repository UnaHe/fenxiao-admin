<div style="width: 600px;">
    <div style="padding: 20px;">
        <input type="hidden" name="id" value="<%=user.id%>">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td>用户手机号</td>
                    <td><%=user.mobile%></td>
                </tr>

                <tr>
                    <td>提现金额</td>
                    <td><%=withdraw.amount%></td>
                </tr>

                <tr>
                    <td>提现申请时间</td>
                    <td><%=withdraw.add_time%></td>
                </tr>

                <tr>
                    <td>处理时间</td>
                    <td><%=withdraw.deal_time%></td>
                </tr>

                <tr>
                    <td>提现状态</td>
                    <td><%=withdraw.status_str%></td>
                </tr>

            </tbody>
        </table>
    </div>

    <div class="layui-layer-btn layui-layer-btn-">
        <a class="layui-layer-btn0 confirm-btn" style="<%=withdraw.status != 0 ? 'display:none':''%>">确认打款</a>
        <a class="layui-layer-btn1 refuse-btn" style="<%=withdraw.status != 0 ? 'display:none':''%>">拒绝申请</a>
    </div>
</div>