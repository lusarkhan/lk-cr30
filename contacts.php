<?php

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
include './sg-config.php';

include './sg-includes/functions/funct.php';

session_start();
date_default_timezone_set('Europe/Samara');
$today = date('d.m.Y H:i:s');

header('Content-Type: text/html; charset=UTF8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ob_start();
$login = isset($_SESSION['LOGIN']) ? $_SESSION['LOGIN'] : false;
$timestamp = isset($_SESSION['timestamp']) ? $_SESSION['timestamp'] : false;
$errors = array();
$_SESSION['active_nav_menu']=4;


 if(time() -$_SESSION['timestamp'] > 600) { 
    unset($_SESSION['LOGIN'], $_SESSION['ID_USER'], $_SESSION['timestamp']);
    header('Location:'. SG_HOST .'/logout');
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
			header('Location:'. SG_HOST .'/login');
		}
?>
<!DOCTYPE html>
<html>

<?php require('./head.php'); ?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <?php require('./topnavbar.php'); ?>
  <?php require('./leftmenu.php'); ?>
	<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Контакты</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Контакты</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="card card-solid">
        <div class="card-body pb-0">
          <div class="row d-flex align-items-stretch">
            <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch">
              <div class="card bg-light">
                <div class="card-header text-muted border-bottom-0">
                  Центральный офис
                </div>
                <div class="card-body pt-0">
                  <div class="row">
                    <div class="col-7">
                      <h2 class="lead"><b>ГОРЯЧАЯ ЛИНИЯ ЖКХ</b></h2>
                      <p class="text-muted text-sm"><b> </b> Передача показаний / Консультации</p>
                      <ul class="ml-4 mb-0 fa-ul text-muted">
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> Адрес: г. Астрахань, ул. Дж. Рида, 37В</li>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Тел.: +7 (8512) 48-23-92</li>
                      </ul>
                    </div>
                    <div class="col-5 text-center">
                      <img src="images/user.png" alt="" class="img-circle img-fluid">
                    </div>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="text-right">
                    <a href="tel:+78512482392" class="btn btn-sm btn-primary">
                      <i class="fas fa-phone"></i> Позвонить
                    </a>
                  </div>
                </div>
              </div>
            </div>
           <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch">
              <div class="card bg-light">
                <div class="card-header text-muted border-bottom-0">
                  Центральный офис
                </div>
                <div class="card-body pt-0">
                  <div class="row">
                    <div class="col-7">
                      <h2 class="lead"><b>ЗАПИСЬ НА ПРИЕМ</b></h2>
                      <p class="text-muted text-sm"><b> </b> Запись на прием к специалисту</p>
                      <ul class="ml-4 mb-0 fa-ul text-muted">
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> Адрес: г. Астрахань, ул. Дж. Рида, 37В</li>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Тел.: +7 (8512) 48-23-92 (доб. 4)</li>
                      </ul>
                    </div>
                    <div class="col-5 text-center">
                      <img src="images/user.png" alt="" class="img-circle img-fluid">
                    </div>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="text-right">
                    <a href="tel:+78512482392" class="btn btn-sm btn-primary">
                      <i class="fas fa-phone"></i> Позвонить
                    </a>
                  </div>
                </div>
              </div>
            </div>
			<div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch">
              <div class="card bg-light">
                <div class="card-header text-muted border-bottom-0">
                  Центральный офис
                </div>
                <div class="card-body pt-0">
                  <div class="row">
                    <div class="col-7">
                      <h2 class="lead"><b>ФАКС</b></h2>
                      <p class="text-muted text-sm"><b> </b></p>
                      <ul class="ml-4 mb-0 fa-ul text-muted">
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> Адрес: г. Астрахань, ул. Дж. Рида, 37В</li>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Тел.: +7 (8512) 48-23-91</li>
                      </ul>
                    </div>
                    <div class="col-5 text-center">
                      <img src="images/user.png" alt="" class="img-circle img-fluid">
                    </div>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="text-right">
                  </div>
                </div>
              </div>
            </div>
        <!-- /.card-footer -->
      </div>
      <!-- /.card -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
        
<!-- footer content -->
  <?php require "./footer.php" ?>
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<?php require "./scripts.php" ?>
</body>
</html>

