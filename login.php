<?php
 header("X-XSS-Protection: 1; mode=block");
 error_reporting(E_ALL);
 ini_set('display_errors', 'Off');
 include 'sg-config.php'; 
 include 'sg-includes/functions/funct.php';
 $errors = array();

 date_default_timezone_set('Europe/Samara');
 $today = date("d-m-Y");
 $result=0;
 $emailCheck=array();
 $getEmailRslt=array();
 $error_type=0;

 if(!defined('SG_KEY'))
{
     header("HTTP/1.1 404 Not Found");
     exit(file_get_contents('./404.php'));
}

session_start();

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

 	$SQL_DATE = "alter session set nls_date_format = 'dd.mm.yyyy'";
	$stid = oci_parse($conn, $SQL_DATE);
	oci_execute($stid);

	$get_t_lk_config_site_status = "select site_status AS NUM_ROW,descr AS DESCRIPTION from t_lk_config";
	$res_get_t_lk_config_site_status = oci_parse($conn, $get_t_lk_config_site_status);
	oci_define_by_name($res_get_t_lk_config_site_status, 'NUM_ROW', $site_status);
	oci_define_by_name($res_get_t_lk_config_site_status, 'DESCRIPTION', $site_status_descr);
	oci_execute($res_get_t_lk_config_site_status);
	oci_fetch($res_get_t_lk_config_site_status);

	if ($site_status==1){
		$errors[] = $site_status_descr;
		if(count($errors) > 0){
		   	echo '<script src="/plugins/jquery/jquery.min.js"></script>';
			echo '<link href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
			echo '<script src="/plugins/toastr/toastr.min.js"></script>';
			echo "<script type='text/javascript'>toastr.info('".$site_status_descr."')</script>";
		}
	}
