<?php
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
include './../sg-config.php';
$conn='';
session_start();
date_default_timezone_set('Europe/Samara');
$today = date('d.m.Y');
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
$dt = isset($_GET['dt']) ? htmlspecialchars(trim($_GET['dt'])) : null;
$ls = isset($_GET['ls']) ? htmlspecialchars(trim($_GET['ls'])) : null;
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
	
	$sql_get_payments = "select dtz_pay AS DTZPAY, (select name from Max_payments_collector where kp_num=m.kp_num) AS SBOR,
																	(select coupon_kind||' '||name from Max_payments_service where coupon_kind=m.coupon_kind and rownum=1) AS USLUGA,
																	coupon_start AS DTBEG, coupon_end AS DETEND,
																	summa AS SUMMA from Max_payments_all m 
																	where lsnum_dtpay='".htmlspecialchars($_SESSION['LS'])."'||'_'||to_char(trunc(to_date('".$dt."','dd.mm.yyyy'),'MM'),'mm.yyyy') 
																	and id_parent is null order by 1,2,3,4,5";
	$res_get_payments = oci_parse($conn, $sql_get_payments);
	oci_execute($res_get_payments);
	oci_fetch($res_get_payments);
	print '<div class="col-12 table-responsive">';
		print '<table id="datatable-buttons" class="styled-table" style="width:100%">';
			print '<thead>';
				print '<tr>';
					print '<th rowspan="2">Дата платежа</th>';
					print '<th rowspan="2" style="text-align: center;">Сборщик</th>';
					print '<th rowspan="2" style="text-align: center;">Услуга</th>';
					print '<th rowspan="2" style="text-align: center;">Начало периода</th>';
					print '<th rowspan="2" style="text-align: center;">Конец периода</th>';
					print '<th rowspan="2" style="text-align: center;">Сумма, руб.</th>';
				print '</tr>';
			print '</thead>';
			print '<tbody>';
				while ($row=oci_fetch_array($res_get_payments,OCI_RETURN_NULLS+OCI_ASSOC)){
						print "<tr>"; 
						print "<td>". oci_result($res_get_payments, 'DTZPAY') . "</td>";
						print "<td>". oci_result($res_get_payments, 'SBOR') . "</td>";
						print "<td>". oci_result($res_get_payments, 'USLUGA') . "</td>";
						print "<td>". oci_result($res_get_payments, 'DTBEG') . "</td>";
						print "<td>". oci_result($res_get_payments, 'DETEND') . "</td>";
						print "<td>". oci_result($res_get_payments, 'SUMMA') . "</td>";
				}
				print '</tr>';
				print '</tbody>';
		print '</table>';
	print '</div>';	
?>
