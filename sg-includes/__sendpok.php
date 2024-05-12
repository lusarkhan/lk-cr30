<?php
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
header("X-XSS-Protection: 1; mode=block");
include '../sg-config.php';

session_start();
date_default_timezone_set('Europe/Samara');
$today = date('d.m.Y');

header('Content-Type: text/html; charset=UTF8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$login = isset($_SESSION['LOGIN']) ? $_SESSION['LOGIN'] : false;

$pok = isset($_GET['pok']) ? htmlspecialchars(trim($_GET['pok'])) : null;
$cntnum = isset($_GET['cntnum']) ? htmlspecialchars(trim($_GET['cntnum'])) : null;
$cntserialnumber = isset($_GET['cntserialnumber']) ? htmlspecialchars(trim($_GET['cntserialnumber'])) : null;
$errors = array();

 if(time() - $_SESSION['timestamp'] > 600) { 
    unset($_SESSION['LOGIN'], $_SESSION['ID_USER'], $_SESSION['timestamp']);
    header('Location:'. SG_HOST .'/logout');
    exit;
} else {
    $_SESSION['timestamp'] = time();
}
	$SQL_DATE = "alter session set nls_date_format = 'dd.mm.yyyy'";
	$RES_SQL_DATE = oci_parse($conn, $SQL_DATE);
	oci_execute($RES_SQL_DATE);

	$sql_get_insert_count = "select COUNT(*) AS NUM_ROWS FROM SARV_PAY.T_POK WHERE DT_ADD like to_date(to_char(sysdate,'dd.mm.yyyy'),'dd.mm.yyyy') and NUM=".htmlspecialchars($_SESSION['LS'])." and DK=".$cntnum;
	$res_sql_get_insert_count = oci_parse($conn, $sql_get_insert_count);
	oci_define_by_name($res_sql_get_insert_count, 'NUM_ROWS', $send_count_rows);
	oci_execute($res_sql_get_insert_count);
	oci_fetch($res_sql_get_insert_count);

	if ($send_count_rows >=3){
		result(2);
	}
	else{
		if(($pok>=0) && !empty($cntnum)){
			$sql_add_pok = "INSERT INTO SARV_PAY.T_POK
						(SG_REG_ID, IP_ADDR, NUM, DF, DK, POK, DT_ADD, STATUS, DT_EDIT, DESCR)
					VALUES ('".htmlspecialchars($_SESSION['ID_USER'])."','".htmlspecialchars($_SERVER['REMOTE_ADDR'])."','".htmlspecialchars($_SESSION['LS'])."','".$cntserialnumber."','".$cntnum."','".$pok."',SYSDATE,1,SYSDATE,'')";				   
			$ex_sql_add_pok = oci_parse($conn, $sql_add_pok);
			oci_execute($ex_sql_add_pok);
			result(1);
		}else{
			result(0);
		}
	}
function result($id)
{
	if($id==0)
		print "0";  //Не удалось передать показания!
        else if ($id==1)
		print "1";  //Показания успешно переданы!
	else if ($id==2)
		print "2";  //Вы превысели количество попыток отправки показаний! Отправка показаний заблокирована до завтра
	else if ($id==null)
		print "0";
}

?>
