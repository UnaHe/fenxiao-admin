@extends('admin.layouts.app')
@section("content")

<script type="text/javascript">
  $(function(){
    setNav(".dashboard");
  });
</script>

        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            系统概览
          </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <a href="/withdraw"  class="small-box bg-aqua">
                        <div class="inner">
                            <h3><?=$unDealWithdrawNum?></h3>
                            <p>提现申请</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-bell-o"></i>
                        </div>
                        <div class="small-box-footer">
                            查看详情<i class="fa fa-arrow-circle-right"></i>
                        </div>
                    </a>
                </div><!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <a href="/apply_upgrade" class="small-box bg-green">
                        <div class="inner">
                            <h3><?=$unDealApplyUpgradeNum?></h3>
                            <p>直升等级申请</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-bell-o"></i>
                        </div>
                        <div class="small-box-footer">
                            查看详情<i class="fa fa-arrow-circle-right"></i>
                        </div>
                    </a>
                </div><!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <a href="/apply_guaji" class="small-box bg-yellow">
                        <div class="inner">
                            <h3><?=$unDealApplyGuajiNum?></h3>
                            <p>挂机续费申请</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-bell-o"></i>
                        </div>
                        <div class="small-box-footer">
                            查看详情<i class="fa fa-arrow-circle-right"></i>
                        </div>
                    </a>
                </div><!-- ./col -->
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">用户注册情况</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td>今日注册</td>
                                        <td><?=$userStatistics['today']?></td>
                                    </tr>
                                    <tr>
                                        <td>昨日注册</td>
                                        <td><?=$userStatistics['yesterday']?></td>
                                    </tr>
                                    <tr>
                                        <td>本周注册</td>
                                        <td><?=$userStatistics['cur_week']?></td>
                                    </tr>
                                    <tr>
                                        <td>本月注册</td>
                                        <td><?=$userStatistics['cur_month']?></td>
                                    </tr>
                                    <tr>
                                        <td>用户总数</td>
                                        <td><?=$userStatistics['total']?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div><!-- /.col -->

                <div class="col-md-3">
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">订单情况</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td>今日付款</td>
                                        <td>
                                            <div>订单数 <?=$orderStatistics['today_pay']['num']?></div>
                                            <div>付款金额 ¥<?=$orderStatistics['today_pay']['money']?:0?></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>今日结算</td>
                                        <td>
                                            <div>订单数 <?=$orderStatistics['today_settle']['num']?></div>
                                            <div>结算金额 ¥<?=$orderStatistics['today_settle']['money']?:0?></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>本月付款</td>
                                        <td>
                                            <div>订单数 <?=$orderStatistics['cur_month_pay']['num']?></div>
                                            <div>付款金额 ¥<?=$orderStatistics['cur_month_pay']['money']?:0?></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>本月结算</td>
                                        <td>
                                            <div>订单数 <?=$orderStatistics['cur_month_settle']['num']?></div>
                                            <div>结算金额 ¥<?=$orderStatistics['cur_month_settle']['money']?:0?></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>同步时间</td>
                                        <td>
                                            <?=$orderStatistics['last_sync_time']?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div><!-- /.col -->

                <div class="col-md-3">
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">PID使用情况</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-striped">
                                <tbody>
                                <tr>
                                    <td>PID总量</td>
                                    <td><?=$pidStatistics['total']?></td>
                                </tr>
                                <tr>
                                    <td>已使用</td>
                                    <td><?=$pidStatistics['used']?></td>
                                </tr>
                                <tr>
                                    <td>未使用</td>
                                    <td><?=$pidStatistics['not_used']?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div><!-- /.col -->

            </div>

        </section><!-- /.content -->
@endsection
