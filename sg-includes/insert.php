<?php
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

include 'sg-config.php';

include '/sg-includes/functions/funct.php';

include '/sg-includes/sg-db.php';
$auth='';
session_start();
date_default_timezone_set('Europe/Samara');
$today = date('d.m.Y');
$is_actual_exucuted=0;
$check_add=0;
$empty_var=1;
$host=$config['DB_HOST'];
$dbuser=$config['DB_USERNAME'];
$dbpass=$config['DB_PASSWORD'];
$dbloginget=$config['DB_LOGIN_SQL'];
$dbuserscountget=$config['DB_USERSCOUNTSQL'];
$conn = oci_connect($dbuser, $dbpass, $host);

header('Content-Type: text/html; charset=UTF8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$login = isset($_SESSION['LOGIN']) ? $_SESSION['LOGIN'] : false;

$errors = array();
$empty_var=0;

 if(time() - $_SESSION['timestamp'] > 600) { 
    unset($_SESSION['LOGIN'], $_SESSION['ID_USER'], $_SESSION['timestamp']);
    header('Location:'. SG_HOST .'/logout');
    exit;
} else {
    $_SESSION['timestamp'] = time();
}    

if($_POST['Pokaz'=='48'])
{
 $auth="dfdfdf";
	
}
	$SQL_DATE = "alter session set nls_date_format = 'dd.mm.yyyy HH24:MI:SS'";
	$RES_SQL_DATE = oci_parse($conn, $SQL_DATE);
	oci_execute($RES_SQL_DATE);


	$pok = legal_input($_POST['Pokaz']);

   json_encode(array('auth'=>$pok));
  
function legal_input($value) {
    $value = trim($value);
    $value = stripslashes($value);
    $value = htmlspecialchars($value);
    return $value;
}
if(!empty($pok) && !empty($cntnum)){
    json_encode(array('auth'=>$pok));
}else{
 echo "All fields are required";
}

function insert_data($pokk,$cntnumm){
				$add_df_selectedid = (int)$cntnumm;
				$add_pok = (int)$pokk; 
				$sql_get_dk_pay_counters = "select to_char(CNT_NUM) as CNTNUM from pay_counters where NUM=" .$_SESSION['LS']. " and CNT_SERIAL_NUM=". $add_df_selectedid." and dt_coupon = '01." . date("m", strtotime("-1 month", time()) ). ".2020'";
				$res_sql_get_dk_pay_counters = oci_parse($conn, $sql_get_dk_pay_counters);
				oci_define_by_name($res_sql_get_dk_pay_counters, 'CNTNUM', $CNTNUM); 
				oci_execute($res_sql_get_dk_pay_counters);
				oci_fetch($res_sql_get_dk_pay_counters);
				$sql_get_short_value_pay_counters = "select SHORT_VALUE from pay_counters where NUM=" .$_SESSION['LS']. " and CNT_SERIAL_NUM=". $add_df_selectedid." and dt_coupon = '01." . date("m", strtotime("-1 month", time()) ). ".2020'";
				$res_sql_get_short_value_pay_counters = oci_parse($conn, $sql_get_short_value_pay_counters);
				oci_define_by_name($res_sql_get_short_value_pay_counters, 'SHORT_VALUE', $SHORTVALUE);
				oci_execute($res_sql_get_short_value_pay_counters);
				oci_fetch($res_sql_get_short_value_pay_counters);
				
				$check_add=1;
				$sql = "SELECT COUNT(*) AS NUM_ROWS FROM SARV_PAY.SG_REG WHERE LS='".$_SESSION['LS'] ."' AND ID=".htmlspecialchars($_SESSION['ID_USER']);
				$sql_get_pay_ls_num = "SELECT COUNT(*) AS NUM_ROWS FROM SARV_PAY.PAY_LS WHERE NUM='".$_SESSION['LS']."'";
				$res = oci_parse($conn, $sql);
				$ex_sql_get_pay_ls_num = oci_parse($conn, $sql_get_pay_ls_num);
				oci_define_by_name($res, 'NUM_ROWS', $num_rows);
				oci_define_by_name($ex_sql_get_pay_ls_num, 'NUM_ROWS', $num_rows_pay_ls);
				oci_execute($res);
				oci_execute($ex_sql_get_pay_ls_num);
				oci_fetch($res);
				oci_fetch($ex_sql_get_pay_ls_num);
				if ($num_rows == 0) 
					$errors[] = "ЛС" . $_SESSION['LS']  . " не найден";
				
				if ($add_pok - $SHORTVALUE>=50)
			        $errors[] = "Проверьте корректность передаваемых показания! Существенная разность показаний.";
				if ($add_pok < $SHORTVALUE)
					$errors[] = "Проверьте корректность передаваемых показаний! Передаваемые показания не могут быть меньше учтенных.";
                if ($add_pok == $SHORTVALUE)	
					$errors[] = "Проверьте корректность передаваемых показаний! Передаваемые и учтенные показания равны.";
				if(count($errors) > 0)
				{

				}
				else
				{
					$sql_add_pok = "INSERT INTO SARV_PAY.T_POK
								(SG_REG_ID, IP_ADDR, NUM, DF, DK, POK, DT_ADD, STATUS, DT_EDIT, DESCR)
								VALUES ('".htmlspecialchars($_SESSION['ID_USER'])."','".htmlspecialchars($_SERVER['REMOTE_ADDR'])."','".htmlspecialchars($_SESSION['LS'])."','".htmlspecialchars($add_df_selectedid)."','".$CNTNUM."','".$pok."',SYSDATE,1,SYSDATE,'')";				   
					$ex_sql_add_pok = oci_parse($conn, $sql_add_pok);
					oci_execute($ex_sql_add_pok);
				}
			}
	
	
  
?>
