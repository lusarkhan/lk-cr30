<?php
error_reporting(E_ALL);
 ini_set('display_errors', 'On');
 session_start();
host './sg-config.php';
host './sg-hosts/functions/funct.php';
host './sg-hosts/sg-db.php';
 $errors = array();
 $host=$config['DB_HOST'];
 $dbuser=$config['DB_USERNAME'];
 $dbpass=$config['DB_PASSWORD'];
 $dbloginget=$config['DB_LOGIN_SQL'];
 $dbuserscountget=$config['DB_USERSCOUNTSQL'];
 $conn = oci_connect($dbuser, $dbpass, $host);
	
 date_default_timezone_set('Europe/Samara');
 $today = date("d-m-Y");
 $data = $_POST;
 $errors = array();
 $login = isset($_SESSION['LOGIN']) ? $_SESSION['LOGIN'] : false;
 $timestamp = isset($_SESSION['timestamp']) ? $_SESSION['timestamp'] : false;
 if(time() -$_SESSION['timestamp'] > 600) {
    unset($_SESSION['LOGIN'], $_SESSION['ID_USER'], $_SESSION['timestamp']);
    header('Location:'.'/logout');
    exit;
} else {
    $_SESSION['timestamp'] = time();
}

if (isset($_SESSION['HTTP_USER_AGENT']))
{
    if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))
    {
        exit;
    }
}
else
{
    $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
}

if (isset($_SESSION['LOGIN'])){
 if(isset($_POST['recovery']))
 {
        if($_POST['recovery'] == "change")
                if (isset($_POST['old_pass']) && isset($_POST['password1']) && isset($_POST['password2'])){
                        $old_pass = $_POST['old_pass'];
                        $sql_get_salt = "select SALT,PASS from sg_reg WHERE LOGIN='".$_SESSION['LOGIN']."'";
                        $get_salt_query = oci_parse($conn, $sql_get_salt);
                        oci_define_by_name($get_salt_query, 'SALT', $salt_db);
                        oci_define_by_name($get_salt_query, 'PASS', $pass_db);
                        oci_execute($get_salt_query);
                        oci_fetch($get_salt_query);

                        $old_password = md5(md5(trim($_POST['old_pass'])).$salt_db);

                        if ($pass_db==$old_password)
                        {
                                if ($_POST['password1']==$_POST['password2']){
                                        $salt = uniqid();
                                        $pass = md5(md5($_POST['password1']).$salt);
                                        $md5salt = md5($salt);

                                        $sql_update_passwd = "UPDATE sg_reg SET PASS='".$pass."',SALT='".$salt."',ACTIVE_HEX='".$md5salt."' WHERE LOGIN='".$_SESSION['LOGIN']."' AND PASS='".$pass_db."'";
                                        $ex_sql_update_passwd = oci_parse($conn, $sql_update_passwd);
                                        oci_execute($ex_sql_update_passwd);
                                        oci_fetch($ex_sql_update_passwd);
                                        header('Location:'.'/personal/options');
                                        exit;
                                }
                                else
                                {
                                        $errors[] = 'Пароли не совпадают';
                                }
                        }
                        else{$errors[] = 'Не верный старый пароль!';}
                }
 }
 }
   else {
        $login = '';
        if (isset($_COOKIE['CookieMy'])){
                $login = htmlspecialchars($_COOKIE['CookieMy']);
        }
                header('Location:'.'/login');
    }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Личный кабинет абонента АО "Цифровые Решения" | Восстановление пароля</title>
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
<body class="hold-transition login-page" onload="init()">
<div class="login-box">
  <div class="login-logo">
	<center><a href="/"><img src="../build/images/logos.png"></a></center>
    <center><h4>АО "Цифровые Решения"</h4></center>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Введите новый пароль</p>

      <form action="" method="post">
	    <div class="input-group mb-3">
          <input type="password" name="old_pass" class="form-control" required placeholder="Старый пароль">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password1" class="form-control" required placeholder="Новый пароль">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password2" class="form-control" required placeholder="Подтвердите пароль">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
		<?php
	                if(!empty($errors)) {
		               echo '<div class="row">';
					   echo '<div class="col-12">';
					   echo '<div class="alert alert-danger alert-dismissible fade show" style="width:100%;">';
					   echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                       echo '<strong>Ошибка! </strong>'  . array_shift($errors). '</div>';
					   echo '</div>';
					   echo '</div>';
				    }
		?>
        <div class="row">
          <div class="col-12">
            <button type="submit" name="recovery" value="change" id="recoverybtn" class="btn btn-primary btn-block">Сменить пароль</button>
          </div>
          <!-- /.col -->
        </div>
	 <p class="mt-3 mb-1">
        <a href="/personal/">Назад</a>
      </p>
      </form>
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
</body>
</html>
