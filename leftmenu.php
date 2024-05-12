<?php
if ( ! defined( 'ABSPATH' ) ) {
        define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
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

$morning = "Доброе утро";
$day = "Добрый день";
$evening = "Добрый вечер";
$night = "Доброй ночи";
 
$minyt = date("i");
$chasov = date("H");
 
if($chasov >= 04) {$hello = $morning;}
if($chasov >= 10) {$hello = $day;}
if($chasov >= 16) {$hello = $evening;}
if($chasov >= 22 or $chasov < 04) {$hello = $night;}
?>
  <style>
  .active{  
    color:#f00;  
}
  </style>
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Sidebar -->
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="./" class="<?php if($_SESSION['active_nav_menu']==0) echo "nav-link active"; else echo "nav-link";?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Общая информация
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./calc" class="<?php if($_SESSION['active_nav_menu']==3) echo "nav-link active"; else echo "nav-link";?>">
              <i class="nav-icon fas fa-money-check-alt"></i>
              <p>
                Начисления
              </p>
			  <p>
                и долги
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./payments" class="<?php if($_SESSION['active_nav_menu']==4) echo "nav-link active"; else echo "nav-link";?>">
              <i class="nav-icon fas fa-receipt"></i>
              <p>
                История платежей
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./ipu" class="<?php if($_SESSION['active_nav_menu']==7) echo "nav-link active"; else echo "nav-link";?>">
              <i class="nav-icon fas fa-exchange-alt"></i>
              <p>
                Передача показаний
              </p>
            </a>
          </li>
	  <li class="nav-item">
            <a href="./add" class="<?php if($_SESSION['active_nav_menu']==8) echo "nav-link active"; else echo "nav-link";?>">
              <i class="nav-icon fas fa-plus-square"></i>
              <p>
                Лицевые счета
              </p>
            </a>
          </li>
	  <li class="nav-item">
            <a href="./preentry" class="<?php if($_SESSION['active_nav_menu']==9) echo "nav-link active"; else echo "nav-link";?>">
              <i class="nav-icon fas fa-user-plus"></i>
              <p>
                Записаться на прием
              </p>
            </a>
          </li>
	  <li class="nav-item">
            <a href="./options" class="<?php if($_SESSION['active_nav_menu']==10) echo "nav-link active"; else echo "nav-link";?>">
              <i class="nav-icon fas fa-user-cog"></i>
              <p>
                Настройки
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
	            <div class="sidebar-footer hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="Settings">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Lock">
                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div>
  </aside>
