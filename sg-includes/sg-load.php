<?php
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
include './../sg-includes/sg-sql.php';
session_start();
header('Content-Type: text/html; charset=UTF8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ob_start();
$errors = array();

$login = isset($_SESSION['LOGIN']) ? $_SESSION['LOGIN'] : false;
if (isset($_SESSION['LOGIN'])){
	exit;
	}
	else {
	}
	
?>
