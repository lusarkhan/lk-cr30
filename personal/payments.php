<?php
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

include './../sg-config.php';

include './../sg-includes/functions/funct.php';

session_start();
date_default_timezone_set('Europe/Samara');
$today = date('d.m.Y H:i:s');

header('Content-Type: text/html; charset=UTF8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ob_start();
$login = isset($_SESSION['LOGIN']) ? $_SESSION['LOGIN'] : false;
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
	if (!empty($_SESSION['LS'])){
		$_SESSION['url']=$_SERVER['REQUEST_URI'];
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
	$SQL_DATE = "alter session set nls_date_format = 'dd.mm.yyyy'";
	$RES_SQL_DATE = oci_parse($conn, $SQL_DATE);
	oci_execute($RES_SQL_DATE);
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
   .lux { 
    width: 300px;
    border: 1px solid #fafafa;
    border-collapse: collapse;
    border-spacing: 0;
   }
   .lux th {
    background: #a2a2a9;
    color: #000000; 
   }
   .lux td { 
    border-bottom: 1px solid black;
   }
   .lux td, .lux th {
    padding: 4px;
   }
.styled-table {
    border-collapse: collapse;
    font-size: 0.9em;
    font-family: sans-serif;
    min-width: 400px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
	margin-bottom: 10px;
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
<body class="hold-transition sidebar-mini layout-fixed">
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
            <h1>Платежи<h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Платежи</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <!-- Main content -->
            <div class="invoice p-3 mb-3">
              <!-- title row -->
              <!-----<div class="row">-------->
                <div class="col-12">
                  <h4>
                    <i class="fas fa-receipt"></i> Платежи по лицевому счету №<?php echo $_SESSION['LS'];?>
		    <div class="row invoice-info">
			<div class="col-sm-10 invoice-col" style="font-family: Times, Times New Roman, Georgia, serif; font-size: 14pt;">
			</div>
           	    </div>
                  </h4>
                </div>
		<div class="card-body">
		<div id="accordion">
                  <div class="card">
		  <?php
         	        $_monthsList = array(
                        ".01." => "Январь",
                        ".02." => "Февраль",
                        ".03." => "Март",
                        ".04." => "Апрель",
                        ".05." => "Май",
                        ".06." => "Июнь",
                        ".07." => "Июль",
                        ".08." => "Август",
                        ".09." => "Сентябрь",
                        ".10." => "Октябрь",
                        ".11." => "Ноябрь",
                        ".12." => "Декабрь"
                );
                $_monthsList = array(
                        ".01."=>"Январь",".02."=>"Февраль",".03."=>"Март",
                        ".04."=>"Апрель",".05."=>"Май", ".06."=>"Июнь",
                        ".07."=>"Июль",".08."=>"Август",".09."=>"Сентябрь",
                        ".10."=>"Октябрь",".11."=>"Ноябрь",".12."=>"Декабрь");

               $query = "select * from (select add_months(trunc(sysdate,'MM'), -rownum+1) d from pay_ls where rownum < 13) where d>=to_date('01.01.2021','dd.mm.yyyy')";
               $state=oci_parse($conn, $query);
               oci_execute($state, OCI_COMMIT_ON_SUCCESS);
               while($row = oci_fetch_array($state, OCI_BOTH)){
                    $month = $_monthsList[date(".m.", strtotime($row[0]))];
                    $mm = date("m", strtotime($row[0]));
                    $date_coupon = $row[0];
    		    echo '<div class="card-header">
                      <h4 class="card-title">
                       <a id="col1" data-toggle="collapse" data-parent="#accordion" value="'.date("m", strtotime($row[0])).'" href="#collapse'.date("m", strtotime($row[0])).'">';
			echo '<i class="fas fa-chevron-circle-down"></i>';
                        echo ' '.$month. ' ' . date("Y", strtotime($row[0]));
                        echo '</a>
                      </h4>
                    </div>
                    <div id="collapse'.date("m", strtotime($row[0])).'" class="panel-collapse collapse">
                      <div class="card-body">
                        <div class="row">
							<div class="col-12 table-responsive">
							<!--<span id="jan"></span>-->
								<table id="datatable-buttons" class="styled-table" style="width:100%">
									<thead>
										<tr>
											<th rowspan="2" style="text-align: center;">Дата платежа</th>
											<th rowspan="2" style="text-align: center;">Сборщик</th>
											<th rowspan="2" style="text-align: center;">Код услуги</th>
											<th rowspan="2" style="text-align: center;">Услуга</th>
											<th rowspan="2" style="text-align: center;">Начало периода</th>
											<th rowspan="2" style="text-align: center;">Конец периода</th>
											<th rowspan="2" style="text-align: center;">Сумма, руб.</th>
										</tr>
									</thead>
									<tbody>
										<tr>';    
										    $sql_get_payments = "select dtz_pay AS DTZPAY, (select name from Max_payments_collector where kp_num=m.kp_num) AS SBOR,
											                        coupon_kind AS CKND,
																	(select name from Max_payments_service where coupon_kind=m.coupon_kind and rownum=1) AS USLUGA,
																	coupon_start AS DTBEG, coupon_end AS DETEND,
																	summa AS SUMMA from Max_payments_all m 
																	where lsnum_dtpay='".$_SESSION['LS']."'||'_'||to_char(trunc(to_date('01.".$mm.".".date("Y", strtotime($row[0]))."','dd.mm.yyyy'),'MM'),'mm.yyyy') 
																	and id_parent is null order by 1,2,3,4,5";
											$res_get_payments = oci_parse($conn, $sql_get_payments);
											oci_execute($res_get_payments);
											while (oci_fetch($res_get_payments)){
												echo "<tr>"; 
												echo "<td style='text-align: center;'>". oci_result($res_get_payments, 'DTZPAY') . "</td>";
												echo "<td style='text-align: center;'>". oci_result($res_get_payments, 'SBOR') . "</td>";
												echo "<td style='text-align: center;'>". oci_result($res_get_payments, 'CKND') . "</td>";
												echo "<td style='text-align: center;'>". oci_result($res_get_payments, 'USLUGA') . "</td>";
												echo "<td style='text-align: center;'>". oci_result($res_get_payments, 'DTBEG') . "</td>";
												echo "<td style='text-align: center;'>". oci_result($res_get_payments, 'DETEND') . "</td>";
												echo "<td style='text-align: center;'>". oci_result($res_get_payments, 'SUMMA') . "</td>";
											}   
										
										echo '</tr>
									</tbody>
								</table>
							</div>
							<!-- /.col -->
						</div>
                      </div>
                    </div>';
			   }
			   ?>
                  </div>
                </div>
              </div>
              <!-------</div>-------------->
            </div>
            <!-- /.invoice -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>	
    <!-- /.content -->

  </div>
  <!-- /.content-wrapper -->
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<?php require "../scripts.php" ?>
</body>
</html>

