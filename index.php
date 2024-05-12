<?php 
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

header("X-XSS-Protection: 1; mode=block");
include './sg-config.php';

session_start();
date_default_timezone_set('Europe/Moscow');
$today = date('d.m.Y H:i');
define('SG_KEY', true);
header('Content-Type: text/html; charset=UTF8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ob_start();
$errors = array();
$mode = isset($_GET['mode'])  ? $_GET['mode'] : false;
$user = isset($_SESSION['user']) ? $_SESSION['user'] : false;
$role_id = isset($_SESSION['ROLE_ID']) ? $_SESSION['ROLE_ID'] : false;
$timestamp = isset($_SESSION['timestamp']) ? $_SESSION['timestamp'] : false;

if(time() -$_SESSION['timestamp'] > 600) { 
    unset($_SESSION['LOGIN'], $_SESSION['ID_USER'], $_SESSION['timestamp']);
    header('Location:'. SG_HOST .'/logout');
    exit;
} else {
    $_SESSION['timestamp'] = time();
}
if (isset($_SESSION['HTTP_USER_AGENT']) &&
    $_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'])) {
    exit;
} else {
  $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
}

if(!defined('SG_KEY'))
{
     header("HTTP/1.1 404 Not Found");
     exit(file_get_contents('./404.php'));
}

if (isset($_SESSION['LOGIN'])){
		$SQL_DATE = "alter session set nls_date_format = 'dd.mm.yyyy'";
		$stid = oci_parse($conn, $SQL_DATE);
		oci_execute($stid);

		$GET_SG_REG = "select ID,LOGIN,PASS,SALT,ACTIVE_HEX,STATUS,ROLE_ID,ADRESS,PHONE_NUMBER,FAM,IM,OTCH,LS,DT_UPDATE,STATUS_PASS,DESCRIPTION from sg_reg WHERE ID=".$_SESSION['ID_USER'];
                $get_query = oci_parse($conn, $GET_SG_REG);
                oci_execute($get_query);
                $row = oci_fetch_assoc($get_query);
                if ($row['STATUS']==1 and $row['DESCRIPTION']==''){
			        $sql_get_t_lk_ls = "select COUNT(*) AS NUM_ROWS from t_lk_ls where T_SG_REG_ID=".$_SESSION['ID_USER'];
				$res_sql_get_t_lk_ls = oci_parse($conn, $sql_get_t_lk_ls);
			        oci_define_by_name($res_sql_get_t_lk_ls, 'NUM_ROWS', $t_lk_num_rows);
			        oci_execute($res_sql_get_t_lk_ls);
			        oci_fetch($res_sql_get_t_lk_ls);


				if ($t_lk_num_rows=0)
				{
				    header('Location:'. SG_HOST .'/personal/add');
                                    exit;
				}else{
        				$sql_get_t_lk_ls_ = "select * from T_LK_LS WHERE ROLE_ID=1 and T_SG_REG_ID=".$_SESSION['ID_USER'];
				        $get_query_sql_get_t_lk_ls_ = oci_parse($conn, $sql_get_t_lk_ls_);
				        oci_execute($get_query_sql_get_t_lk_ls_);
				        $row_t_lk = oci_fetch_assoc($get_query_sql_get_t_lk_ls_);
				        $_SESSION['LS'] =$row_t_lk['LS'];

					$_SESSION['STATUS'] = $row['STATUS'];
        	                        $_SESSION['STATUS_PASS'] = $row['STATUS_PASS'];
                	                $_SESSION['ROLE_ID'] = $row['ROLE_ID'];
                        	        $_SESSION['PHONE_NUMBER'] = $row['PHONE_NUMBER'];
                                	$_SESSION['FAM'] =$row['FAM'];
           	                        $_SESSION['IM'] = $row['IM'];
                	                $_SESSION['OTCH'] = $row['OTCH'];
                        	        $_SESSION['ADRESS'] = $row['ADRESS'];
                                	$_SESSION['DESCRIPTION'] = $row['DESCRIPTION'];
                               	 	$_SESSION['DT_UPDATE'] = $row['DT_UPDATE'];
                               		$_SESSION['timestamp'] = time();

				if($_SESSION['ROLE_ID']=='1'){
			            header('Location:'. SG_HOST .'/personal');
				    exit;
				}
				}
		}
		else if($row['STATUS']==1 and $row['DESCRIPTION']=='На проверку'){
                     $errors[] = 'Учетная запись проверяется модератором! Результат будет выслан на электронную почту указанную при регистрации!';
                }
                else{
                     $errors[] = 'Учетная запись не активирована, письмо с ссылкой для активации отправлено на почту указанную при регистрации!';
                }
}
else {
        $login = '';
        if (isset($_COOKIE['CookieMy'])){
                $login = htmlspecialchars($_COOKIE['CookieMy']);
        }
		header('Location:'. SG_HOST .'/login');
    }

$content = ob_get_contents();
ob_end_clean();
?>
