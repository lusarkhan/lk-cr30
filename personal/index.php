<?php
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
header("X-XSS-Protection: 1; mode=block");

include './../sg-config.php';

include './../sg-includes/functions/funct.php';


session_start();
date_default_timezone_set('Europe/Samara');
$today = date('d.m.Y');

header('Content-Type: text/html; charset=UTF8');
error_reporting(E_ALL);
ini_set('display_errors', 'Off');

ob_start();

$login = isset($_SESSION['LOGIN']) ? $_SESSION['LOGIN'] : false;
$errors = array();
$LASTD = array();
$_SESSION['active_nav_menu']=0;

if(time() -$_SESSION['timestamp'] > 600) { 
    unset($_SESSION['LOGIN'], $_SESSION['ID_USER'], $_SESSION['timestamp']);
    header('Location:'. SG_HOST .'/logout');
    exit;
} else {
    $_SESSION['timestamp'] = time();
}
if (isset($_SESSION['HTTP_USER_AGENT']))
{
    if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))
    {
        exit;
    }
}
else
{
    $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
}
if (isset($_SESSION['LOGIN'])){
	$SQL_DATE = "alter session set nls_date_format = 'dd.mm.yyyy'";
	$RES_SQL_DATE = oci_parse($conn, $SQL_DATE);
	oci_execute($RES_SQL_DATE);

      if (!empty($_SESSION['LS'])){

	if(isset($_GET['ls']))
	{
	    $sql = "SELECT COUNT(*) AS NUM_ROWS FROM T_LK_LS WHERE T_SG_REG_ID = ". $_SESSION['ID_USER'] ." and LS=".$_GET['ls'];
	    $res = oci_parse($conn, $sql);
	    oci_define_by_name($res, 'NUM_ROWS', $num_rows);
	    oci_execute($res);
	    oci_fetch($res);

	    if ($num_rows == 0)
	        $errors[] = 'Неверный ключ активации!';

	    if(count($errors) > 0){
	      header('Location:'. SG_HOST .'/personal/add');
	      exit;
	    }
	    else
	    {
		 $_SESSION['LS'] = $_GET['ls'];

	    }
	}

	$GET_SG_REG = "select ADDRESS,FIO from SARV_PAY.PAY_LS WHERE NUM=".$_SESSION['LS'];
        $get_query = oci_parse($conn, $GET_SG_REG);
        oci_execute($get_query);
        $row = oci_fetch_assoc($get_query);
        $_SESSION['FIO'] =$row['FIO'];
        $_SESSION['ADRESS'] = $row['ADDRESS'];

	$sql_get_email_coupon = 'select EMAIL_COUPON from T_LK_LS WHERE LS='.$_SESSION['LS'];
	$res_sql_get_email_coupon = oci_parse($conn, $sql_get_email_coupon);
	oci_define_by_name($res_sql_get_email_coupon, 'EMAIL_COUPON', $email_coupon_result);
	oci_execute($res_sql_get_email_coupon);
	oci_fetch($res_sql_get_email_coupon);
	$_SESSION['EMAIL_COUPON']=$email_coupon_result;

	$sql_get_message = "select ID,DT_BEG,MESSAGE,MSG_OPN from T_NOTIFY where DT_END is null and T_SG_REG_ID=".$_SESSION['ID_USER']." or T_SG_REG_ID=0";
	$res_sql_get_message = oci_parse($conn, $sql_get_message);
	oci_execute($res_sql_get_message);


        $sql_get_ls_adress = 'select ADDRESS from SARV_PAY.PAY_LS WHERE NUM='.$_SESSION['LS'];
        $res_sql_get_ls_adress = oci_parse($conn, $sql_get_ls_adress);
        oci_define_by_name($res_sql_get_ls_adress, 'ADDRESS', $ls_address);
        oci_execute($res_sql_get_ls_adress);
        oci_fetch($res_sql_get_ls_adress);
	$_SESSION['ADRESS']=$ls_address;
}else{
        header('Location:'. SG_HOST .'/personal/add');
        exit;
}

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
<?php require('../head.php'); ?>
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<!-- Google Font: Source Sans Pro -->
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <?php require('../topnavbar.php'); ?>
  <?php require('../leftmenu.php'); ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!--<div class="content-header">-->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Общая информация</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Общая информация</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    <!--</div>-->
    </section>

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <?php if($_SESSION['EMAIL_COUPON'] == '0'){
	  echo "<div class='col-12'>";
	   echo "<form id='send_change_recepiets' role='form'>";
	      echo '<div class="alert alert-warning alert-dismissible">';
                echo '<h5><i class="fa fa-info"></i> Подпишитесь на электронную квитанцию</h5>';
                 echo '<div class="custom-control custom-checkbox">';
                   echo '<input class="custom-control-input" type="checkbox" checked id="customCheckbox1" value="1">';
                     echo '<label for="customCheckbox1" class="custom-control-label">Даю согласие на получение квитанций по электронной почте и отказываюсь от бумажных квитанций</label>';
		echo '</div>';
                   echo '<div class="btn-group">';
                        echo '<button type="submit" id="btn_change_recepiets" class="btn btn-block btn-success btn-sm" onClick="change_recepiets()">Подписаться</button>';
                   echo '</div>';
              echo '</div>';
	    echo '</form>';
	  echo '</div>';
	  } else {
                echo "<div class='col-12'>";
	          echo '<div class="alert alert-success alert-dismissible">';
                    echo '<h5><i class="fa fa-info"></i> Вы получаете квитанции на электронную почту, указанную ниже. Спасибо за Ваш выбор!</h5>';
                  echo '</div>';
	        echo '</div>';
	  }
	  ?>
          <div class="col-lg-6">
            <div class="card">
              <div class="card-header border-1">
                <div class="d-flex justify-content-between">
                  <h3 class="card-title">Информация по лицевому счету</h3>
                </div>
              </div>
              <div class="card-body">
                <div class="d-flex">
                  <p class="d-flex flex-column">
                    <span class="text-bold"><i class="fa fa-map-marker" aria-hidden="true"></i> Адрес</span>
                    <span><?php echo $_SESSION['ADRESS'];?></span>
                  </p>
                </div>
		<div class="d-flex">
                  <p class="d-flex flex-column">
                    <span class="text-bold"><i class="fa fa-user" aria-hidden="true"></i> ФИО</span>
                    <span><?php echo $_SESSION['FIO'];?></span>
                  </p>
                </div>
		<div class="d-flex">
                  <p class="d-flex flex-column">
                    <span class="text-bold"><i class="fa fa-phone-square" aria-hidden="true"></i> Контактный номер</span>
                    <span><?php echo $_SESSION['PHONE_NUMBER'];?></span>
                  </p>
                </div>
		<div class="d-flex">
                  <p class="d-flex flex-column">
                    <span class="text-bold"><i class="fa fa-envelope" aria-hidden="true"></i> E-mail</span>
                    <span><?php echo $_SESSION['LOGIN'];?></span>
                  </p>
                </div>
              </div>
            </div>
          </div>
          <!-- /.col-md-6 -->
          <div class="col-lg-6">
            <div class="card">
              <div class="card-header border-1">
                <div class="d-flex justify-content-between">
                  <h3 class="card-title">Уведомления</h3>
                  <a href="javascript:void(0);">Все уведомления</a>
                </div>
              </div>
		<div class="card-body table-responsive p-0">
                <table class="table table-striped table-valign-middle">
                  <thead>
                  <tr>
		    <th>Дата</th>
                    <th>Уведомление</th>
                    <th>Далее</th>
                  </tr>
                  </thead>
                  <tbody>
                      <?php echo date("d.m.Y");?>

                      <?php echo 'Нет уведомлений!';?>
		  <?php
			while (oci_fetch($res_sql_get_message)){
				if (oci_result($res_sql_get_message, 'MSG_OPN')==1){
					echo '<tr id="row'. oci_result($res_sql_get_message, 'ID') .'">';
					echo '<td>';
					echo oci_result($res_sql_get_message, 'DT_BEG');
					echo '</td>';
					echo '<td>';
					echo oci_result($res_sql_get_message, 'MESSAGE');
					echo '</td>';
					echo '<td>';
					echo '<a href="/personal/notify?msg_id='.oci_result($res_sql_get_message, 'ID').'" class="text-muted">';
        	                    	echo '<i class="fas fa-search"></i>';
					echo '</a>';
					echo '</td>';
					echo '</tr>';
				}else{
                                        echo '<tr id="row'. oci_result($res_sql_get_message, 'ID') .'">';
                                        echo '<td>';
                                        echo '<strong>' . oci_result($res_sql_get_message, 'DT_BEG') . '</strong>';
                                        echo '</td>';
                                        echo '<td>';
                                        echo '<strong>' . oci_result($res_sql_get_message, 'MESSAGE') . '</strong>';
                                        echo '</td>';
                                        echo '<td>';
                                        echo '<a href="/personal/notify?msg_id='.oci_result($res_sql_get_message, 'ID').'" class="text-muted">';
                                        echo '<i class="fas fa-search"></i>';
                                        echo '</a>';
                                        echo '</td>';
                                        echo '</tr>';
				}
			}
		  ?>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col-md-6 -->
        <!-- /.row -->
	</div>
      </div>
      <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
        <?php require "../footer.php" ?>
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- jQuery -->
<!-- jQuery UI 1.11.4 -->
<script src="../plugins/jquery-ui/jquery-ui.min.js"></script>
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE -->
<script src="../dist/js/demo.js"></script>
<script src="../dist/js/pages/dashboard3.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- jQuery Knob Chart -->
<script src="../plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<!-- TempusdominBootstrap 4 -->
<script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- overlayScrollbars -->
<script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.js"></script>
<!-- SweetAlert2 -->
<script src="../plugins/sweetalert2/sweetalert2.all.min.js"></script>
<!-- Toastr -->
<script src="../plugins/toastr/toastr.min.js"></script>
<!-- OPTIONAL SCRIPTS -->
<script type="text/javascript">
function change_recepiets(){
		event.preventDefault();
		var $email_coupon='';
		if (document.querySelector('.custom-control-input').checked){
			$email_coupon=1;
			$.ajax({ url: '/sg-includes/__change_recepiets.php',
				data: {'email_coupon' : $email_coupon},
				type: 'get',
				cache: false,
				dataType: 'html',
				success: function(data) {
					if(data==1){
							toastr.options = {
								"closeButton": false,
								"debug": false,
								"newestOnTop": false,
								"progressBar": false,
								"positionClass": "toast-top-right",
								"preventDuplicates": false,
								"onclick": null,
								"showDuration": "300",
								"hideDuration": "1000",
								"timeOut": "2500",
								"extendedTimeOut": "1000",
								"showEasing": "swing",
								"hideEasing": "linear",
								"showMethod": "fadeIn",
								"hideMethod": "fadeOut"
					}
					toastr["success"]("Вы подписались на электронную квитанцию!", "")
					document.location.reload();
					}
					else if(data==0){
							toastr.options = {
								"closeButton": false,
								"debug": false,
								"newestOnTop": false,
								"progressBar": false,
								"positionClass": "toast-top-right",
								"preventDuplicates": false,
								"onclick": null,
								"showDuration": "300",
								"hideDuration": "1000",
								"timeOut": "1500",
								"extendedTimeOut": "1000",
								"showEasing": "swing",
								"hideEasing": "linear",
								"showMethod": "fadeIn",
								"hideMethod": "fadeOut"
					}
					toastr["error"]("Не удалось выполнить запрос!", "Ошибка")
					}
			}
			})
		}else{
			$email_coupon=0;
			toastr.options = {
								"closeButton": false,
								"debug": false,
								"newestOnTop": false,
								"progressBar": false,
								"positionClass": "toast-top-right",
								"preventDuplicates": false,
								"onclick": null,
								"showDuration": "300",
								"hideDuration": "1000",
								"timeOut": "2500",
								"extendedTimeOut": "1000",
								"showEasing": "swing",
								"hideEasing": "linear",
								"showMethod": "fadeIn",
								"hideMethod": "fadeOut"
			}
			toastr["error"]("Чтобы подписаться на рассылку квитанций по электронной почте, необходимо ваше согласие!", "Ошибка")
		}
 }	 
</script>
</body>
</html>
