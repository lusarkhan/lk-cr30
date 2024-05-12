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
?>
<!-- page content -->
        <div class="right_col" role="main">
          <div class="">

            <div class="row">
              <div class="col-md-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Сводка расходов <small>Еженедельный</small></h2>
                    <div class="filter">
                      <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                        <span>December 30, 2014 - January 28, 2015</span> <b class="caret"></b>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                 
                </div>
              </div>
            </div>
          </div>
        </div>