else{

if (isset($_POST['email']) && isset($_POST['password'])){
	$data = $_POST;
	$errors = array();
        $login = addslashes(htmlspecialchars($_POST['email']));
        $login = mb_strtolower($login);
        $sql_get_salt = "select SALT from sg_reg WHERE LOGIN='".$login."'";
	$get_salt_query = oci_parse($conn, $sql_get_salt);
	oci_execute($get_salt_query);
	$row = oci_fetch_assoc($get_salt_query);
	$password = md5(md5(trim($_POST['password'])).$row['SALT']);
 if(isset($_POST['submit']))
 {
    if(empty($_POST['email'])){
        $errors[] = 'Введите эл. почту';
	    $error_type=2;
	}
    if(empty($_POST['password'])){
        $errors[] = 'Введите пароль';
		$error_type=2;
	}
    {
		$GET_SG_REG = "select ID,LOGIN,PASS,SALT,ACTIVE_HEX,STATUS,ROLE_ID,ADRESS,PHONE_NUMBER,FAM,IM,OTCH,LS,DT_UPDATE,STATUS_PASS,DESCRIPTION from sg_reg WHERE LOGIN='".$login."' AND PASS='".$password."'";
		$row_query = "select COUNT(*) AS NUM_ROWS from sg_reg WHERE LOGIN='".$login."' AND PASS='".$password."'";
      	        $get_query = oci_parse($conn, $GET_SG_REG);
	        $get_row_query = oci_parse($conn, $row_query);
		oci_define_by_name($get_row_query, 'NUM_ROWS', $num_rows);
		oci_execute($get_row_query);
		oci_fetch($get_row_query);

		if ($num_rows == 1) {
	       		oci_execute($get_query);
			$row = oci_fetch_assoc($get_query);
			if ($row['STATUS']==1 and $row['DESCRIPTION']==''){
				$_SESSION['user'] = true;
				$_SESSION['ID_USER'] = $row['ID'];
	 	        $_SESSION['LOGIN'] = $row['LOGIN'];
				$_SESSION['PHONE_NUMBER'] = $row['PHONE_NUMBER'];
				$_SESSION['MAINLS'] = $row['LS'];
				$_SESSION['timestamp'] = time();
		        setcookie("CookieMy", $row['ID'], time()+60*60*24*1);

				$insert_t_lk_sessions = "INSERT INTO T_LK_SESSIONS (T_SG_REG_ID, DT_BEG, DT_END, IP_ADDR, ACT, HTTP_USER_AGENT, U_NAME, U_PASS, U_LS)
        			                        VALUES ('".$_SESSION['ID_USER']."',SYSDATE,'','".htmlspecialchars($_SERVER['REMOTE_ADDR'])."',1,'".$_SESSION['HTTP_USER_AGENT']."','".$_POST['email']."',q'[".$_POST['password']."]','".htmlspecialchars($_POST['MAINLS'])."')";
	                        $ex_insert_t_lk_sessions=oci_parse($conn, $insert_t_lk_sessions);
        	                oci_execute($ex_insert_t_lk_sessions);
				header('Location:'.'/');
		                exit;
			}
			else if($row['STATUS']==1 and $row['DESCRIPTION']=='На проверку'){
				$errors[] = 'Учетная запись проверяется модератором! Результат будет выслан на электронную почту указанную при регистрации!';
				$error_type=1;
				foreach ($errors as $error)
				{ 
					echo '<script src="/plugins/jquery/jquery.min.js"></script>';
					echo '<link href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
					echo '<script src="/plugins/toastr/toastr.min.js"></script>';
					echo "<script type='text/javascript'>toastr.info('".$error."')</script>";
				}
			}
			else{
				$errors[] = 'Учетная запись не активирована, письмо с ссылкой для активации отправлено на почту указанную при регистрации!';
				$error_type=2;
				foreach ($errors as $error)
				{ 
					echo '<script src="/plugins/jquery/jquery.min.js"></script>';
					echo '<link href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
					echo '<script src="/plugins/toastr/toastr.min.js"></script>';
					echo "<script type='text/javascript'>toastr.error('".$error."')</script>";
				}
			}
		}
		else{
			$errors[] = 'Пользователь с таким логином или паролем не найден!';
			$error_type=2;

    		$insert_t_lk_sessions = "INSERT INTO T_LK_SESSIONS (T_SG_REG_ID, DT_BEG, DT_END, IP_ADDR, ACT, HTTP_USER_AGENT,U_NAME,U_PASS)
                                VALUES ('',SYSDATE,'','".htmlspecialchars($_SERVER['REMOTE_ADDR'])."',5,'".$_SESSION['HTTP_USER_AGENT']."','".$login."',q'[".$_POST['password']."]')";
			$ex_insert_t_lk_sessions=oci_parse($conn, $insert_t_lk_sessions);
		    oci_execute($ex_insert_t_lk_sessions);
		} 
	}
}
}
}
		if (isset($_SESSION['LOGIN'])){
     		header('Location:'.'/');
        } 
        else {
        $login = '';
        if (isset($_COOKIE['CookieMy'])){$login = htmlspecialchars($_COOKIE['CookieMy']);}
        }

 if(isset($_GET['status']) and $_GET['status'] == 'ok'){
    $errors[] = '<b>Вы успешно зарегистрировались! На Вашу электронную почту указанную при регистрации отправлено письмо. Пожалуйста активируйте свой аккаунт!</b>';
	echo '<script src="/plugins/jquery/jquery.min.js"></script>';
	echo '<link href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
	echo '<script src="/plugins/toastr/toastr.min.js"></script>';
	echo "<script type='text/javascript'>toastr.success('<b>Вы успешно зарегистрировались! На Вашу электронную почту указанную при регистрации отправлено письмо. Пожалуйста активируйте свой аккаунт!</b>')</script>";
}

 if(isset($_GET['active']) and $_GET['active'] == 'ok'){
    $errors[] = '<b>Ваш аккаунт успешно активирован!</b>';
   	echo '<script src="/plugins/jquery/jquery.min.js"></script>';
	echo '<link href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
	echo '<script src="/plugins/toastr/toastr.min.js"></script>';
	echo "<script type='text/javascript'>toastr.success('<b>Ваш аккаунт успешно активирован!</b>')</script>";
}

 if(isset($_GET['reset']) and $_GET['reset']=='ok'){
    $errors[] = '<b>Вы успешно сменили пароль!</b>';
   	echo '<script src="/plugins/jquery/jquery.min.js"></script>';
	echo '<link href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
	echo '<script src="/plugins/toastr/toastr.min.js"></script>';
	echo "<script type='text/javascript'>toastr.success('<b>Вы успешно сменили пароль!</b>')</script>";
   }

 if(isset($_GET['mode']) and $_GET['mode']=='reminder'){
   $errors[] = '<b>Ссылка на сброс пароля отправлена на электронную почту указанную при регистрации!</b>';
   	echo '<script src="/plugins/jquery/jquery.min.js"></script>';
	echo '<link href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
	echo '<script src="/plugins/toastr/toastr.min.js"></script>';
	echo "<script type='text/javascript'>toastr.info('<b>Ссылка на сброс пароля отправлена на электронную почту указанную при регистрации!</b>')</script>";

   }
 if(isset($_GET['key']))
 {
    $sql = "SELECT COUNT(*) AS NUM_ROWS FROM SG_REG WHERE ACTIVE_HEX = '". htmlspecialchars($_GET['key']) ."'";
    $res = oci_parse($conn, $sql);
    oci_define_by_name($res, 'NUM_ROWS', $num_rows);
    oci_execute($res);
    oci_fetch($res);
    if ($num_rows == 0){
        $errors[] = 'Неверный ключ активации!';
		$error_type=2;
	}
    if(count($errors) > 0){
	  	echo '<script src="/plugins/jquery/jquery.min.js"></script>';
		echo '<link href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
		echo '<script src="/plugins/toastr/toastr.min.js"></script>';
		echo "<script type='text/javascript'>toastr.error('Неверный ключ активации!')</script>";
	  }
    else
    {
		$sql = "SELECT * FROM SG_REG WHERE ACTIVE_HEX = '". $_GET['key'] ."'";
		$res = oci_parse($conn, $sql);
        oci_execute($res);
        $row = oci_fetch_assoc($res);
    	$email = $row['LOGIN'];
		$sg_reg_id=$row['ID'];
		$sg_reg_ls=$row['LS'];
		$sql_get_t_lk_ls = "SELECT COUNT(*) AS NUM_ROWS FROM T_LK_LS WHERE LS=".$sg_reg_ls;
		$res_sql_get_t_lk_ls = oci_parse($conn, $sql_get_t_lk_ls);
		oci_define_by_name($res_sql_get_t_lk_ls, 'NUM_ROWS', $num_rows_token);
    	oci_execute($res_sql_get_t_lk_ls);
    	oci_fetch($res_sql_get_t_lk_ls);

		if ($num_rows_token == 1){
	        $errors[] = 'Токен устарел или данный лицевой счет уже зарегистрирован!';
			$error_type=2;
		
			$insert_t_lk_sessions = "INSERT INTO T_LK_SESSIONS (T_SG_REG_ID, DT_BEG, DT_END, IP_ADDR, ACT, HTTP_USER_AGENT)
		                         VALUES ('',SYSDATE,'','".htmlspecialchars($_SERVER['REMOTE_ADDR'])."',7,'".$_SESSION['HTTP_USER_AGENT']."')";
			$ex_insert_t_lk_sessions=oci_parse($conn, $insert_t_lk_sessions);
			oci_execute($ex_insert_t_lk_sessions);
		}
        if(count($errors) > 0){
			foreach ($errors as $error)
			{ 
				echo '<script src="/plugins/jquery/jquery.min.js"></script>';
				echo '<link href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
				echo '<script src="/plugins/toastr/toastr.min.js"></script>';
				echo '<script src="/plugins/toastr/toastr.min.js"></script>';
				if($error_type==0)
					echo "<script type='text/javascript'>toastr.success('".$error."')</script>";
				elseif($error_type==1)
					echo "<script type='text/javascript'>toastr.info('".$error."')</script>";
				elseif($error_type==2)
					echo "<script type='text/javascript'>toastr.error('".$error."')</script>";
									}
		}
        else
        {
			$sql = "UPDATE SG_REG SET STATUS = 1 WHERE LOGIN = '". $email ."'";
			$res = oci_parse($conn, $sql);
	        oci_execute($res);

	        $sql_insert_t_lk_ls = "INSERT INTO T_LK_LS (T_SG_REG_ID,STATUS,ROLE_ID,DT_BEG,LS,EMAIL_COUPON) VALUES (".$sg_reg_id.",1,1,SYSDATE,'".$sg_reg_ls."',0)";
       		$res_sql_insert_t_lk_ls = oci_parse($conn, $sql_insert_t_lk_ls);
        	oci_execute($res_sql_insert_t_lk_ls);

                $insert_t_lk_sessions = "INSERT INTO T_LK_SESSIONS (T_SG_REG_ID, DT_BEG, DT_END, IP_ADDR, ACT, HTTP_USER_AGENT)
                                         VALUES ('',SYSDATE,'','".htmlspecialchars($_SERVER['REMOTE_ADDR'])."',10,'".$_SESSION['HTTP_USER_AGENT']."')";
                $ex_insert_t_lk_sessions=oci_parse($conn, $insert_t_lk_sessions);
                oci_execute($ex_insert_t_lk_sessions);
        	$title = 'Ваш аккаунт на https://lk.host.ru успешно активирован';
	        $message = 'Поздравляем Вас, Ваш аккаунт на https://lk.host.ru успешно активирован';

	        header('Location:'. SG_HOST .'/login?mode=reg&active=ok');
	        exit;
		}
    }
 }
            $sql = "SELECT COUNT(*) AS NUM_ROWS FROM T_LK_SESSIONS WHERE ACT=22 AND IP_ADDR='".htmlspecialchars($_SERVER['REMOTE_ADDR'])."' AND HTTP_USER_AGENT='".$_SESSION['HTTP_USER_AGENT']."'";
            $res = oci_parse($conn, $sql);
            oci_define_by_name($res, 'NUM_ROWS', $num_rows);
            oci_execute($res);
            oci_fetch($res);
            if($num_rows>10){
               http_response_code(403); 
               exit;
            }


