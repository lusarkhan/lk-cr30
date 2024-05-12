<?php
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
header("X-XSS-Protection: 1; mode=block");
include './../sg-config.php';
session_start();
date_default_timezone_set('Europe/Samara');
 
header('Content-Type: text/html; charset=UTF8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ob_start();

$login = isset($_SESSION['LOGIN']) ? $_SESSION['LOGIN'] : false;

$email_coupon = isset($_GET['email_coupon']) ? htmlspecialchars(trim($_GET['email_coupon'])) : null;
$add_ls = isset($_GET['add_ls']) ? htmlspecialchars(trim($_GET['add_ls'])) : null;
$add_fio =  isset($_GET['add_fio']) ? htmlspecialchars(trim($_GET['add_fio'])) : null;

$add_fio = mb_strtoupper($add_fio);

$errors = array();

if(time() - $_SESSION['timestamp'] > 600) { 
    unset($_SESSION['LOGIN'], $_SESSION['ID_USER'], $_SESSION['timestamp']);
    header('Location:'. SG_HOST .'/logout');
    exit;
} else {
    $_SESSION['timestamp'] = time();
}
if(!empty($status_pass) || !empty($add_ls) || !empty($add_fio)){
		$sql = "select COUNT(*) AS NUM_ROWS from SARV_PAY.PAY_LS WHERE NUM='".$add_ls."'";
		$res = oci_parse($conn, $sql);
		oci_define_by_name($res, 'NUM_ROWS', $num_rows);
		oci_execute($res);
		oci_fetch($res);


		if ($num_rows == 0){
			result(0);
		}else{
			$sql_get_t_lk_ls = "select COUNT(*) AS NUM_ROWS from T_LK_LS WHERE LS='".$add_ls."'";
			$res_sql_get_t_lk_ls = oci_parse($conn, $sql_get_t_lk_ls);
			oci_define_by_name($res_sql_get_t_lk_ls, 'NUM_ROWS', $num_rows_get_t_lk_ls);
			oci_execute($res_sql_get_t_lk_ls);
			oci_fetch($res_sql_get_t_lk_ls);

			if ($num_rows_get_t_lk_ls != 0){
				result(3);
			}else{
				$sql_get_count_t_lk_ls = "select COUNT(*) AS NUM_ROWS from T_LK_LS WHERE T_SG_REG_ID='".$_SESSION['ID_USER']."'";
                        	$res_sql_get_count_t_lk_ls = oci_parse($conn, $sql_get_count_t_lk_ls);
                        	oci_define_by_name($res_sql_get_count_t_lk_ls, 'NUM_ROWS', $num_rows_get_count_t_lk_ls);
                        	oci_execute($res_sql_get_count_t_lk_ls);
                        	oci_fetch($res_sql_get_count_t_lk_ls);

			        $sql_get_ls_fio = "SELECT FIO FROM PAY_LS WHERE NUM='".$add_ls."'"; 
                                $res_sql_get_ls_fio = oci_parse($conn, $sql_get_ls_fio);
                                oci_define_by_name($res_sql_get_ls_fio, 'FIO', $ls_num_fio);
                                oci_execute($res_sql_get_ls_fio);
                                oci_fetch($res_sql_get_ls_fio);

                                if ($add_fio!=mb_strtoupper(trim($ls_num_fio))){
					result(0);
		                        $insert_t_lk_sessions = "INSERT INTO T_LK_SESSIONS (T_SG_REG_ID, DT_BEG, DT_END, IP_ADDR, ACT, HTTP_USER_AGENT,U_LS)
                				                VALUES ('".$_SESSION['ID_USER']."',SYSDATE,'','".htmlspecialchars($_SERVER['REMOTE_ADDR'])."',3,'".$_SESSION['HTTP_USER_AGENT']."','".$add_ls."')";
		                        $ex_insert_t_lk_sessions=oci_parse($conn, $insert_t_lk_sessions);
                		        oci_execute($ex_insert_t_lk_sessions);
				}else{ 
                            		if($num_rows_get_count_t_lk_ls==0){
						$sql_insert_t_lk_ls = "INSERT INTO T_LK_LS (T_SG_REG_ID,STATUS,ROLE_ID,DT_BEG,LS,EMAIL_COUPON, DT_EMAIL_COUPON) VALUES (".$_SESSION['ID_USER'].",1,1,SYSDATE,'".$add_ls."',0, SYSDATE)";
                                                $res_sql_insert_t_lk_ls = oci_parse($conn, $sql_insert_t_lk_ls);
                                                oci_execute($res_sql_insert_t_lk_ls);
                                                $_SESSION['LS']=$add_ls;
						$insert_t_lk_sessions = "INSERT INTO T_LK_SESSIONS (T_SG_REG_ID, DT_BEG, DT_END, IP_ADDR, ACT, HTTP_USER_AGENT)
				                                VALUES ('".$_SESSION['ID_USER']."',SYSDATE,'','".htmlspecialchars($_SERVER['REMOTE_ADDR'])."',41,'".$_SESSION['HTTP_USER_AGENT']."')";
                        			$ex_insert_t_lk_sessions=oci_parse($conn, $insert_t_lk_sessions);
			                        oci_execute($ex_insert_t_lk_sessions); 
				                result(1);
                                	}else{
						$sql_insert_t_lk_ls = "INSERT INTO T_LK_LS (T_SG_REG_ID,STATUS,ROLE_ID,DT_BEG,LS,EMAIL_COUPON, DT_EMAIL_COUPON) VALUES (".$_SESSION['ID_USER'].",1,2,SYSDATE,'".$add_ls."',".$email_coupon.", SYSDATE)";
						$res_sql_insert_t_lk_ls = oci_parse($conn, $sql_insert_t_lk_ls);
						oci_execute($res_sql_insert_t_lk_ls);
						$_SESSION['LS']=$add_ls;
						result(1);
					}

				}
			}
		}
	}else{
		result(0);
	}
function result($id)
{
    if($id==0)
	print "0";  //Не удалось добавить ЛС
    else if ($id==1)
	print "1";  //ЛС успешно привязан
    else if ($id==2)
	print "2";  //Вы превысели количество попыток отправки показаний! Отправка показаний заблокирована до завтра
    else if ($id==3)
        print "3";  //ЛС уже привязан
}
?>
