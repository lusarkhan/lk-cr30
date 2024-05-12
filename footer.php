<?php
if ( ! defined( 'ABSPATH' ) ) {
        define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
header("X-XSS-Protection: 1; mode=block");
date_default_timezone_set('Europe/Samara');
header('Content-Type: text/html; charset=UTF8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ob_start();
$login = isset($_SESSION['LOGIN']) ? $_SESSION['LOGIN'] : false;
$timestamp = isset($_SESSION['timestamp']) ? $_SESSION['timestamp'] : false;

$errors = array();
 if(time() -$_SESSION['timestamp'] > 600) {
    unset($_SESSION['LOGIN'], $_SESSION['ID_USER'], $_SESSION['timestamp']);
    header('Location:'.'/logout');
    exit;
} else {
    $_SESSION['timestamp'] = time();
}
    if (isset($_SESSION['LOGIN'])){

    }
    else {
        $login = '';
        if (isset($_COOKIE['CookieMy'])){
                $login = htmlspecialchars($_COOKIE['CookieMy']);
        }
                header('Location:'.'/login');
    }
?>
        <!-- footer content -->
	<footer class="main-footer">
           <a href="https://host.ru"><strong>Акционерное общество "Цифровые Решения" &copy; <?php echo date("Y"); ?></strong></a>
        <div class="float-right d-none d-sm-inline-block">
		<span><a href="https://lk.host.ru/pdn2022.pdf">Правила обработки персональных данных</a></span>
        </div>
        </footer>
        <!-- /footer content -->
