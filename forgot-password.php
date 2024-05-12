<?php
header("X-XSS-Protection: 1; mode=block");
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid', 0 );
ini_set('session.cookie_httponly', 1);
ini_set('session.entropy_file', '/dev/urandom');
ini_set('session.hash_function', 'whirlpool');
ini_set('session.cookie_secure', 1);

error_reporting(E_ALL);
ini_set('display_errors', 'On');

session_start();
include './sg-config.php'; 
require_once './sg-includes/functions/funct.php';
$errors = array();
$data = $_POST;	
date_default_timezone_set('Europe/Samara');
$today = date("d-m-Y");
$result=0;
$emailCheck=array();
$getEmailRslt=array();

$dt_now = date("d.m.Y H:i:s"); 
$SQL_DATE = "alter session set nls_date_format = 'dd.mm.yyyy'";
$stid = oci_parse($conn, $SQL_DATE);
oci_execute($stid);

if (isset($_POST['email'])){

	$email = addslashes(htmlspecialchars(trim(strtolower($_POST["email"]))));
	if(!empty($email)){
		$login = addslashes(htmlspecialchars(strtolower($_POST['email'])));
		$sql_get_salt = "select SALT,STATUS from sg_reg WHERE LOGIN='".$login."'";
		$get_salt_query = oci_parse($conn, $sql_get_salt);
		oci_execute($get_salt_query);
		$row = oci_fetch_assoc($get_salt_query);
		if(!empty($row))
		{
			if((int)$row["STATUS"] === 1){
				$token=gen_token();
				$query_update_token  = "UPDATE SG_REG SET reset_password_token='".$token."',DT_TOKEN=sysdate WHERE LOGIN = '". $login ."'";
				$res_query_update_token = oci_parse($conn, $query_update_token);
				oci_execute($res_query_update_token);

				$link_reset_password = SG_HOST."/reset-password?email=".$email."&token=".$token;
                $title = 'Вы запросили восстановление пароля на https://lk.host.ru';
                $message = '<table width="600" cellspacing="0" cellpadding="0" border="0" align="center" style="background:#ffffff;border:1px solid #cccccc;border-radius: 2px 2px 0 0">
                <tbody><tr>
                    <td align="center" style="font-size:18px;line-height:20px;">
                        <br>Личный кабинет абонента АО "Цифровые Решения"<br><br></td>
					</tr>
					<tr>
                    <td></td>
					</tr>
					<tr>

					</tr><tr>

					<td width="540" style="padding:0 25px 15px 25px">
						<h1 style="color:#333333;font-size:32px;line-height:34px;font-family:Arial,Helvetica,sans-serif">
							Изменить пароль</h1>
						<p style="color:#666666;font-size:14px;line-height:20px;font-family:Arial,Helvetica,sans-serif">
							Для смены пароля, пожалуйста, нажмите кнопку либо пройдите по ссылке.</p>
					</td>
					</tr>
					<tr>
					<td></td>
					</tr>
					<tr>
					<td width="540" style="padding: 0 25px 15px 25px;">
						<table cellpadding="0" cellspacing="0" border="0" width="540">
						<tbody><tr>
							<td width="220" height="44" valign="middle" align="center" bgcolor="#2579A9" style="border-radius: 5px;">
								<a style="display:block;height:44px;line-height:44px;color:#ffffff;text-decoration:none;font-size:14px;font-family:Arial,Helvetica,sans-serif" href="'.$link_reset_password.'" target="_blank" rel=" noopener noreferrer">
									Изменить пароль
								</a>
							</td>
							<td width="320" height="44">&nbsp;</td>
						</tr>
					</tbody></table>
					</td>
					</tr>
					<tr>
					<td></td>
					</tr>
					<tr>
					<td style="padding: 0 25px 15px 25px;">
						<p style="color:#666666;font-size:14px;line-height:20px;font-family:Arial,Helvetica,sans-serif">
						Если ссылка не открывается, скопируйте её в адресную строку браузера.</p>
						<p style="color:#666666;font-size:14px;line-height:20px;font-family:Arial,Helvetica,sans-serif;text-decoration: underline"><a href="'.$link_reset_password.'" target="_blank">'.$link_reset_password.'</a></p>
					</td>
					</tr>

							<tr bgcolor="#F3F3F3">
								<td width="540" style="padding: 10px 25px 15px 25px;">
									<p style="color:#333333;font-size:14px;line-height:20px;font-family:Arial,Helvetica,sans-serif">
										Это письмо было отправлено автоматически. </p>
									<p style="color:#333333;font-size:14px;line-height:20px;font-family:Arial,Helvetica,sans-serif">
										Если Вы считаете, что получили его по ошибке, просто проигнорируйте его. </p>
									<p style="color:#333333;font-size:14px;line-height:20px;font-family:Arial,Helvetica,sans-serif">
									</p>
								</td>
							</tr>
							<tr>
							<td></td>
							</tr>
					</tbody></table>';  
                sendMessageMail($email, SG_MAIL_AUTOR, $title, $message);

                header('Location: /login?mode=reminder');
                exit;
			}else{
				$_SESSION["error_messages"] = "<p class='mesage_error' ><strong>Ошибка!</strong> Вы не можете восстановить свой пароль, потому что указанный адрес электронной почты ($email) не подтверждён. </p><p>Для подтверждения почты перейдите по ссылке из письма, которую получили после регистрации.</p><p><strong>Внимание!</strong> Ссылка для подтверждения почты, действительна 24 часа с момента регистрации. Если Вы не подтвердите Ваш email в течении этого времени, то Ваш аккаунт будет удалён.</p>";

				header("HTTP/1.1 301 Moved Permanently");
				header("Location: /reset-password");

				exit;
			}
		}
		else{
			$_SESSION["error_messages"] = "<p class='mesage_error' > Ошибка запроса на выборки пользователя из БД</p>";

			header("HTTP/1.1 301 Moved Permanently");
			header("Location: /reset-password");
		}
	}

}

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Личный кабинет абонента | Восстановление пароля</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition login-page" style="background:#F7F7F7;">
<div class="login-box" style="width:500px;">
  <div class="login-logo">
	<center><img src="../build/images/logos.png"></center>
    <center><h4> АО "Цифровые Решения"</h4></center>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Введите свой адрес электронной почты, указанный при регистрации. На этот адрес мы пришлем Вам пароль.</p>

      <form id="forgot" method="post">
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Электронная почта">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Восстановить</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <p class="mt-3 mb-1">
        <a href="./login">Войти</a>
      </p>
      <p class="mb-0">
        <a href="/login#signup" class="text-center">Зарегистрироваться</a>
      </p>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        "use strict";
        var pattern = /^[a-z0-9][a-z0-9\._-]*[a-z0-9]*@([a-z0-9]+([a-z0-9-]*[a-z0-9]+)*\.)+[a-z]+/i;
        var mail = $('input[name=email]');
        $('button[type=submit]').attr('disabled', true);

        mail.blur(function(){
            if(mail.val() != ''){
                if(mail.val().search(pattern) == 0){
					mail.css({backgroundColor:"white"});
					mail.css({border:"1px solid #007bff"});
                    $('button[type=submit]').attr('disabled', false);
                }else{
                    mail.attr('placeholder', 'Не правильный Email');
					mail.css({border:"1px solid #ca4343"});
                    $('button[type=submit]').attr('disabled', true);
                }
            }else{
				mail.css({border:"1px solid #ca4343"});
                mail.attr('placeholder','Введите Ваш email');
            }
        });
    });
</script>
</body>
</html>

