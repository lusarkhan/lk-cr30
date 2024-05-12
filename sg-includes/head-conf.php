<?php
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
session_start();
date_default_timezone_set('Europe/Samara');
$today = date('d.m.Y');
$is_actual_exucuted=0;
$check_add=0;
header('Content-Type: text/html; charset=UTF8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ob_start();

$errors = array();
?>
