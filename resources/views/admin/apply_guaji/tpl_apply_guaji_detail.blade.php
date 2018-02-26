<div style="width: 600px;">
    <div style="padding: 20px;">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td>续费账号</td>
                    <td><%=apply.mobile%></td>
                </tr>

                <tr>
                    <td>付款支付宝账号</td>
                    <td><%=apply.alipay_account%></td>
                </tr>

                <tr>
                    <td>申请时间</td>
                    <td><%=apply.add_time%></td>
                </tr>

                <tr>
                    <td>处理时间</td>
                    <td><%=apply.deal_time%></td>
                </tr>

                <tr>
                    <td>申请状态</td>
                    <td><%=apply.status_str%></td>
                </tr>

            </tbody>
        </table>
    </div>

    <div class="layui-layer-btn layui-layer-btn-">
        <a class="layui-layer-btn0 confirm-btn" style="<%=apply.status != 0 ? 'display:none':''%>">已收款，确认续期</a>
        <a class="layui-layer-btn1 refuse-btn" style="<%=apply.status != 0 ? 'display:none':''%>">拒绝申请</a>
    </div>
</div>

<style>
    .bold{
        font-size: 20px;
        font-weight: bold;
    }
</style>