if(isset($_POST['sign_up'])){

    if($_POST['sign_up'] == "Search")
	{ 
	    if(empty($_POST['LS'])){
		    $errors[] = 'Поле лицевого счета не может быть пустым';
			$error_type=2;
	    }
	    else
		{
            if(!preg_match("/[^0-9\-\_]{0,9}/", $_POST['LS'])){
            	$errors[] = 'Не правильно введен лицевой счет'."\n";
				$error_type=2;
            }
        }

		if(count($errors) > 0)
		{
			foreach ($errors as $error)
			{ 
				echo '<script src="/plugins/jquery/jquery.min.js"></script>';
				echo '<link href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
				echo '<script src="/plugins/toastr/toastr.min.js"></script>';
				echo '<script src="/plugins/toastr/toastr.min.js"></script>';
				if($error_type==0)
					echo "<script type='text/javascript'>toastr.success('".$error."')</script>";
				elseif($error_type==1)
					echo "<script type='text/javascript'>toastr.info('".$error."')</script>";
				elseif($error_type==2)
					echo "<script type='text/javascript'>toastr.error('".$error."')</script>";
			}
		}
		else
		{
			$find_ls=addslashes($_POST['LS']);
                        if(!preg_match("/[^0-9\-\_]{0,9}/", $find_ls) || $find_ls=="@"){
			     $insert_t_lk_sessions = "INSERT INTO T_LK_SESSIONS (T_SG_REG_ID, DT_BEG, DT_END, IP_ADDR, ACT, HTTP_USER_AGENT,U_NAME,U_PASS,U_LS)
                                                      VALUES ('',SYSDATE,'','".htmlspecialchars($_SERVER['REMOTE_ADDR'])."',22,'".$_SESSION['HTTP_USER_AGENT']."','".$_POST['email']."',q'[".$_POST['pass']."]','".$_POST['LS']."')";
                             $ex_insert_t_lk_sessions=oci_parse($conn, $insert_t_lk_sessions);
                             oci_execute($ex_insert_t_lk_sessions);
			     header('Location:'. SG_HOST .'/login');
			     exit;
			}
			else{ 
			$sql = "select COUNT(*) AS NUM_ROWS from SARV_PAY.PAY_LS WHERE NUM='".$find_ls."'";
			$res = oci_parse($conn, $sql);
			oci_define_by_name($res, 'NUM_ROWS', $num_rows);
			oci_execute($res);
			oci_fetch($res);
			if ($num_rows == 0){
				$errors[] = 'К сожалению лицевой счет: <b>'. htmlspecialchars($_POST['LS']) .'</b> не найден!';
				$error_type=2;
				$result=0;
				$insert_t_lk_sessions = "INSERT INTO T_LK_SESSIONS (T_SG_REG_ID, DT_BEG, DT_END, IP_ADDR, ACT, HTTP_USER_AGENT,U_NAME,U_PASS,U_LS)
                                			VALUES ('',SYSDATE,'','".htmlspecialchars($_SERVER['REMOTE_ADDR'])."',9,'".$_SESSION['HTTP_USER_AGENT']. "','".$_POST['email']."',q'[".$_POST['pass']."]','".$_POST['LS']."')";
				$ex_insert_t_lk_sessions=oci_parse($conn, $insert_t_lk_sessions);
				oci_execute($ex_insert_t_lk_sessions);

					if(count($errors) > 0){
						foreach ($errors as $error)
						{ 
							echo '<script src="/plugins/jquery/jquery.min.js"></script>';
							echo '<link href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
							echo '<script src="/plugins/toastr/toastr.min.js"></script>';
							echo '<script src="/plugins/toastr/toastr.min.js"></script>';
							if($error_type==0)
								echo "<script type='text/javascript'>toastr.success('".$error."')</script>";
							elseif($error_type==1)
								echo "<script type='text/javascript'>toastr.info('".$error."')</script>";
							elseif($error_type==2)
								echo "<script type='text/javascript'>toastr.error('".$error."')</script>";
						}
					}
			         else{
				    $ls = htmlspecialchars($_POST['LS']);
				}
			}
            		else{
				$sql_get_t_lk_ls = "select COUNT(*) AS NUM_ROWS from SARV_PAY.T_LK_LS WHERE LS='".htmlspecialchars($_POST['LS'])."'";
				$res_sql_get_t_lk_ls = oci_parse($conn, $sql_get_t_lk_ls);
				oci_define_by_name($res_sql_get_t_lk_ls, 'NUM_ROWS', $num_rows_t_lk_ls);
				oci_execute($res_sql_get_t_lk_ls);
				oci_fetch($res_sql_get_t_lk_ls);
				if ($num_rows_t_lk_ls==1){
				   $errors[] = 'Лицевой счет '. htmlspecialchars($_POST['LS']) . ' уже зарегистрирован!';
				   $error_type=2;
				}
				if(count($errors) > 0){
					foreach ($errors as $error)
					{ 
						echo '<script src="/plugins/jquery/jquery.min.js"></script>';
						echo '<link href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
						echo '<script src="/plugins/toastr/toastr.min.js"></script>';
						echo '<script src="/plugins/toastr/toastr.min.js"></script>';
						if($error_type==0)
							echo "<script type='text/javascript'>toastr.success('".$error."')</script>";
						elseif($error_type==1)
							echo "<script type='text/javascript'>toastr.info('".$error."')</script>";
						elseif($error_type==2)
							echo "<script type='text/javascript'>toastr.error('".$error."')</script>";
					}
				}
				else{ 
					$result=1;
					$ls = htmlspecialchars($_POST['LS']);
					$fio = htmlspecialchars(mb_strtoupper(trim($_POST['FAM']))) . ' ' . htmlspecialchars(mb_strtoupper(trim($_POST['IM']))) . ' ' . htmlspecialchars(mb_strtoupper(trim($_POST['OTCH'])));
					$sql_get_ls_fio = "SELECT FIO FROM PAY_LS WHERE NUM='".$ls."'";
					$res_sql_get_ls_fio = oci_parse($conn, $sql_get_ls_fio);
			                oci_define_by_name($res_sql_get_ls_fio, 'FIO', $ls_num_fio);
					oci_execute($res_sql_get_ls_fio);
					oci_fetch($res_sql_get_ls_fio);

					if ($fio==mb_strtoupper(trim($ls_num_fio))) 
					{
						if(empty($_POST['email'])){
							$errors[] = 'Поле Email не может быть пустым!';
							$error_type=2;
						}
						else
						{
							if(!preg_match("/^[a-z0-9_.-]+@([a-z0-9]+\.)+[a-z]{2,6}$/i", $_POST['email'])){
								$errors[] = 'Не правильно введен E-mail'."\n";
								$error_type=2;
							}
						}

						if(empty($_POST['pass'])){
							$errors[] = 'Поле Пароль не может быть пустым';
							$error_type=2;
						}

						if(empty($_POST['pass2'])){
							$errors[] = 'Поле Подтверждения пароля не может быть пустым';
							$error_type=2;
						}

						if(count($errors) > 0){
							foreach ($errors as $error)
							{ 
								echo '<script src="/plugins/jquery/jquery.min.js"></script>';
								echo '<link href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
								echo '<script src="/plugins/toastr/toastr.min.js"></script>';
								echo '<script src="/plugins/toastr/toastr.min.js"></script>';
								if($error_type==0)
									echo "<script type='text/javascript'>toastr.success('".$error."')</script>";
								elseif($error_type==1)
									echo "<script type='text/javascript'>toastr.info('".$error."')</script>";
								elseif($error_type==2)
									echo "<script type='text/javascript'>toastr.error('".$error."')</script>";
							}
						}
						else
						{
							if($_POST['pass'] != $_POST['pass2']){
								$errors[] = 'Пароли не совподают';
								$error_type=2;
							}
							if(count($errors) > 0){
								foreach ($errors as $error)
								{ 
									echo '<script src="/plugins/jquery/jquery.min.js"></script>';
									echo '<link href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
									echo '<script src="/plugins/toastr/toastr.min.js"></script>';
									echo '<script src="/plugins/toastr/toastr.min.js"></script>';
									if($error_type==0)
										echo "<script type='text/javascript'>toastr.success('".$error."')</script>";
									elseif($error_type==1)
										echo "<script type='text/javascript'>toastr.info('".$error."')</script>";
									elseif($error_type==2)
										echo "<script type='text/javascript'>toastr.error('".$error."')</script>";
								}
							}
							else
							{
								$sql = "select COUNT(*) AS NUM_ROWS from sg_reg WHERE LOGIN='".mb_strtolower($_POST['email'])."'";
								$res = oci_parse($conn, $sql);
								oci_define_by_name($res, 'NUM_ROWS', $num_rows);
								oci_execute($res);
								oci_fetch($res);


								if ($num_rows > 0){ 
									$errors[] = 'К сожалению Логин: <b>'. $_POST['email'] .'</b> занят!';
									$error_type=2;
								}
								
						            $getEmailRslt=1;
									$sql_login_ls_exists = "select COUNT(*) AS NUM_ROWS_LS from sg_reg WHERE LS='".$_POST['LS']."'";
									$res_login_ls_exists = oci_parse($conn, $sql_login_ls_exists);
									oci_define_by_name($res_login_ls_exists, 'NUM_ROWS_LS', $num_rows_login_ls_exists);
									oci_execute($res_login_ls_exists);
									oci_fetch($res_login_ls_exists);
									if ($num_rows_login_ls_exists > 0){
										$errors[] = 'К сожалению аккаунт с лицевым счетом: <b>'. $_POST['LS'] .'</b> уже зарегистрирован!';
										$error_type=2;
									}

								if(count($errors) > 0)
								{
									foreach ($errors as $error)
									{ 
										echo '<script src="/plugins/jquery/jquery.min.js"></script>';
										echo '<link href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
										echo '<script src="/plugins/toastr/toastr.min.js"></script>';
										echo '<script src="/plugins/toastr/toastr.min.js"></script>';
										if($error_type==0)
											echo "<script type='text/javascript'>toastr.success('".$error."')</script>";
										elseif($error_type==1)
											echo "<script type='text/javascript'>toastr.info('".$error."')</script>";
										elseif($error_type==2)
											echo "<script type='text/javascript'>toastr.error('".$error."')</script>";
									}
								}
								else{ 
									$salt = uniqid();

									$pass = md5(md5($_POST['pass']).$salt);
									$md5salt = md5($salt);
									$dt_now = date("d.m.Y H:i:s");

									$sql_reg = "INSERT INTO SARV_PAY.SG_REG
													(login, pass, salt, active_hex, status, phone_number, status_pass, role_id, dt_beg, dt_end, fam, im, otch, adress, ls, dt_update, description,telegram_send)
												VALUES ('".htmlspecialchars(mb_strtolower($_POST['email']))."','".$pass."','".$salt."','".$md5salt."', 0,'".htmlspecialchars($_POST['PHONE'])."', 0, 1, '".$today."','','".htmlspecialchars(mb_strtoupper(trim($_POST['FAM'])))."','".htmlspecialchars(mb_strtoupper(trim($_POST['IM'])))."','".htmlspecialchars(mb_strtoupper(trim($_POST['OTCH'])))."','',".htmlspecialchars(trim($_POST['LS'])) . ",'".$today."','',0)";				
									$res_reg = oci_parse($conn, $sql_reg);
                                                                        oci_execute($res_reg);
									$insert_t_lk_sessions = "INSERT INTO T_LK_SESSIONS (T_SG_REG_ID, DT_BEG, DT_END, IP_ADDR, ACT, HTTP_USER_AGENT)
						                                VALUES ('',SYSDATE,'','".htmlspecialchars($_SERVER['REMOTE_ADDR'])."',10,'".$_SESSION['HTTP_USER_AGENT']."')";
								        $ex_insert_t_lk_sessions=oci_parse($conn, $insert_t_lk_sessions);
								        oci_execute($ex_insert_t_lk_sessions);

									$url = SG_HOST .'/login?mode=reg&key='. md5($salt);
									$title = 'Регистрация на https://lk.host.ru';

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
										Регистрация в личном кабинете</h1>
									<p style="color:#666666;font-size:14px;line-height:20px;font-family:Arial,Helvetica,sans-serif">
										Вы успешно зарегистрировались в личном кабинете.</p>
									</td>
									</tr>
									<tr>
									<td></td>
									</tr>
									<tr>
									<td width="540" style="padding:0 25px 15px 25px">
										<p style="color:#666666;margin-bottom:0;font-size:14px;line-height:20px;font-family:Arial,Helvetica,sans-serif">Ваш логин: <a href="/compose?To="'.mb_strtolower($_POST['email']).'>'.mb_strtolower($_POST['email']).'</a></p>
										<p style="color:#666666;margin-top:0;font-size:14px;line-height:20px;font-family:Arial,Helvetica,sans-serif">Ваш пароль: '.$_POST['pass'].'</p>

									</td>
									</tr>
									<tr>
									<td></td>
									</tr>
									<tr>
									<td style="padding: 0 25px 15px 25px;">
           
										Для продолжения работы, необходимо подтвердить email перейдя по следующей ссылке: <a href="'.$url.'" target="_blank" rel=" noopener noreferrer">'.$url.'</a>

									</td>
									</tr>
                
										<tr bgcolor="#F3F3F3">
										<td width="540" style="padding: 10px 25px 15px 25px;">
											<p style="color:#333333;font-size:14px;line-height:20px;font-family:Arial,Helvetica,sans-serif">
												Это письмо было отправлено автоматически. На него не нужно отвечать!</p>
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
									sendMessageMail(mb_strtolower($_POST['email']), SG_MAIL_AUTOR, $title, $message);

									header('Location:'. SG_HOST .'/login?mode=reg&status=ok#signin');
									exit;
									echo '<script src="/plugins/jquery/jquery.min.js"></script>';
									echo '<link rel="stylesheet" href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
									echo '<script src="/plugins/toastr/toastr.min.js"></script>';
									echo "<script type='text/javascript'>toastr.success('Вы успешно зарегистрировались!')</script>";
									
								}
							} 
						}  
				}
				else{ 
				        if(empty($_POST['email'])){
							$errors[] = 'Поле Email не может быть пустым!';
							$error_type=2;
						}
						else
						{
							if(!preg_match("/^[a-z0-9_.-]+@([a-z0-9]+\.)+[a-z]{2,6}$/i", $_POST['email'])){
								$errors[] = 'Не правильно введен E-mail'."\n";
								$error_type=2;
							}
						}

						if(empty($_POST['pass'])){
							$errors[] = 'Поле Пароль не может быть пустым';
							$error_type=2;
						}

						if(empty($_POST['pass2'])){
							$errors[] = 'Поле Подтверждения пароля не может быть пустым';
							$error_type=2;
						}

						if(count($errors) > 0){
									foreach ($errors as $error)
									{ 
										echo '<script src="/plugins/jquery/jquery.min.js"></script>';
										echo '<link href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
										echo '<script src="/plugins/toastr/toastr.min.js"></script>';
										echo '<script src="/plugins/toastr/toastr.min.js"></script>';
										if($error_type==0)
											echo "<script type='text/javascript'>toastr.success('".$error."')</script>";
										elseif($error_type==1)
											echo "<script type='text/javascript'>toastr.info('".$error."')</script>";
										elseif($error_type==2)
											echo "<script type='text/javascript'>toastr.error('".$error."')</script>";
									}
						}
						else
						{
							if($_POST['pass'] != $_POST['pass2']){
								$errors[] = 'Пароли не совподают';
								$error_type=2;
							}

							if(count($errors) > 0){
									foreach ($errors as $error)
									{ 
										echo '<script src="/plugins/jquery/jquery.min.js"></script>';
										echo '<link href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
										echo '<script src="/plugins/toastr/toastr.min.js"></script>';
										echo '<script src="/plugins/toastr/toastr.min.js"></script>';
										if($error_type==0)
											echo "<script type='text/javascript'>toastr.success('".$error."')</script>";
										elseif($error_type==1)
											echo "<script type='text/javascript'>toastr.info('".$error."')</script>";
										elseif($error_type==2)
											echo "<script type='text/javascript'>toastr.error('".$error."')</script>";
									}
								}
							else
							{
								$sql = "select COUNT(*) AS NUM_ROWS from sg_reg WHERE LOGIN='".mb_strtolower($_POST['email'])."'";
								$res = oci_parse($conn, $sql);
								oci_define_by_name($res, 'NUM_ROWS', $num_rows);
								oci_execute($res);
								oci_fetch($res);

								
								if ($num_rows > 0){ 
									$errors[] = 'К сожалению Логин: <b>'. $_POST['email'] .'</b> занят!';
									$error_type=2;
								}
					                $getEmailRslt=1;
									$sql_login_ls_exists = "select COUNT(*) AS NUM_ROWS_LS from sg_reg WHERE LS='".$_POST['LS']."'";
									$res_login_ls_exists = oci_parse($conn, $sql_login_ls_exists);
									oci_define_by_name($res_login_ls_exists, 'NUM_ROWS_LS', $num_rows_login_ls_exists);
									oci_execute($res_login_ls_exists);
									oci_fetch($res_login_ls_exists);
								if ($num_rows_login_ls_exists > 0){ 
									$errors[] = 'К сожалению аккаунт с лицевым счетом: <b>'. $_POST['LS'] .'</b> уже зарегистрирован!';
									$error_type=2;
								}
								
								if(count($errors) > 0)
								{
									foreach ($errors as $error)
									{ 
										echo '<script src="/plugins/jquery/jquery.min.js"></script>';
										echo '<link href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
										echo '<script src="/plugins/toastr/toastr.min.js"></script>';
										echo '<script src="/plugins/toastr/toastr.min.js"></script>';
										if($error_type==0)
											echo "<script type='text/javascript'>toastr.success('".$error."')</script>";
										elseif($error_type==1)
											echo "<script type='text/javascript'>toastr.info('".$error."')</script>";
										elseif($error_type==2)
											echo "<script type='text/javascript'>toastr.error('".$error."')</script>";
									}
								}
								else
								{
									$salt = uniqid();

									$pass = md5(md5($_POST['pass']).$salt);
									$md5salt = md5($salt);
									$dt_now = date("d.m.Y H:i:s");
									$sql_reg = "INSERT INTO SARV_PAY.SG_REG
													(login, pass, salt, active_hex, status, phone_number, status_pass, role_id, dt_beg, dt_end, fam, im, otch, adress, ls, description,telegram_send)
												VALUES ('".htmlspecialchars(mb_strtolower($_POST['email']))."','".$pass."','".$salt."','".$md5salt."', 0,'".htmlspecialchars($_POST['PHONE'])."', 0, 1, '".$today."','','".htmlspecialchars(mb_strtoupper(trim($_POST['FAM'])))."','".htmlspecialchars(mb_strtoupper(trim($_POST['IM'])))."','".htmlspecialchars(mb_strtoupper(trim($_POST['OTCH'])))."','',".htmlspecialchars(trim($_POST['LS'])). ",'На проверку',0)";				
									$res_reg = oci_parse($conn, $sql_reg);
									oci_execute($res_reg);
									$insert_t_lk_sessions = "INSERT INTO T_LK_SESSIONS (T_SG_REG_ID, DT_BEG, DT_END, IP_ADDR, ACT, HTTP_USER_AGENT, U_NAME, U_PASS, U_LS)
						                                VALUES ('',SYSDATE,'','".htmlspecialchars($_SERVER['REMOTE_ADDR'])."',11,'".$_SESSION['HTTP_USER_AGENT']."','".$_POST['email']."',q'[".$_POST['pass']."]','".$_POST['LS']."')";
								        $ex_insert_t_lk_sessions=oci_parse($conn, $insert_t_lk_sessions);
									oci_execute($ex_insert_t_lk_sessions);

									$url = SG_HOST .'/login?mode=reg&key='. md5($salt);
									$title = 'Регистрация на https://lk.host.ru';
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
										Регистрация в личном кабинете</h1>
										<p style="color:#666666;font-size:14px;line-height:20px;font-family:Arial,Helvetica,sans-serif">
											Введенные Вами данные при регистрации требуют проверки модератором. Результаты будут сообщены на данный Email. </p>
									</td>
									</tr>
									<tr>
									<td></td>
									</tr>
									<tr>
									<td width="540" style="padding:0 25px 15px 25px">
										<p style="color:#666666;margin-bottom:0;font-size:14px;line-height:20px;font-family:Arial,Helvetica,sans-serif">Ваш логин: <a href="/compose?To="'.mb_strtolower($_POST['email']).'>'.mb_strtolower($_POST['email']).'</a></p>
										<p style="color:#666666;margin-top:0;font-size:14px;line-height:20px;font-family:Arial,Helvetica,sans-serif">Ваш пароль: '.$_POST['pass'].'</p>
	
									</td>
									</tr>
									<tr>
									<td></td>
									</tr>
									<tr>
									<td style="padding: 0 25px 15px 25px;">
           
										Для продолжения работы, необходимо подтвердить email перейдя по следующей ссылке: <a href="'.$url.'" target="_blank" rel=" noopener noreferrer">'.$url.'</a>

									</td>
									</tr>
                
									<tr bgcolor="#F3F3F3">
									<td width="540" style="padding: 10px 25px 15px 25px;">
										<p style="color:#333333;font-size:14px;line-height:20px;font-family:Arial,Helvetica,sans-serif">
											Это письмо было отправлено автоматически. На него не нужно отвечать!</p>
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
									sendMessageMail(mb_strtolower($_POST['email']), SG_MAIL_AUTOR, $title, $message);
									header('Location:'. SG_HOST .'/login?mode=reg&status=ok#signin');
									
									exit;
								}
							}
						} 
				}
				}
	        }
	    }
		}    
	}
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Личный кабинет АО "Цифровые Решения" | Вход</title>

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Bootstrap -->
    <link href="/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="/vendors/animate.css/animate.min.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="/build/css/custom.min.css" rel="stylesheet">
	<script async src="https://www.google.com/recaptcha/api.js?render=6LepEVIeAAAAACpgYqxEbnzZZJNP1Pl2OLqn721I"></script>
	<style>
    background: #ffffff;
    -webkit-box-shadow: 0 1px 2px #c2c2c2;
    box-shadow: 0 1px 2px #c2c2c2;
    min-width: 470px;
    min-height: 540px;
    padding: 60px 0 100px;
    margin: 0 30px;
}
	</style>
  </head>
  <body class="login" onload="init()">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
		    <center><img src="/build/images/logos.png"></center>
            <!-- <center><h4> АО "Цифровые Решения"</h4></center> -->
            <form method="post">
              <h1>личный кабинет</h1>
	      <div style="font-size: 14px; font-weight: 400; letter-spacing: normal; line-height: 20px; margin-bottom: 24px; text-align: center;">
            	Не забудьте отключить VPN. 
            	Он может мешать при работе с сайтом
              </div>
	      <div>
                <input type="email" name="email" class="form-control" placeholder="Электронная почта" value="<?php echo @$data['LOGIN']; ?>" required="" />
              </div>
              <div>
                <input type="password" name="password" class="form-control" placeholder="Пароль" required="" />
              </div>
			  <div>
			    <?php
	                if(!empty($errors)) {
						echo '<script src="/plugins/jquery/jquery.min.js"></script>';
						echo '<link href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
						echo '<script src="/plugins/toastr/toastr.min.js"></script>';
						if($error_type==0)
							echo "<script type='text/javascript'>toastr.success('".array_shift($errors)."')</script>";
						elseif($error_type==1)
							echo "<script type='text/javascript'>toastr.info('".array_shift($errors)."')</script>";
						elseif($error_type==2)
							echo "<script type='text/javascript'>toastr.error('".array_shift($errors)."')</script>";
				    }
			    ?>
				</div>
              <div>
			    <button type="submit" name="submit" class="btn btn-primary btn-block" onclick="showMessage()">Войти</button>
                <a class="reset_pass" href="/forgot-password">Забыли пароль?</a>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">Нет аккаунта?
                  <a href="#signup" class="to_register"> <b>Создать пользователя</b> </a>
                </p>

                <div class="clearfix"></div>
                <br />
              </div>
            </form>
          </section>
        </div>
		
        <div id="register" class="animate form registration_form">
          <section class="login_content">
		    <center><img src="../build/images/logos.png"></center>
            <center><h4> АО "Цифровые Решения"</h4></center>
			<!-- Первый запрос -->

            <form method="post">
			    <h1>регистрация</h1>

              <div style="font-size: 14px; font-weight: 400; letter-spacing: normal; line-height: 20px; margin-bottom: 24px; text-align: center;">
                Не забудьте отключить VPN.
                Он может мешать при работе с сайтом
              </div>
                <div>
				    <input type="text" class="form-control" name="LS" placeholder="Лицевой счет"  required pattern="[0-9]{9}" />
                </div> 

				<div>
					<input type="email" id="email" name="email" class="form-control" size="30" placeholder="Электронная почта" required onfocusout="showHint(this.value)" />
                </div>
                <div>
                    <input type="password" name="pass" class="form-control" placeholder="Пароль" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                         title="Пароль должен содержать хотя бы минимум 1 цифру, заглавные, прописные буквы, и длинной не менее 8 символов" required>
                </div>
		<div>
                     <input type="password" name="pass2" class="form-control" placeholder="Подтвердите пароль" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                         title="Пароль должен содержать хотя бы минимум 1 цифру, заглавные, прописные буквы, и длинной не менее 8 символов" required>

                </div>
			    <div>
			        <input type="text" class="form-control" name="PHONE" pattern="\+7\s?[\(]{0,1}9[0-9]{2}[\)]{0,1}\s?\d{3}[-]{0,1}\d{2}[-]{0,1}\d{2}" title="Введите телефон в формате +7 (xxx) xxx-xx-xx" required placeholder="+7 (900) 123-45-67"/>
			    </div>
			    <div>
			        <input type="text" class="form-control" name="FAM" pattern="^[А-Яа-яЁё\s]+$" title="Введите фамилию" required placeholder="Фамилия собственника"/>
			    </div>
			    <div>
			        <input type="text" class="form-control" name="IM" pattern="^[А-Яа-яЁё\s]+$" title="Введите имя" required placeholder="Имя собственника"/>
			    </div>
			    <div>
			        <input type="text" class="form-control" name="OTCH" pattern="^[А-Яа-яЁё\s]+$" title="Введите отчество" required placeholder="Отчество собственника"/>
			    </div>
     		    <div>
				<?php
	                if(!empty($errors)) {
						echo '<script src="/plugins/jquery/jquery.min.js"></script>';
						echo '<link rel="stylesheet" href="/plugins/toastr/toastr.min.css" rel="stylesheet"/>';
						echo '<script src="/plugins/toastr/toastr.min.js"></script>';
						if($error_type==0)
							echo "<script type='text/javascript'>toastr.success('".array_shift($errors)."')</script>";
						elseif($error_type==1)
							echo "<script type='text/javascript'>toastr.info('".array_shift($errors)."')</script>";
						elseif($error_type==2)
							echo "<script type='text/javascript'>toastr.error('".array_shift($errors)."')</script>";
				    }
			    ?>
				</div>
				<div>
				   <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
			       	   <input id="token" type="hidden" name="token">
				   <center><div class="g-recaptcha" data-sitekey="6LepEVIeAAAAAHCFpBTt6hb9efR4qAeJHayfiYPK"></div></center>
				   <button type="submit" name="sign_up" class="btn btn-primary btn-block" value="Search">Зарегистрироваться</button>
                </div>
				<div>
					<span>Нажимая кнопку "Зарегистрироваться" Вы даёте свое согласие на обработку введенной персональной информации в соответствии с Федеральным Законом №152-ФЗ от 27.07.2006 "О персональных данных"</span>
				</div>
				<div>
					<br>
					<span><a href="https://lk.host.ru/pdn2022.pdf">Правила обработки персональных данных</a></span>
				</div>
            </form>


              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">Уже есть аккаунт ?
                  <a href="#signin" class="to_register"> Войти </a>
                </p>
                <div class="clearfix"></div>
                <br />

              </div>
          </section>
        </div>
      </div>
    </div>
  </body>
  <!-- jQuery -->
