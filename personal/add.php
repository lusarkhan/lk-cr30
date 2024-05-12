<?php
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
header("X-XSS-Protection: 1; mode=block");
include './../sg-config.php';

session_start();
date_default_timezone_set('Europe/Samara');
$today = date('d.m.Y');
header('Content-Type: text/html; charset=UTF8');
error_reporting(E_ALL);
ini_set('display_errors', 'off');
ob_start();
$login = isset($_SESSION['LOGIN']) ? $_SESSION['LOGIN'] : false;
$errors = array();
$_SESSION['active_nav_menu']=8;
$LASTD = array();
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
	$_SESSION['url']=$_SERVER['REQUEST_URI'];

	$sql_get_added_ls = "select * from t_lk_ls where T_SG_REG_ID=".$_SESSION['ID_USER'];
	$res_sql_get_added_ls = oci_parse($conn,$sql_get_added_ls);
	oci_execute($res_sql_get_added_ls);

	if(isset($_POST['MainLS']))
        {
           $ls_selected_id = (int)$_POST['MainLS'];
           $sql_update_role_id_t_lk_ls = "UPDATE T_LK_LS SET ROLE_ID=2 WHERE T_SG_REG_ID = ".$_SESSION['ID_USER'];
           $ex_sql_update_role_id_t_lk_ls = oci_parse($conn, $sql_update_role_id_t_lk_ls);
           oci_execute($ex_sql_update_role_id_t_lk_ls);

           $sql_t_lk_ls_update_role_id = "UPDATE T_LK_LS SET ROLE_ID=1 WHERE ID = ".$ls_selected_id;
           $ex_sql_t_lk_ls_update_role_id = oci_parse($conn, $sql_t_lk_ls_update_role_id);
           oci_execute($ex_sql_t_lk_ls_update_role_id);

           $sql_get_main_ls = "select LS from t_lk_ls where ID=".$ls_selected_id;
           $res_sql_get_main_ls = oci_parse($conn,$sql_get_main_ls);
 	   oci_execute($res_sql_get_main_ls);
           $row_main_ls = oci_fetch_assoc($res_sql_get_main_ls);
	   $_SESSION['LS']=$row_main_ls['LS'];
           header('Location:'. SG_HOST .'/personal/add'); 
	}

	if(isset($_POST['deleteLS']))
    	{
           $ls_selected_id = (int)$_POST['deleteLS'];
	   $sql_delete_ls = "DELETE FROM T_LK_LS WHERE ID = ".$ls_selected_id;
           $ex_sql_delete_ls = oci_parse($conn, $sql_delete_ls);
           oci_execute($ex_sql_delete_ls);

           $insert_t_lk_sessions = "INSERT INTO T_LK_SESSIONS (T_SG_REG_ID, DT_BEG, DT_END, IP_ADDR, ACT, HTTP_USER_AGENT)
                             VALUES ('".$_SESSION['ID_USER']."',SYSDATE,'','".htmlspecialchars($_SERVER['REMOTE_ADDR'])."',4,'".$_SESSION['HTTP_USER_AGENT']."')";
           $ex_insert_t_lk_sessions=oci_parse($conn, $insert_t_lk_sessions);
           oci_execute($ex_insert_t_lk_sessions);
	   $_SESSION['LS']=$_SESSION['MAINLS'];

	   header('Location:'. SG_HOST .'/personal/add');
        }

	if(isset($_POST['startepd'])){
	   $ls_selected_id = (int)$_POST['startepd'];
	   $sql_start_email_coupon = "UPDATE T_LK_LS SET EMAIL_COUPON = 1, DT_EMAIL_COUPON=SYSDATE WHERE ID = ".$ls_selected_id;
           $ex_sql_start_email_coupon = oci_parse($conn, $sql_start_email_coupon);
           oci_execute($ex_sql_start_email_coupon);
		header('Location:'. SG_HOST .'/personal/add');
	}
	if(isset($_POST['cancelepd'])){
	   $ls_selected_id = (int)$_POST['cancelepd'];
	   $sql_cancel_email_coupon = "UPDATE T_LK_LS SET EMAIL_COUPON = 0, DT_EMAIL_COUPON=SYSDATE WHERE ID = ".$ls_selected_id;
           $ex_sql_cancel_email_coupon = oci_parse($conn, $sql_cancel_email_coupon);
           oci_execute($ex_sql_cancel_email_coupon);
	   header('Location:'. SG_HOST .'/personal/add');
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
<style>
.invoice {
    background: #fff;
    border: 1px solid rgba(0,0,0,.125);
    position: relative;
    border-radius: 24px;
}
</style>
<body class="hold-transition sidebar-mini layout-fixed" onload="init()">
<div class="wrapper">

  <?php require('../topnavbar.php'); ?>
  <?php require('../leftmenu.php'); ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Управление лицевыми счетами</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Управление лицевыми счетами</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- jquery validation -->
            <div class="invoice p-3 mb-3">
	     <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title">Добавить лицевой счет</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="POST" role="form" id="quickForm">
                <div class="card-body">
                  <div class="form-group">
                    <label for="inputAddLS_">Номер лицевого счета</label>
                    <input type="number" name="inputAddLS_" class="form-control" id="inputAddLS" placeholder="Например: 100000000" required pattern="^[0-9]{9}" />
                  </div>
		  <div class="form-group">
		  	<div class="row">
				<div class="col-12 col-sm-6 col-md-3">
					<label for="inputAddFAM_">Фамилия</label>
					<input type="text" name="inputAddFAM_" class="form-control inputaddfam" id="inputAddFAM" placeholder="Введите Фамилию" required pattern="^[А-Яа-яЁё\s]+$" />
				</div>
				<div class="col-12 col-sm-6 col-md-3">
					<label for="inputAddIM_">Имя</label>
					<input type="text" name="inputAddIM_" class="form-control inputaddim" id="inputAddIM" placeholder="Введите Имя" required pattern="^[А-Яа-яЁё\s]+$" />
				</div>
				<div class="col-12 col-sm-6 col-md-3">
					<label for="inputAddOTCH_">Отчество</label>
					<input type="text" name="inputAddOTCH_" class="form-control inputaddotch" id="inputAddOTCH" placeholder="Введите Отчество" required pattern="^[А-Яа-яЁё\s]+$" />
				</div>
			</div>
		  </div>
                  <div class="form-group mb-0">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="AddLSCheckBox" class="custom-control-input" id="AddLSCheckBox">
                      <label class="custom-control-label" for="AddLSCheckBox">Даю согласие на получение квитанций по электронной почте.</label>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
			<button type="submit" id="addls" name="addls" class="btn btn-info btn-flat addls">Привязать</button>	
                </div>
              </form>
            </div>
            </div>
            <!-- /.card -->
            </div>
          <!--/.col (left) -->
          <!-- right column -->
          <div class="col-md-6">

          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
	<div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- jquery validation -->
	    <div class="invoice p-3 mb-3">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Текущие лицевые счета</h3>
              </div>
	      <table id="datatable-buttons" class="table table-striped table-valign-middle">
                  <thead>
                  <tr>
                    <th>Лицевой счет</th>
                    <th>Дата добавления</th>
		    <th>Сделать основным</th>
		    <th>Электронная квитанция</th>
                  </tr>
                  </thead>
                  <tbody>
                  <!--<tr>-->

		  <?php
	            while (oci_fetch($res_sql_get_added_ls)){
			    echo "<tr>"; 
	 		    echo "<td>". oci_result($res_sql_get_added_ls, 'LS') . "</td>";
			    echo "<td>". oci_result($res_sql_get_added_ls, 'DT_BEG') . "</td>";
		            if(oci_result($res_sql_get_added_ls, 'ROLE_ID')=='2'){
			    echo '<td><form method="POST">
                                        <div class="icheck-success d-inline">
                                                <button type="submit" style="width:20%" name="MainLS" value="'. oci_result($res_sql_get_added_ls, 'ID') .'" class="btn btn-default btn-sm" data-toggle="tooltip" data-container="body" title="Основной"><i class="fas fa-check"></i></button>
                                        </div>
                                  </form></td>';
			    }else{
			    	echo '<td>Основной</td>';
			    }

			    echo '<td class="jsgrid-cell jsgrid-align-center" style="width: 100px;">
			         <center>';
			    if (oci_result($res_sql_get_added_ls, 'EMAIL_COUPON')==1){ 
				echo '<form method="POST">';
				echo '<div class="btn-group">
					<span class="badge badge-success">Да</span>
					</div>
					</form>
					</center>
					</td>';
			   } else {
				echo '<form method="POST">';
				echo '<div class="btn-group">
					<button type="submit" value="' . oci_result($res_sql_get_added_ls, 'ID') .'" class="btn btn-default btn-sm" data-container="body" name="startepd" title="Получать электронную квитанцию">
						<i class="fa fa-play-circle" aria-hidden="true"></i>
					</button>
					</div>
					</form>
					</center>
					</td>';
			   }
			    echo '<td><form method="POST">
			    	  <center>
					<div class="icheck-success d-inline">
						<button type="submit" style="width:20%" name="deleteLS" value="'. oci_result($res_sql_get_added_ls, 'ID') .'" class="btn btn-default btn-sm" data-toggle="tooltip" data-container="body" title="Удалить"><i class="far fa-trash-alt"></i></button>
					</div>
				  </center>
				  </form>
                                  </td>';
			    echo "</tr>";
		  } 
		  ?>
                  </tbody>
                </table>
            </div>
            <!-- /.card -->
            </div>
            </div>
          <!--/.col (left) -->
          <!-- right column -->
          <div class="col-md-6">

          </div>
          <!--/.col (right) -->
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <?php require "../footer.php" ?>
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../../plugins/jquery-ui/jquery-ui.min.js"></script>
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
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
<script type="text/javascript">
function init(){
document.querySelector('.inputaddfam').addEventListener('keyup', function(){
	this.value = this.value.replace(/[^[А-Яа-яЁё\s]+$/g, '');
});

document.querySelector('.inputaddim').addEventListener('keyup', function(){
	this.value = this.value.replace(/[^[А-Яа-яЁё\s]+$/g, '');
});

document.querySelector('.inputaddotch').addEventListener('keyup', function(){
	this.value = this.value.replace(/[^[А-Яа-яЁё\s]+$/g, '');
});

 $('.addls').on('click',function(e){
		e.preventDefault();
		var data_result;
		var add_fio='';
		add_ls=document.getElementById("inputAddLS").value;
		add_fam=document.getElementById("inputAddFAM").value;
		add_im=document.getElementById("inputAddIM").value;
		add_otch=document.getElementById("inputAddOTCH").value;

		if((add_ls=="") || (document.getElementById("inputAddLS").value.length <9)){
			document.getElementById("inputAddLS").className = "form-control is-invalid";
			document.getElementById("inputAddLS").value = '';
		}else{document.getElementById("inputAddLS").className = "form-control is-valid";}

		if((add_fam=="") || (document.getElementById("inputAddFAM").value.length <2)){
			document.getElementById("inputAddFAM").className = "form-control is-invalid";
			document.getElementById("inputAddFAM").value = '';
		}else{document.getElementById("inputAddFAM").className = "form-control is-valid";}

		if((add_im=="") || (document.getElementById("inputAddIM").value.length <2)){
			document.getElementById("inputAddIM").className = "form-control is-invalid";
			document.getElementById("inputAddIM").value = '';
		}else{document.getElementById("inputAddIM").className = "form-control is-valid";}

		if((add_otch=="") || (document.getElementById("inputAddOTCH").value.length <2)){
			document.getElementById("inputAddOTCH").className = "form-control is-invalid";
			document.getElementById("inputAddOTCH").value = '';
		}else{document.getElementById("inputAddOTCH").className = "form-control is-valid";}
		var $add_fio=add_fam+' '+add_im+' '+add_otch;
		var $email_coupon='';
        if (document.querySelector('.custom-control-input').checked){
                        $email_coupon=1;
                        $.ajax({ url: '/sg-includes/__add_ls.php',
                                data: {'email_coupon' : $email_coupon,'add_ls' : add_ls,'add_fio' : $add_fio},
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
					toastr["success"]("Лицевой счет успешно привязан!", "")
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
								"timeOut": "2500",
								"extendedTimeOut": "1000",
								"showEasing": "swing",
								"hideEasing": "linear",
								"showMethod": "fadeIn",
								"hideMethod": "fadeOut"
					}
					toastr["error"]("Информация не найдена, обратитесь в центр обслуживания населения!", "Ошибка")
					}
                                        else if(data==3){
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
                                        toastr["error"]("Ошибка добавления лицевого счета! ЛС привязан к другому кабинету", "Ошибка")
                                        }
			}
			})
		}else{
			    $email_coupon=0;
 	                    $.ajax({ url: '/sg-includes/__add_ls.php',
                                data: {'email_coupon' : $email_coupon,'add_ls' : add_ls,'add_fio' : $add_fio},
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
					toastr["success"]("Лицевой счет успешно привязан!", "")
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
								"timeOut": "2500",
								"extendedTimeOut": "1000",
								"showEasing": "swing",
								"hideEasing": "linear",
								"showMethod": "fadeIn",
								"hideMethod": "fadeOut"
					}
					toastr["error"]("Информация не найдена, обратитесь в центр обслуживания населения!", "Ошибка")
					}
					else if(data==3){
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
                                        toastr["error"]("Ошибка добавления лицевого счета! ЛС привязан к другому кабинету", "Ошибка")
                                        }

			}
			})
		}
});
}
 </script>
</script>
</body>
</html>
