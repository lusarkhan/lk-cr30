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
 <!-- Navbar -->
   <nav class="main-header navbar navbar-expand navbar-white navbar-light"> 
   <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="/" class="nav-link">Главная</a>
      </li>
    </ul>

   <form class="form-inline ml-3">
      <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" type="search" placeholder="Поиск" aria-label="Поиск">
        <div class="input-group-append">
          <button class="btn btn-navbar" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </form>

    <ul class="navbar-nav ml-auto">

      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
			<strong id="toplstext" class="nav-item"></strong><strong id="topls"></strong>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <?php
                    $sql_get_t_lk_ls = "select LS from T_LK_LS WHERE T_SG_REG_ID=".$_SESSION['ID_USER'];
                    $res_sql_get_t_lk_ls = oci_parse($conn, $sql_get_t_lk_ls);
                    oci_execute($res_sql_get_t_lk_ls, OCI_COMMIT_ON_SUCCESS);
                    while(($row = oci_fetch_array($res_sql_get_t_lk_ls, OCI_BOTH))){
                        echo '<a href="/personal/?ls='.$row[0].'" class="dropdown-item"><i class="fas fa-user float-right"></i> ' .$row[0]. ' </a>';
                        echo '<div class="dropdown-divider"></div>';
                    }
                    oci_free_statement($res_sql_get_t_lk_ls);
                ?>
	  <a href="/personal/add" class="dropdown-item"><i class="fas fa-user float-right"></i> Управление лицевыми счетами</a>
                  <div class="dropdown-divider"></div>
          <a class="dropdown-item"  href="/logout"> Выход <i class="fa fa-sign-out-alt float-right"></i></a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">0</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">Нет уведомлений</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> Уведомления
            <span class="float-right text-muted text-sm"></span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">Все уведомления</a>
        </div>
      </li>
	  <li class="nav-item">
	  <a class="nav-link" href="/logout" title="Выход">
          <i class="fas fa-sign-out-alt"></i>
      </a>
	  </li>
    </ul>
  </nav>
<script>
var ls = "<?= $_SESSION['LS'] ?>";
if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
   document.getElementById("toplstext").innerText="";
   document.getElementById("topls").innerText=ls;
   document.getElementById("topls").style.font = "bold 11px arial,serif";
}else{
   document.getElementById("toplstext").innerText="Выберите лицевой счет: ";
   document.getElementById("topls").innerText=ls;
   document.getElementById("topls").style.font = "bold 14px arial,serif";
}
</script>
