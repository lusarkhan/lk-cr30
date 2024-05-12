<?php
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
session_start();
date_default_timezone_set('Europe/Samara');
header('Content-Type: text/html; charset=UTF8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ob_start();
include './sg-config.php';
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
<!DOCTYPE html>
<html>
<?php require('./head.php'); ?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <?php require('topnavbar.php'); ?>
  <?php require('leftmenu.php'); ?>
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>404 Страница не найдена</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">404 Страница не найдена</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
    <section class="content">
      <div class="error-page">
        <h2 class="headline text-warning"> 404</h2>
        <div class="error-content">
          <h3><i class="fas fa-exclamation-triangle text-warning"></i> Извините! Страница не найдена.</h3>
          <p>
            Извините, но мы не смогли найти эту страницу.
            Вы можете <a href="/">вернуться на главную страницу.</a>
          </p>
        </div>
      </div>
    </section>
  </div>
  <?php require "./footer.php" ?>
  <aside class="control-sidebar control-sidebar-dark">
  </aside>
</div>
<?php require "./scripts.php" ?>
</body>
</html>