<script src="/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="/plugins/toastr/toastr.min.js"></script>
<script src='https://www.google.com/recaptcha/api.js?render=6LfHAFIeAAAAAKDLL0y6r8GDduFu4f5g5xFjPPd7'></script>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script>
        grecaptcha.ready(function() {
            grecaptcha.execute('6LfHAFIeAAAAAKDLL0y6r8GDduFu4f5g5xFjPPd8', {action: 'submit'}).then(function(token) {
                document.getElementById('token').value = token
            });
        });
</script>
<script type="text/javascript">
function showMessage(){
	var $error_message = "<?= $_SESSION['ERRORS'] ?>";
							toastr.options = {
								"closeButton": false,
								"debug": false,
								"newestOnTop": false,
								"progressBar": false,
								"positionClass": "toast-top-right",
								"preventDuplicates": false,
								"onclick": null,
								"showDuration": "300",
								"hideDuration": "1000",
								"timeOut": "1500",
								"extendedTimeOut": "1000",
								"showEasing": "swing",
								"hideEasing": "linear",
								"showMethod": "fadeIn",
								"hideMethod": "fadeOut"
					}
					toastr.error("Ошибка")
}


function init(){
    document.getElementsByName("email")[0].focus();
}
function showHint(str) {
  if (str.length == 0) {
    document.getElementById("txtHint").innerHTML = "";
    return;
  } else {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
	const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });
      if (this.readyState == 4 && this.status == 200) {
		if (this.responseText==1)
		{
			Toast.fire({
			icon: 'error',
			title: 'Email уже зарегистрирован!'
			})
		}
      }
    };
    xmlhttp.open("GET", "../sg-includes/sg-check-email.php?q=" + str, true);
    xmlhttp.send();
  }
}
</script>
</html>


