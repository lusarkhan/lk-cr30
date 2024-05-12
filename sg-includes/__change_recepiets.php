<?php
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
include './../sg-config.php';
session_start();
date_default_timezone_set('Europe/Samara');
header('Content-Type: text/html; charset=UTF8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ob_start();

$login = isset($_SESSION['LOGIN']) ? $_SESSION['LOGIN'] : false;

$email_coupon = isset($_GET['email_coupon']) ? htmlspecialchars(trim($_GET['email_coupon'])) : null;
$errors = array();
if(time() - $_SESSION['timestamp'] > 600) { 
    unset($_SESSION['LOGIN'], $_SESSION['ID_USER'], $_SESSION['timestamp']);
    header('Location:'. SG_HOST .'/logout');
    exit;
} else {
    $_SESSION['timestamp'] = time();
}
	if(!empty($email_coupon) && $email_coupon==1){
		$sql_update_email_coupon="update T_LK_LS set email_coupon=".$email_coupon.", dt_email_coupon=SYSDATE where LS=".htmlspecialchars($_SESSION['LS']);
		$ex_sql_update_email_coupon = oci_parse($conn, $sql_update_email_coupon);
		oci_execute($ex_sql_update_email_coupon);
		$_SESSION['EMAIL_COUPON']=1;
		result(1);
	}else{
   	        $_SESSION['EMAIL_COUPON']=0;
        	result(0);
	}
function result($id)
{
	if($id==0)
		print "0";
    else if ($id==1)
		print "1"; 
	else if ($id==2)
		print "2";  
}
?>
