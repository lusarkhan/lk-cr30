<?php
header("X-XSS-Protection: 1; mode=block");
session_start();
if ( ! defined( 'ABSPATH' ) ) {
        define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

if (time() -$_SESSION['timestamp'] > 600) 
	{
		unset($_SESSION['LOGIN'], $_SESSION['ID_USER'], $_SESSION['timestamp']);
		header('Location: https://lk.host.ru/logout');
		exit;
	} 
else 
	{
		$_SESSION['timestamp'] = time();
		require('./Main_PZ_4LK.php');
	}
?>
