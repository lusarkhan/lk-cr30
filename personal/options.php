<?php
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

include './../sg-config.php';

session_start();

header('Content-Type: text/html; charset=UTF8');

error_reporting(E_ALL);

ini_set('display_errors', 'On');

ob_start();

date_default_timezone_set('Europe/Samara');

$login = isset($_SESSION['LOGIN']) ? $_SESSION['LOGIN'] : false;
$ID_USER = isset($_SESSION['ID_USER']) ? $_SESSION['ID_USER'] : false;
$errors = array();
$_SESSION['active_nav_menu']=10;

 if(time() - $_SESSION['timestamp'] > 600) { 
    unset($_SESSION['LOGIN'], $_SESSION['ID_USER'], $_SESSION['timestamp']);
    header('Location:'. SG_HOST .'/logout');
    exit;
} else {
    $_SESSION['timestamp'] = time();
    $_SESSION['url']=$_SERVER['REQUEST_URI'];
}    
	$sql_get_phone_number = 'select PHONE_NUMBER from sg_reg WHERE ID=' . htmlspecialchars($ID_USER);
	$res_sql_get_phone_number  = oci_parse($conn, $sql_get_phone_number);
	oci_define_by_name($res_sql_get_phone_number , 'PHONE_NUMBER', $phone_number);
	oci_execute($res_sql_get_phone_number);
	oci_fetch($res_sql_get_phone_number);


?>
<!DOCTYPE html>
<html>

<?php require('../head.php'); ?>
<style>
.card {
    box-shadow: 0 0 1px rgb(0 0 0 / 13%), 0 1px 3px rgb(0 0 0 / 20%);
    margin-bottom: 1rem;
    border-radius: 9px;
}
.invoice {
    background: #fff;
    border: 1px solid rgba(0,0,0,.125);
    position: relative;
    border-radius: 24px;
}
.styled-table {
    border-collapse: collapse;
    font-size: 0.9em;
    font-family: sans-serif;
    min-width: 400px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
}
.styled-table thead tr {
    background-color: #009879;
    color: #ffffff;
    text-align: left;
}
.styled-table th,
.styled-table td {
    padding: 12px 15px;
}
.styled-table tbody tr {
    border-bottom: 1px solid #dddddd;
}

.styled-table tbody tr:nth-of-type(even) {
    background-color: #f3f3f3;
}

.styled-table tbody tr:last-of-type {
    border-bottom: 2px solid #009879;
}
.styled-table tbody tr.active-row {
    font-weight: bold;
    color: #009879;
}
.styled-table tbody tr:hover td {
    color: #009879;
	background:#f3f3f3;
}
</style>
<body class="hold-transition sidebar-mini layout-fixed" onload=init()>
<div class="wrapper">
<?php require('../topnavbar.php'); ?>
<?php require('../leftmenu.php');?>

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Настройки<h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Настройки</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
 <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
			<div class="col-md-12">
	<div class="invoice p-3 mb-3">
        <div class="card card-default">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-user-cog"></i>
                  Настройки
                </h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="callout callout-danger">
					<div class="row mb-2">
						<div class="col-sm-0">
							<span style="font-size: 2em; color: #bd2130;">
								<i class="fas fa-key"></i>
							</span>
						</div>
						<div class="col-sm-11">
							<h5>Пароль</h5>
							<p>Последний раз пароль менялся <b><?php echo($_SESSION['DT_UPDATE']);?></b></p>
						</div>
						<div class="col-sm-0">
							<form action="/recover-password">
								<button type="submit" class="btn btn-info btn-flat">Изменить</button></span>
							</form>
							</ol>
						</div>
					</div>
					</div>
				<div class="callout callout-success">
                  	<div class="row mb-2">
						<div class="col-sm-0">
							<span style="font-size: 2em; color: Green;">
								<i class="fas fa-phone-square-alt"></i>
							</span>
						</div>
						<div class="col-sm-11">
							<h5>Мобильный телефон</h5>
							<!--<p><b><?php $str = substr($phone_number,0,7) . '***' . substr($phone_number, -2); echo $str;?></b></p>-->
							<p><b><?php echo $phone_number; ?></b></p>
						</div>
						<div class="col-sm-0">
							</ol>
						</div>
					</div>
                </div>
                <div class="callout callout-info">
                  	<div class="row mb-2">
						<div class="col-sm-0">
							<span style="font-size: 2em; color: #117a8b;">
								<i class="fas fa-envelope-open"></i>
							</span>
						</div>
						<div class="col-sm-11">
							<h5>Адрес электронной почты</h5>
							<p><b><?php print_r($_SESSION['LOGIN']);?></b></p>
						</div>
					</div>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
	   </div>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
	</div>
      <!-- /.modal -->
  </div>
  <!-- /.content-wrapper -->
 
  <!-- footer content -->
        <?php require "../footer.php" ?>
        <!-- /footer content -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<?php require "../scripts.php" ?>
<!-- SweetAlert2 -->
<script src="../../plugins/sweetalert2/sweetalert2.all.min.js"></script>
<!-- Toastr -->
<script src="../../plugins/toastr/toastr.min.js"></script>
<script type="text/javascript">

 $('#updatephone').on('click',function(e){
		e.preventDefault();
 			Swal.fire({
				title: 'Введите новый номер мобильного телефона',
				input: 'text',
				inputAttributes: {
				autocapitalize: 'off'
				},
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Изменить',
				cancelButtonText: 'Отмена',
			}).then((result) => {
				if (result.value){
						$.ajax({ url: '/sg-includes/__updatephone.php',
							data: {'phone' : result.value},
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
										"timeOut": "1500",
										"extendedTimeOut": "1000",
										"showEasing": "swing",
										"hideEasing": "linear",
										"showMethod": "fadeIn",
										"hideMethod": "fadeOut"
									}
									toastr["success"]("Номер успешно изменен!", "")
									
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
									toastr["error"]("Не удалось изменить номер мобильного телефона!", "")
								}
							},
							error: function(request, status, error){
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
								toastr["error"](data, "")
							}
						})
					}
		})
 });
</script>
</body>
</html>
