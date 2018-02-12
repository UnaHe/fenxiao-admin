<div style="width: 1000px;">
    <div class="col-sm-6">
        <label>订单详情</label>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td>订单id</td>
                    <td><%=order.id%></td>
                </tr>

                <tr>
                    <td>订单号</td>
                    <td><%=order.order_no%></td>
                </tr>

                <tr>
                    <td>订单状态</td>
                    <td><%=order.order_state_str%></td>
                </tr>

                <tr>
                    <td>淘宝商品id</td>
                    <td><%=order.goods_id%></td>
                </tr>

                <tr>
                    <td>商品名称</td>
                    <td><a href='http://item.taobao.com/item.htm?id=<%=order.goods_id%>' target='_blank'><%=order.goods_title%></a></td>
                </tr>

                <tr>
                    <td>商品数</td>
                    <td><%=order.goods_num%></td>
                </tr>

                <tr>
                    <td>商品单价</td>
                    <td>¥<%=order.goods_price%></td>
                </tr>

                <tr>
                    <td>卖家名称</td>
                    <td><%=order.seller_name%></td>
                </tr>

                <tr>
                    <td>店铺名称</td>
                    <td><%=order.shop_name%></td>
                </tr>

                <tr>
                    <td>收入比率</td>
                    <td><%=order.income_rate%>%</td>
                </tr>

                <tr>
                    <td>分成比例</td>
                    <td><%=order.share_rate%>%</td>
                </tr>

                <tr>
                    <td>付款金额</td>
                    <td>¥<%=order.pay_money%></td>
                </tr>

                <tr>
                    <td>结算金额</td>
                    <td>¥<%=order.settle_money%></td>
                </tr>

                <tr>
                    <td>结算时间</td>
                    <td><%=order.settle_time%></td>
                </tr>

                <tr>
                    <td>效果预估</td>
                    <td>¥<%=order.predict_money%></td>
                </tr>

                <tr>
                    <td>结算预估</td>
                    <td>¥<%=order.predict_income%></td>
                </tr>

                <tr>
                    <td>佣金比例</td>
                    <td><%=order.commission_rate%>%</td>
                </tr>

                <tr>
                    <td>佣金金额</td>
                    <td>¥<%=order.commission_money%></td>
                </tr>

                <tr>
                    <td>补贴比率</td>
                    <td><%=order.subsidy_rate%>%</td>
                </tr>

                <tr>
                    <td>补贴金额</td>
                    <td>¥<%=order.subsidy_money%></td>
                </tr>

                <tr>
                    <td>补贴类型</td>
                    <td><%=order.subsidy_type%></td>
                </tr>

                <tr>
                    <td>网站id</td>
                    <td><%=order.site_id%></td>
                </tr>

                <tr>
                    <td>广告位id</td>
                    <td><%=order.adzone_id%></td>
                </tr>

                <tr>
                    <td>订单类型</td>
                    <td><%=order.pay_platform%></td>
                </tr>

                <tr>
                    <td>成交平台</td>
                    <td><%=order.platform%></td>
                </tr>

                <tr>
                    <td>订单创建时间</td>
                    <td><%=order.create_time%></td>
                </tr>

                <tr>
                    <td>点击时间</td>
                    <td><%=order.click_time%></td>
                </tr>

                <tr>
                    <td>同步时间</td>
                    <td><%=order.sync_time%></td>
                </tr>

                <tr>
                    <td>返利结算时间</td>
                    <td><%=order.deal_time%></td>
                </tr>

            </tbody>
        </table>
    </div>

    <div class="col-sm-6">
        <label>返利信息</label>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>返利用户</th>
                    <th>等级</th>
                    <th>返利比例</th>
                    <th>效果预估</th>
                    <th>结算预估</th>
                    <th>备注</th>
                </tr>
            </thead>
            <tbody>
                <%_.each(users, function(user){%>
                    <tr>
                        <td><%=user.mobile%></td>
                        <td><%=user.grade_str%></td>
                        <td><%=user.user_rate%>%</td>
                        <td>¥<%=user.predict_money%></td>
                        <td>¥<%=user.predict_income%></td>
                        <td><%=user.order_user_id==user.user_id ? '订单用户' :''%></td>
                    </tr>
                <%});%>
            </tbody>
        </table>
    </div>

</div>
