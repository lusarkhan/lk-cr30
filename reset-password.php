<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid', 0 );
ini_set('session.cookie_httponly', 1);
ini_set('session.entropy_file', '/dev/urandom');
ini_set('session.hash_function', 'whirlpool');
ini_set('session.cookie_secure', 1);
session_start();
include './sg-config.php';
include './sg-includes/functions/funct.php';

 date_default_timezone_set('Europe/Samara');
 $today = date("d-m-Y");
 $data = $_POST;
 $errors = array();
 $dt_now = date("d.m.Y H:i:s");
 $SQL_DATE = "alter session set nls_date_format = 'dd.mm.yyyy HH24:MI:SS'";
 $stid = oci_parse($conn, $SQL_DATE);
 oci_execute($stid);

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
 if (isset($_GET['token']) && !empty($_GET['token'])){
    $token = htmlspecialchars($_GET['token'], ENT_QUOTES);
    $sql = "SELECT COUNT(*) AS NUM_ROWS FROM SG_REG WHERE RESET_PASSWORD_TOKEN = '". $token ."'";
    $res = oci_parse($conn, $sql);
    oci_define_by_name($res, 'NUM_ROWS', $num_rows);
    oci_execute($res);
    oci_fetch($res);
    if ($num_rows == 0)
        $errors[] = ' Неверный токен';
    if(count($errors) > 0){
        showErrorMessage($errors);
       $insert_t_lk_sessions = "INSERT INTO T_LK_SESSIONS (T_SG_REG_ID, DT_BEG, DT_END, IP_ADDR, ACT, HTTP_USER_AGENT)
                                VALUES ('',SYSDATE,'','".htmlspecialchars($_SERVER['REMOTE_ADDR'])."',7,'".$_SESSION['HTTP_USER_AGENT']."')";
       $ex_insert_t_lk_sessions=oci_parse($conn, $insert_t_lk_sessions);
       oci_execute($ex_insert_t_lk_sessions);
    }
    else
    {
	if(isset($_GET['email']) && !empty($_GET['email'])){
    		$email = htmlspecialchars($_GET['email'], ENT_QUOTES);
		$sql_get_count = "select COUNT(*) AS NUM_ROWS from sg_reg WHERE RESET_PASSWORD_TOKEN='". $token."' and LOGIN='". $email."'";
                $res_sql_get_count = oci_parse($conn, $sql_get_count);
                oci_define_by_name($res_sql_get_count, 'NUM_ROWS', $token_num_rows);
                oci_execute($res_sql_get_count);
                oci_fetch($res_sql_get_count);

		if ($token_num_rows == 1){
			$sql_get_dt_token = "SELECT DT_TOKEN AS NUM_ROWS FROM SG_REG WHERE RESET_PASSWORD_TOKEN ='".$token."'";
			$res_sql_get_dt_token = oci_parse($conn, $sql_get_dt_token);
                	oci_define_by_name($res_sql_get_dt_token, 'NUM_ROWS', $dt_token_num_rows);
                	oci_execute($res_sql_get_dt_token);
                	oci_fetch($res_sql_get_dt_token);

			if(isset($_POST['recovery']))
  			{
        			if($_POST['recovery'] == "change")
                			if (isset($_POST['password1']) && isset($_POST['password2'])){

                                                if(empty($_POST['password1']))
                                                        $errors[] = 'Поле Пароль не может быть пустым';

                                                if(empty($_POST['password2']))
                                                        $errors[] = 'Поле Подтверждения пароля не может быть пустым';

                                		if ($_POST['password1']==$_POST['password2']){
                                        		$salt = uniqid();
                                        		$pass = md5(md5($_POST['password1']).$salt);
                                        		$md5salt = md5($salt);

			                                $sql_update_passwd = "UPDATE sg_reg SET PASS='".$pass."',SALT='".$salt."',ACTIVE_HEX='".$md5salt."' WHERE LOGIN='". $email."' AND RESET_PASSWORD_TOKEN='". $token."'";
        	        		                $ex_sql_update_passwd = oci_parse($conn, $sql_update_passwd);
                	                	        oci_execute($ex_sql_update_passwd);
                			                header('Location:'.'/login?reset=ok');
                                		        exit;
                                		}
                                		else
                                		{
                                        		$errors[] = 'Пароли не совпадают';
                           			}
  		        		}
 			}

		}
		else{$errors[] = 'Неверная пара почта и токен!';}
}else{
    	$errors[] = " Отсутствует адрес электронной почты.";
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
          <input type="password" name="password1" class="form-control"  placeholder="Новый пароль" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                   title="Пароль должен содержать хотя бы минимум 1 цифру, заглавные, прописные буквы, и длинной не менее 8 символов" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password2" class="form-control" placeholder="Подтвердите пароль" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                   title="Пароль должен содержать хотя бы минимум 1 цифру, заглавные, прописные буквы, и длинной не менее 8 символов" required>
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
      </form>
      <p class="mt-3 mb-1">
        <a href="/">На главную</a>
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
</body>
</html>
