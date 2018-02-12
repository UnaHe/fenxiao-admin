<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>朋友淘</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="/admin_resource/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="/admin_resource/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

  </head>
  <body class="login-page">
    <div class="login-box">
      <div class="login-logo">
        <a href="/">朋友淘</a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg">请先登陆</p>
        <form onsubmit="return false">
          <div class="form-group has-feedback">
            <input type="text" class="form-control" id="account" placeholder="账号"/>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password" class="form-control" id="password" placeholder="密码"/>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="row">
            <div class="col-xs-8">             
            </div><!-- /.col -->
            <div class="col-xs-4">
              <button type="submit" class="btn btn-primary btn-block btn-flat sub">登录</button>
            </div><!-- /.col -->
          </div>
        </form>

      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <!-- jQuery 2.1.4 -->
    <script src="/admin_resource/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <script>
      $(function(){
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
        });        
        $(document).on('click', '.sub', function(){
          var $btn = $(this);
          var account = $("#account");
          var password = $("#password");
          if (account.val() == '' || password.val() == '') {
            alert('请输入用户名或密码');
            return;
          }

          $btn.prop("disabled", true).html("登录中...");
          $.post('{{url("/login")}}', {mobile:account.val(), password: password.val()}, function(data){
            if (data.code == 200) {
                var params = location.href.split("/");
                var index = params.indexOf("redirect");
                var url = "/";
                if ( index != -1) {
                    url = params[index+1];
                }
                location.href=decodeURIComponent(url);
            }else{
                alert("用户名或密码错误");
            }
            $btn.prop("disabled", false).html("登录");
          });
        
        });
      });
    </script>
  </body>
</html>