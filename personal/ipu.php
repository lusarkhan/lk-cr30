<?php
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

include './../sg-config.php';

include './../sg-includes/functions/funct.php';

include './../sg-includes/sg-db.php';

session_start();
date_default_timezone_set('Europe/Samara');
$today = date('d.m.Y');
$is_actual_exucuted=0;
$check_add=0;
$empty_var=1;
$dt_to_sql="";

if(date("m")=="01"){ 
	$dt_to_sql="01." . date("m", strtotime("-1 month", time()) ). "." . date("Y", strtotime("-1 year", time()));
}else {
	$dt_to_sql="01." . date("m", strtotime("-1 month", time()) ). "." . date("Y");
}

header('Content-Type: text/html; charset=UTF8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$login = isset($_SESSION['LOGIN']) ? $_SESSION['LOGIN'] : false;
$errors = array();
$empty_var=0;
$send_count_rows=0;
$_SESSION['active_nav_menu']=7;

 if(time() - $_SESSION['timestamp'] > 600) { 
    unset($_SESSION['LOGIN'], $_SESSION['ID_USER'], $_SESSION['timestamp']);
    header('Location:'. SG_HOST .'/logout');
    exit;
} else {
    $_SESSION['timestamp'] = time();
}    
if (!empty($_SESSION['LS'])){
$_SESSION['url']=$_SERVER['REQUEST_URI'];

	$SQL_DATE = "alter session set nls_date_format = 'dd.mm.yyyy HH24:MI:SS'";
	$RES_SQL_DATE = oci_parse($conn, $SQL_DATE);
	oci_execute($RES_SQL_DATE);
	
	$sql_get_ls_adress = 'select ADDRESS from SARV_PAY.PAY_LS WHERE NUM='.$_SESSION['LS'];
	$res_sql_get_ls_adress = oci_parse($conn, $sql_get_ls_adress);
	oci_define_by_name($res_sql_get_ls_adress, 'ADDRESS', $ls_address);
	oci_execute($res_sql_get_ls_adress);
	oci_fetch($res_sql_get_ls_adress);
	$sql_get_calc = "select ID_COUNTER,MONIKER,NUM,NAME,(select to_CHAR(DT_POV,'dd.mm.yyyy') from dual) as DT_POV,SERIAL_NUM,(select to_CHAR(DT_BEG, 'Month yyyy','nls_date_language=russian') from dual) as DTBEG,(select to_CHAR(DT_BEG, 'dd.MM.yyyy','nls_date_language=russian') from dual) as DTBEG2,SHORT_VALUE  from pay_counters where lsnum = ". $_SESSION['LS'] ." ORDER BY NUM ASC";
	$sql_get_ipu = "select NUM, SERIAL_NUM from pay_counters where lsnum = ". $_SESSION['LS'] ." ORDER BY NUM ASC";
	$sql_get_t_pok = "select NUM,DF,to_char(DK) as DK,POK,DT_ADD from SARV_PAY.T_POK where num = ". $_SESSION['LS'];
	$res_sql_get_calc = oci_parse($conn, $sql_get_calc);
	oci_execute($res_sql_get_calc);
	$res_sql_get_ipu = oci_parse($conn, $sql_get_ipu);
	oci_define_by_name($res_sql_get_ipu, 'NUM', $ipu_num);
	oci_define_by_name($res_sql_get_ipu, 'SERIAL_NUM', $ipu_serial_num);
	oci_execute($res_sql_get_ipu);
	
	$res_sql_get_t_pok = oci_parse($conn, $sql_get_t_pok);
	oci_execute($res_sql_get_t_pok);
}else{
        header('Location:'. SG_HOST .'/personal/add');
        exit;
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
.styled-table {
    border-collapse: collapse;
    font-size: 0.9em;
    font-family: sans-serif;
    min-width: 400px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
}
.styled-table thead tr {
    background-color: #484848; /*#009879*/
    color: #f5f5f5; /*#ffffff*/
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
            <h1>Передача показаний индивидуальных приборов учета<h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Передать показания ИПУ</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
 <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div>
          <div class="col-12">
            <!-- general form elements -->
            <div class="invoice p-3 mb-3">
              <div class="card-header">
                <h3 class="card-title">Приборы учета и учтенные показания</h3>
              </div>
              <!-- /.card-header         table table-striped table-bordered lux-->
				<div class="col-12 table-responsive">
				<table id="datatable-buttons" class="styled-table" style="width:100%">
				<thead>
				<tr>
					<th rowspan="2" style="text-align: center;">Порядковый номер счетчика</th>
                                        <th rowspan="2" style="text-align: center;">Место установки</th>
					<th rowspan="2" style="text-align: center;">Услуга</th>
					<th rowspan="2" style="text-align: center;">Дата следующей поверки</th>
					<th rowspan="2" style="text-align: center;">Серийный номер счетчика</th>
					<th rowspan="2" style="text-align: center;">Месяц показаний, учтенных в последнем ЕПД</th>
					<th rowspan="2" style="text-align: center;">Дата показаний, учтенных в последнем ЕПД</th>
					<th rowspan="2" style="text-align: center;">Показания, учтенные в последнем ЕПД</th>
					<th rowspan="2" style="width:18%; text-align: center;">Текущие показания <br>на <span style="color: #ffc215;"><?php echo date('d.m.Y');?></span></th>
				</tr>
				</thead>
				<tbody>
					<?php
					{
						while (oci_fetch($res_sql_get_calc)){
						
							echo '<tr id="row'. oci_result($res_sql_get_calc, 'NUM') .'">';

                           	     		        echo '<td class="cntnumber"><center><a href="?cntid='. oci_result($res_sql_get_calc, 'NUM') . '">'. oci_result($res_sql_get_calc, 'NUM') . '</a></center></td>';
                            				echo "<td><center>". oci_result($res_sql_get_calc, 'NAME') . "</center></td>";
				                        echo "<td><center>". oci_result($res_sql_get_calc, 'MONIKER') . "</center></td>";
                            				echo '<td id="dt-pov'. oci_result($res_sql_get_calc, 'NUM') .'"><center>'. oci_result($res_sql_get_calc, 'DT_POV') . '</center></td>';
			                                echo '<td class="cnt-num"><center>'. oci_result($res_sql_get_calc, 'SERIAL_NUM') . "</center></td>";
				                        echo "<td><center>". oci_result($res_sql_get_calc, 'DTBEG') . "</center></td>";
			                                echo "<td><center>". oci_result($res_sql_get_calc, 'DTBEG2') . "</center></td>";
				                        echo '<td class="shortvalue'. oci_result($res_sql_get_calc, 'NUM') .'"><center>'.oci_result($res_sql_get_calc, 'SHORT_VALUE') . "</center></td>";
							echo '<td><form method="POST" id="formsendpok'. oci_result($res_sql_get_calc, 'NUM').'">
							<center>
							<div class="input-group input-group-sm" id="message">
								<input type="number" style="width:100%" class="form-control pokazz" name="Pokaz" aria-label="" id="Pokaz'. oci_result($res_sql_get_calc, 'NUM') .'" placeholder="Показания">
								<span class="input-group-append">
									<button type="submit" id="sendpok'. oci_result($res_sql_get_calc, 'NUM').'" name="AddPok" value="'. oci_result($res_sql_get_calc, 'NUM') .'" class="btn btn-info btn-flat sendpok">Передать</button>
								 </span>
							</div>
						 	</center>
						        </form>
							</td>';
						}
					}
					?>
					</tr>
				</tbody>
				</table>
			</div>
            </div>
			<?php if(count($errors) > 0)
			{
				foreach ($errors as $error)
				{ 
					echo '<div class="alert alert-danger alert-dismissible fade show">';
					echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
					echo '<strong>Ошибка! </strong>'  . array_shift($errors). '</div>';
				}
				exit;
			} ?>
            <!-- /.card -->
          </div>
          <!--/.col (right) -->
		  
		  
		  		  <div class="col-12"
            <!-- general form elements -->
            <div class="invoice p-3 mb-3">
              <div class="card-header">
                <h3 class="card-title">История показаний</h3>
              </div>
              <!-- /.card-header         table table-striped table-bordered lux-->
		<div class="card-body">
		<div id="accordion">
        <div class="card">
		  <?php 
				while (oci_fetch($res_sql_get_ipu)){
					echo '<div class="card-header">
                      <h4 class="card-title">
                       <a id="col1" data-toggle="collapse" data-parent="#accordion" value="'.$ipu_num.'" href="#collapse'.$ipu_num.'">';
						echo '<i class="fas fa-chevron-circle-down"></i>';
							echo ' Счетчик № '.$ipu_serial_num;
                        echo '</a>
                      </h4>
                    </div>
                    <div id="collapse'.$ipu_num.'" class="panel-collapse collapse">
                      <div class="card-body">
                        <div class="row">
							<div class="col-12 table-responsive">';
							echo '<div class="card">
									<div class="card-body table-responsive p-0">
										<table class="table table-striped table-valign-middle">
										  <thead>
										  <tr>
											<th>Статус</th>
											<th><center>Показание</center></th>
											<th>Дата передачи</th>
											<th>Источник</th>
										  </tr>
										  </thead>
										  <tbody>';
													$sql_get_ipu_val = "SELECT POK,DT_ADD FROM T_POK WHERE NUM=".$_SESSION['LS']." AND DK='".$ipu_num."' AND DT_ADD between add_months(trunc(sysdate,'mm'),-6) and add_months(trunc(sysdate,'mm'),+1) UNION ALL
																SELECT POK,DT_ADD FROM T_POK_ALL WHERE NUM=".$_SESSION['LS']." AND DK='".$ipu_num."' AND DT_ADD between add_months(trunc(sysdate,'mm'),-6) and add_months(trunc(sysdate,'mm'),+1) ORDER BY DT_ADD DESC";
													$res_sql_get_ipu_val = oci_parse($conn, $sql_get_ipu_val);
													oci_define_by_name($res_sql_get_ipu_val, 'POK', $ipu_pok);
													oci_define_by_name($res_sql_get_ipu_val, 'DT_ADD', $ipu_dt_add);
													oci_execute($res_sql_get_ipu_val);
													while($row = oci_fetch_array($res_sql_get_ipu_val, OCI_BOTH)){
														echo '<tr>
																<td>
																  <span class="badge badge-success">Приняты</span>
																</td>
																<td><center>'.$ipu_pok.'</center></td>
																<td>
																  <div class="text-success mr-1"><div><i class="fas fa-calendar-alt"></i> '.
																	$ipu_dt_add
																  .'</div></div>
																</td>
																<td>
																  Сервис "Личный кабинет"
																</td>
															</tr>';
													}
											echo '</tbody>
										</table>
									  </div>
									</div>';
							echo '</div>
							<!-- /.col -->
						</div>
                      </div>
                    </div>';
			    }
			   
			   ?>
                  </div>
                </div>
              </div>
			<?php if(count($errors) > 0)
			{
				foreach ($errors as $error)
				{ 
					echo '<div class="alert alert-danger alert-dismissible fade show">';
					echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
					echo '<strong>Ошибка! </strong>'  . array_shift($errors). '</div>';
				}
				exit;
			} ?>
			  </div>
			  
			  
			  
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
	 <!-- Main content -->
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
var old_pok,new_pok,pok,input_id,cnt_val,$pokazz,errors,shortvalue;
function init(){
	//---Выделение счетчика со скорой поверкой <30 дней
    moment.defaultFormat = "DD.MM.YYYY";
	var $today = "<?= $today ?>";
	var newtoday = $today.split(".").reverse().join("-");

	var row_id,dtpov,dt_pov_error;
	let cnt_num = document.querySelectorAll('.cntnumber');

	for (let cnt_nums of cnt_num) {
			row_id=cnt_nums.innerText;

			var dtpoverki = document.getElementById("dt-pov"+row_id);
			dtpov=dtpoverki.innerText.split(".").reverse().join("-");
			var a = moment(dtpov);
			var b = moment(newtoday);
			var result = a.diff(b, 'days');   
	    /*if (send_count>=3)
			document.getElementById("Pokaz"+row_id).disabled=true;
		else
			document.getElementById("Pokaz"+row_id).disabled=false;*/

		if(result<=31){	
			document.getElementById("row"+row_id).style.color = "#2a3035";
			document.getElementById("row"+row_id).style.backgroundColor ="rgb(216 27 96 / 47%)";

			var a = [];
			a[row_id] = row_id;
			for (var x in a) {
				toastr.options = {
					"closeButton": true,
					"debug": true,
					"newestOnTop": true,
					"progressBar": false,
					"positionClass": "toast-top-right",
					"preventDuplicates": false,
					"onclick": null,
					"showDuration": "5000",
					"hideDuration": "1000",
					"timeOut": "15000",
					"extendedTimeOut": "1000",
					"showEasing": "swing",
					"hideEasing": "linear",
					"showMethod": "fadeIn",
					"hideMethod": "fadeOut"
				}
				toastr["error"]("<strong><b>Приближается или наступил следующий срок обязательной поверки счетчика с порядковым номером </b></strong><big><b>"+row_id+"</b></big>", "<h5>Внимание!!!</h5>");
			}
		}//endif
    } 
}
 $('.sendpok').on('click',function(e){
		e.preventDefault();
		var data_result;
		const href = $(this).attr('href');
		var $row = $(this).closest("tr");
		var $cnt_serial_num = $row.find(".cnt-num").text();
		var $cnt_num= $row.find(".cntnumber").text();
                shortvalue = parseFloat($row.find(".shortvalue"+$cnt_num).text());
		pok=parseFloat(document.getElementById("Pokaz"+$cnt_num).value);
		var result=pok-shortvalue;

		if((pok==null) || (shortvalue==null) || (isNaN(pok)==true) || (isNaN(shortvalue)==true)){
			document.getElementById("Pokaz"+$cnt_num).className = "form-control is-invalid";
			mode=1;
		}
		else if((pok>0) && (result>=50)){
			document.getElementById("Pokaz"+$cnt_num).className = "form-control is-warning";
			mode=3;
		}
		else if((pok>shortvalue) && (result<50)) //Ошибок нет
		{
			document.getElementById("Pokaz"+$cnt_num).className = "form-control is-valid";
			mode=0;
		}
		else if(pok==shortvalue){
			document.getElementById("Pokaz"+$cnt_num).className = "form-control is-warning";
			mode=0;//2
        }
		else if(pok<shortvalue){
			document.getElementById("Pokaz"+$cnt_num).className = "form-control is-invalid";
			mode=4;
        }

		if(mode==0)
		{     
                      
			$.ajax({ url: '/sg-includes/__sendpok.php',
				data: {'pok' : pok,'cntnum' : $cnt_num, 'cntserialnumber' : $cnt_serial_num},
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
						toastr["success"]("Показания успешно отправлены!", "")
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
					toastr["error"]("Не удалось передать показания!", "Ошибка")
					}
					else if(data==2){
							document.getElementById("Pokaz"+$cnt_num).disabled=true;
							let tomorrow = moment().add(1, 'days').format('DD.MM.YYYY').toString();
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
								"timeOut": "3000",
								"extendedTimeOut": "1000",
								"showEasing": "swing",
								"hideEasing": "linear",
								"showMethod": "fadeIn",
								"hideMethod": "fadeOut"
							}
							toastr["error"]("Вы превысели количество попыток отправки показаний! Функция отправки показаний заблокирована до "+tomorrow, "Внимание")
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
					toastr["error"]("Ошибка 404. Повторите отправку показаний позднее", "Внимание")
				}
			})
		}
		else if((mode==1) || (pok==null) || (isNaN(pok)==true)){     
			document.getElementById("Pokaz"+$cnt_num).className = "form-control is-invalid";
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
			toastr["error"]("Введите передаваемые показания!", "Внимание");
		}
		else if((mode==2) || (mode==4)){
			mode=1;
		}
		else if(mode==3)
		{     
			document.getElementById("Pokaz"+$cnt_num).className = "form-control is-warning";  
			Swal.fire({
				type: 'warning',
				title: 'Предупреждение',
				text: 'Проверьте корректность передаваемых показания! Существенная разность показаний. Если Вы уверенны в точности передаваемых показаний нажмите продолжить',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Продолжить',
				cancelButtonText: 'Отмена',
			}).then((result) => {
					if (result.value){
						$.ajax({ url: '/sg-includes/__sendpok.php',
							data: {'pok' : pok,'cntnum' : $cnt_num, 'cntserialnumber' : $cnt_serial_num},
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
									toastr["success"]("Показания успешно отправлены!", "")
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
									toastr["error"]("Не удалось передать показания!", "error")
								}
								else if(data==2){
									document.getElementById("Pokaz"+$cnt_num).disabled=true;
									let tomorrow =  moment().add(1,'days');
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
										"timeOut": "3000",
										"extendedTimeOut": "1000",
										"showEasing": "swing",
										"hideEasing": "linear",
										"showMethod": "fadeIn",
										"hideMethod": "fadeOut"
									}
								toastr["error"]("Вы превысели количество попыток отправки показаний! Отправка показаний заблокирована до "+tomorrow, "Внимание")
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
		}
});
	
$('.form-control.pokazz').blur(function(){
	var cells,result;
	var $rowss = $(this).closest("tr");
	var input_id= $rowss.find(".cntnumber").text();
    var old_pok = parseFloat($rowss.find(".shortvalue"+input_id).text()); 
    var new_pok=parseFloat(document.getElementById("Pokaz"+input_id).value);
	result=new_pok-old_pok;	
		if((new_pok==null) || (old_pok==null) || (isNaN(new_pok)==true)){
			document.getElementById("Pokaz"+input_id).className = "form-control is-invalid";
			mode=1;
		}
		else if((new_pok>0) && (result>=50)){
			document.getElementById("Pokaz"+input_id).className = "form-control is-warning";
			mode=3;
		}
		else if((new_pok>old_pok) && (result<50)) 
		{
			document.getElementById("Pokaz"+input_id).className = "form-control is-valid";
			mode=0;
		}
		else if(new_pok==old_pok){
			document.getElementById("Pokaz"+input_id).className = "form-control is-warning";
			Swal.fire({
				toast: true,
				icon: 'warning',
				position: 'top-end',
				showConfirmButton: false,
				showCancelButton: false,
				timer: 5000,
				type: 'warning',
				title: 'Предупреждение',
				text: 'Передаваемые показания совпадают с ранее учтенным!'
			})
			mode=0;
        }
		else if(new_pok<old_pok){
			document.getElementById("Pokaz"+input_id).className = "form-control is-invalid";
			Swal.fire({
				toast: true,
				icon: 'error',
				position: 'top-end',
				showConfirmButton: false,
				showCancelButton: false,
				timer: 5000,
				type: 'warning',
				title: 'Ошибка',
				text: 'Передаваемые показания не могут быть меньше ренее учтенных!'
			})
			mode=4;
        }
});

 </script>
</body>
</html>

