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
            Page Header
            <small>Optional description</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
            <li class="active">Here</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
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
            </div>

        </section><!-- /.content -->
@endsection
