@extends('admin.layouts.app')

@section("content")

<script type="text/javascript">
  $(function(){
    setNav(".updatepwd");
  });
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    修改资料
  </h1>
</section>

<!-- Main content -->
<section class="content">
              <div class="box">
                <div class="box-header">
                </div><!-- /.box-header -->
                <div class="box-body">
                  <div class="col-md-6">
                  <form role="form" onsubmit="return false;">
                    <div class="box-body">

                      <div class="form-group">
                        <label for="oldpass">账号</label>
                        <input type="text" class="form-control" id="account" value="{{$user->mobile}}">
                      </div>

                      <div class="form-group">
                        <label for="oldpass">密码</label>
                        <input type="password" class="form-control" id="oldpass">
                      </div>
                      
                      <div class="form-group">
                        <label for="newpass">新密码<span style="color: #999">(如不修改密码请勿填写)</span></label>
                        <input type="password" class="form-control" id="newpass">
                      </div>

                      <div class="form-group">
                        <label for="repass">确认密码<span style="color: #999">(如不修改密码请勿填写)</span></label>
                        <input type="password" class="form-control" id="repass">
                      </div>

                    </div><!-- /.box-body -->

                    <div class="box-footer">
                      <button type="submit" class="btn btn-primary sub">确认</button>
                    </div>
                  </form>
                  </div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->

</section><!-- /.content -->


    <!-- page script -->
    <script type="text/javascript">
      $(function () {

        $(document).on('click', '.sub', function(){
          var account = $("#account").val();
          var oldpass = $("#oldpass").val();
          var newpass = $("#newpass").val();
          var repass = $("#repass").val();

          if (account == '') {
              layer.alert('请输入账号!', {icon:5});
              return;
          }

          if (oldpass == '') {
              layer.alert('请输入密码!', {icon:5});
              return;
          }

          if (newpass != '' && newpass.length<6) {
              layer.alert('新密码长度太短!', {icon:5});
              return;
          }

          if (newpass != '' && newpass != repass) {
              layer.alert('确认密码错误!', {icon:5});
              return;
          }

          var data = {
            account: account,
            oldpass: oldpass,
            newpass: newpass
          }

          $.post("{{route('admin.system.updatepwd')}}", data, function(data){
            if (data.code == 200) {
                layer.alert('保存成功!', {icon:1});
                $("input[type=password]").val("");

            }else if(data.code == 301) {
                layer.alert('原密码错误!', {icon:5});
            }else{
                layer.alert('操作失败!', {icon:5});
            }

          });            

        });

      });


      
    </script>


<style type="text/css">


</style>

@endsection