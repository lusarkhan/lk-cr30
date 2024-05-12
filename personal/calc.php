<?php

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
header("X-XSS-Protection: 1; mode=block");

include '../sg-config.php';


session_start();
date_default_timezone_set('Europe/Samara');
$today = date('d.m.Y H:i:s');
$is_actual_exucuted=0;
$check_add=0;

header('Content-Type: text/html; charset=UTF8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$login = isset($_SESSION['LOGIN']) ? $_SESSION['LOGIN'] : false;
$errors = array();
$fn_result='';
$selectedIndex;
$token='';

$selectedIndex;
$_SESSION['active_nav_menu']=3;
if (!empty($_SESSION['LS'])){
$hash_ls = md5($_SESSION['LS']);
$hash_ls_time = md5($_SESSION['LS']+time(1));
$destination_path = '/var/www/html/lk.host.ru/personal/downloads/'.$hash_ls;
$date_coupon = '01.'.date('m').'.'.date('Y');
$_SESSION['selected_month']= '01.'.date('m').'.'.date('Y');
$selected_ls=$_SESSION['LS'];
$_monthsList = array(
                        ".01."=>"Январь",".02."=>"Февраль",".03."=>"Март",
                        ".04."=>"Апрель",".05."=>"Май", ".06."=>"Июнь",
                        ".07."=>"Июль",".08."=>"Август",".09."=>"Сентябрь",
                        ".10."=>"Октябрь",".11."=>"Ноябрь",".12."=>"Декабрь");

$month = $_monthsList[date(".m.", strtotime($today))];
$u_name=SG_BR_USER;
$u_pass=SG_BR_PSWRD;

 if(time() -$_SESSION['timestamp'] > 600) { 
    unset($_SESSION['LOGIN'], $_SESSION['ID_USER'], $_SESSION['timestamp']);
    header('Location:'. SG_HOST .'/logout');
    exit;
} else {
    $_SESSION['timestamp'] = time();
}

    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'downloads/'.$hash_ls.DIRECTORY_SEPARATOR;
    $PNG_WEB_DIR = '../personal/downloads/'.$hash_ls.'/';
	
    include '../sg-includes/phpqrcode/qrlib.php';

    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);
    if (isset($_SESSION['LOGIN'])){
		$_SESSION['url']=$_SERVER['REQUEST_URI'];	   

    }
    else {
        $login = '';
        if (isset($_COOKIE['CookieMy'])){
                $login = htmlspecialchars($_COOKIE['CookieMy']);
        }
		header('Location:'. SG_HOST .'/login');
    }


    $filename = $PNG_TEMP_DIR.$_SESSION['LS'].'-'.md5(time(1)).'.png';

    $errorCorrectionLevel = 'L';
    if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];    

    $matrixPointSize = 2;
    if (isset($_REQUEST['size']))
        $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);

    if (isset($_REQUEST['data'])) { 
        if (trim($_REQUEST['data']) == '')
            die('data cannot be empty! <a href="?">back</a>');

        $filename = $PNG_TEMP_DIR.'test'.md5($_REQUEST['data'].'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
        QRcode::png($_REQUEST['data'], $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
        
    } else {    
    }


$option = isset($_POST['month']) ? $_POST['month'] : false;
    	if ($option) {
		
			$date_coupon=htmlentities($_POST['month'], ENT_QUOTES, "UTF-8");
			
			$_SESSION['current_month']= $_monthsList[date(".m.", strtotime($date_coupon))];
		
			$_SESSION['selected_month']=  $_SESSION['current_month'] . " " .date("Y", strtotime($date_coupon));

			$date_coupon_bris=date("Ym", strtotime($date_coupon));
			

			$startdate = strtotime($date_coupon);

			$enddate = strtotime('01.02.2022');

			if ($startdate >= $enddate) {

			
			$url_pkg_customer_search_account_ifc = "https://prod.host.ru/sg/api/data/?op=pkg_customer.search_account_ifc";

			$url_authenticate = "https://prod.host.ru/sg/api/account/authenticate";
			$curl_authenticate = curl_init($url_authenticate);
			curl_setopt($curl_authenticate, CURLOPT_URL, $url_authenticate);
			curl_setopt($curl_authenticate, CURLOPT_POST, true);
			curl_setopt($curl_authenticate, CURLOPT_RETURNTRANSFER, true);

			$headers_authenticate = array(
				"Content-Type: application/json",
			);
			curl_setopt($curl_authenticate, CURLOPT_HTTPHEADER, $headers_authenticate);

			$data_authenticate = '{"username": "'.$u_name.'", "password": "'.$u_pass.'"}';

			curl_setopt($curl_authenticate, CURLOPT_POSTFIELDS, $data_authenticate);

			curl_setopt($curl_authenticate, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl_authenticate, CURLOPT_SSL_VERIFYPEER, false);

			$resp_authenticate = curl_exec($curl_authenticate);
			curl_close($curl_authenticate);

			$token_json = $resp_authenticate;
 
			$decoded_token_json = json_decode($token_json, true);
			if(empty($decoded_token_json)){
				//return (false === empty($decoded_token_json));
			}else{
				if(empty($decoded_token_json['session'])){
					//return (false === empty($decoded_token_json['session']));
				}else{
					$token = $decoded_token_json['session'];
				}
			}

			$url_get_invoices_ifc = "https://prod.host.ru/sg/api/data/?op=pkg_account.get_invoices_ifc";
			$sql_get_id_ls_bris = "select ID_LS_BRIS from Max_LS_Id_LS_Bris where ls_num=".$selected_ls;

			$res_get_id_ls_bris = oci_parse($conn, $sql_get_id_ls_bris);
			oci_define_by_name($res_get_id_ls_bris, 'ID_LS_BRIS', $AccountId);
			oci_execute($res_get_id_ls_bris);
			oci_fetch($res_get_id_ls_bris);

			$curl_get_invoices_ifc = curl_init($url_get_invoices_ifc);
			curl_setopt($curl_get_invoices_ifc, CURLOPT_URL, $url_get_invoices_ifc);
			curl_setopt($curl_get_invoices_ifc, CURLOPT_POST, true);
			curl_setopt($curl_get_invoices_ifc, CURLOPT_RETURNTRANSFER, true);

			$headers_get_invoices_ifc = array(
				"Authorization: Token ".$token,
				"Content-Type: application/json",
			);
			curl_setopt($curl_get_invoices_ifc, CURLOPT_HTTPHEADER, $headers_get_invoices_ifc);

$data_get_invoices_ifc = <<<DATA
	{"AccountId": "$AccountId",
	"PageIndex": 1,
	"PageSize": 10,
	"PeriodId": "$date_coupon_bris",
	"_Mode_": "0"}
DATA;

			curl_setopt($curl_get_invoices_ifc, CURLOPT_POSTFIELDS, $data_get_invoices_ifc);


			curl_setopt($curl_get_invoices_ifc, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl_get_invoices_ifc, CURLOPT_SSL_VERIFYPEER, false);

			$resp_get_invoices_ifc = curl_exec($curl_get_invoices_ifc);
			curl_close($curl_get_invoices_ifc);


			$decoded_json = json_decode($resp_get_invoices_ifc,true);

			$json_list_array[] = $decoded_json['List'];

 			if(empty($json_list_array)){
                  $fn_result = 0;
				$filePath_id='';
                $coupon_filename ='https://lk.host.ru/404';
            }else{
                if(empty($json_list_array[0][0]['FilePath'])){
					//return (false === empty($json_list_array[0][0]['FilePath']));
					$fn_result = 0;
					$filePath_id='';
					$coupon_filename ='https://lk.host.ru/404';
                }else{
                    $filePath_id=$json_list_array[0][0]['FilePath'];
                    $url_file_get="https://epd.host.ru/api4/fs/invoice/download?fileId=".$filePath_id."&tenant=lk.host.ru";
                    $coupon_filename=$url_file_get;
                    $fn_result=1;
                }
            }
			
			$url = "https://prod.host.ru/sg/api/data/?op=k_ifc.do_logout_ifc";

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			$headers = array(
				"Authorization: Token ".$token,
				"Content-Type: application/json",
			);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

			$data = <<<DATA
			{
			"Id": 12345
			}
DATA;

			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

			$resp = curl_exec($curl);
			curl_close($curl);

		
			//#############№##################################################################
			//############# ЕСЛИ ЗАПРОШЕНА КВИТАНЦИЯ ДО ВВЕДЕНИЯ БРИС (до 01.02.2022)  #######
			//#############№##################################################################
			}else{
				$coupon_filename1 = '/mnt/coupons/'.$_SESSION['LS'].'  '.$date_coupon.'.pdf';

				if (file_exists($coupon_filename1)) {
					if (!file_exists($destination_path)) {
						mkdir($destination_path, 0755, true);
					}
					$fn_result=1;
					$hashed_filename=md5($_SESSION['LS'].'_'.$date_coupon);
					
					$filename_pdf='/var/www/html/lk.host.ru/personal/downloads/'.$hash_ls.'/'.$hashed_filename.'.pdf';
					
					if(!file_exists($filename_pdf)){
                
						copy('/mnt/coupons/'.$_SESSION['LS'].'  '.$date_coupon.'.pdf', $destination_path.'/'.$hashed_filename.'.pdf'); 
						$coupon_filename ='https://lk.host.ru/personal/downloads/'.$hash_ls.'/'.$hashed_filename.'.pdf'; 
						chmod($destination_path.'/'.$hashed_filename.'.pdf', 0644);
					}else{
						$coupon_filename ='https://lk.host.ru/personal/downloads/'.$hash_ls.'/'.$hashed_filename.'.pdf';  
						chmod($destination_path.'/'.$hashed_filename.'.pdf', 0644);
					}
				}else {
					$fn_result = 0;
					$coupon_filename ='https://lk.host.ru/404';
				}
			}

		//################################################################################
		//####################################################################################
		//######## ЕСЛИ МЕСЯЦ НЕ ВЫБРАН ######################################################
		} else {
			$date_coupon='01.'.date('m').'.'.date('Y');  //date('d.m.Y');
		
			$_SESSION['current_month']=$_monthsList[date(".m.", strtotime($date_coupon))];
		
            		$_SESSION['selected_month']= $_SESSION['current_month'] . " " .date("Y", strtotime($date_coupon));
				
			$date_coupon_bris=date("Ym", strtotime($date_coupon));

	
			$startdate = strtotime($date_coupon);

			$enddate = strtotime('01.02.2022');
			//ЕСЛИ ТЕКУЩАЯ ДАТА БОЛЬШЕ или РАВНА '01.02.2022' РАБОТАЕМ С БРИС ЖКХ
			if ($startdate >= $enddate) {
				
			//#########################################################################
			//#########################################################################
			$url_authenticate = "https://prod.host.ru/sg/api/account/authenticate";
			$url_get_invoices_ifc = "https://prod.host.ru/sg/api/data/?op=pkg_account.get_invoices_ifc";
			$url_pkg_customer_search_account_ifc = "https://prod.host.ru/sg/api/data/?op=pkg_customer.search_account_ifc";
			
			//################################################################################
			//################## Авторизация #################################################
			//################################################################################

			$curl_authenticate = curl_init($url_authenticate);
			curl_setopt($curl_authenticate, CURLOPT_URL, $url_authenticate);
			curl_setopt($curl_authenticate, CURLOPT_POST, true);
			curl_setopt($curl_authenticate, CURLOPT_RETURNTRANSFER, true);

			$headers_authenticate = array(
				"Content-Type: application/json",
			);
			curl_setopt($curl_authenticate, CURLOPT_HTTPHEADER, $headers_authenticate);

			$data_authenticate = '{"username": "'.$u_name.'", "password": "'.$u_pass.'"}';

			curl_setopt($curl_authenticate, CURLOPT_POSTFIELDS, $data_authenticate);

			curl_setopt($curl_authenticate, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl_authenticate, CURLOPT_SSL_VERIFYPEER, false);

			$resp_authenticate = curl_exec($curl_authenticate);
			curl_close($curl_authenticate);

			$token_json = $resp_authenticate;
 
			$decoded_token_json = json_decode($token_json, true);
			if(empty($decoded_token_json)){
				//return (false === empty($decoded_token_json));
			}else{
				if(empty($decoded_token_json['session'])){
					//return (false === empty($decoded_token_json['session']));
				}else{
					$token = $decoded_token_json['session'];
				}
			}

			//################################################################################
			//############# ЗАПРОС ДАННЫХ ЛС #################################################
			//################################################################################

			$sql_get_id_ls_bris = "select ID_LS_BRIS from Max_LS_Id_LS_Bris where ls_num=".$selected_ls;

			$res_get_id_ls_bris = oci_parse($conn, $sql_get_id_ls_bris);
			oci_define_by_name($res_get_id_ls_bris, 'ID_LS_BRIS', $AccountId);
			oci_execute($res_get_id_ls_bris);
			oci_fetch($res_get_id_ls_bris);


			//################################################################################
			//############# ЗАПРОС FilePath ##################################################
			//################################################################################

			$curl_get_invoices_ifc = curl_init($url_get_invoices_ifc);
			curl_setopt($curl_get_invoices_ifc, CURLOPT_URL, $url_get_invoices_ifc);
			curl_setopt($curl_get_invoices_ifc, CURLOPT_POST, true);
			curl_setopt($curl_get_invoices_ifc, CURLOPT_RETURNTRANSFER, true);

			$headers_get_invoices_ifc = array(
				"Authorization: Token ".$token,
				"Content-Type: application/json",
			);
			curl_setopt($curl_get_invoices_ifc, CURLOPT_HTTPHEADER, $headers_get_invoices_ifc);

$data_get_invoices_ifc = <<<DATA
	{"AccountId": "$AccountId",
	"PageIndex": 1,
	"PageSize": 10,
	"PeriodId": "$date_coupon_bris",
	"_Mode_": "0"}
DATA;

			curl_setopt($curl_get_invoices_ifc, CURLOPT_POSTFIELDS, $data_get_invoices_ifc);


			curl_setopt($curl_get_invoices_ifc, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl_get_invoices_ifc, CURLOPT_SSL_VERIFYPEER, false);

			$resp_get_invoices_ifc = curl_exec($curl_get_invoices_ifc);
			curl_close($curl_get_invoices_ifc);


			$decoded_json = json_decode($resp_get_invoices_ifc,true);

			$json_list_array[] = $decoded_json['List'];
			
			if(empty($json_list_array)){
                $fn_result = 0;
				$filePath_id='';
                $coupon_filename ='https://lk.host.ru/404';
            }else{
                if(empty($json_list_array[0][0]['FilePath'])){
					$fn_result = 0;
					$filePath_id='';
					$coupon_filename ='https://lk.host.ru/404';
                }else{
                    $filePath_id=$json_list_array[0][0]['FilePath'];
                    $url_file_get="https://epd.host.ru/api4/fs/invoice/download?fileId=".$filePath_id."&tenant=lk.host.ru";
                    $coupon_filename=$url_file_get;
                    $fn_result=1;
                }
            }
			
			//################################################################################
			//############# ЗАВЕРШЕНИЕ СЕССИИ ################################################
			//################################################################################
			
			$url = "https://prod.host.ru/sg/api/data/?op=k_ifc.do_logout_ifc";

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			$headers = array(
				"Authorization: Token ".$token,
				"Content-Type: application/json",
			);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

			$data = <<<DATA
			{
			"Id": 12345
			}
DATA;

			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

			$resp = curl_exec($curl);
			curl_close($curl);

			//################################################################################
			//############# ЗАПРОС КВИТАНЦИИ #################################################
			//################################################################################
			}else{
				$coupon_filename1 = '/mnt/coupons/'.$_SESSION['LS'].'  '.$date_coupon.'.pdf';

				if (file_exists($coupon_filename1)) {
					if (!file_exists($destination_path)) {
						mkdir($destination_path, 0755, true);
					}
					$fn_result=1;
					$hashed_filename=md5($_SESSION['LS'].'_'.$date_coupon);
					
					$filename_pdf='/var/www/html/lk.host.ru/personal/downloads/'.$hash_ls.'/'.$hashed_filename.'.pdf';
					
					if(!file_exists($filename_pdf)){
                
						copy('/mnt/coupons/'.$_SESSION['LS'].'  '.$date_coupon.'.pdf', $destination_path.'/'.$hashed_filename.'.pdf'); //.'_'.$hash_ls_time.'.pdf');
						$coupon_filename ='https://lk.host.ru/personal/downloads/'.$hash_ls.'/'.$hashed_filename.'.pdf';   //.'_'.$hash_ls_time.'.pdf';
						chmod($destination_path.'/'.$hashed_filename.'.pdf', 0644);
					}else{
						$coupon_filename ='https://lk.host.ru/personal/downloads/'.$hash_ls.'/'.$hashed_filename.'.pdf';   //.'_'.$hash_ls_time.'.pdf';
						chmod($destination_path.'/'.$hashed_filename.'.pdf', 0644);
					}
				}else {
					$fn_result = 0;
					$coupon_filename ='https://lk.host.ru/404';
				}
			}	
	}


	//Выбор ЛС
        $option = isset($_POST['listLS']) ? $_POST['listLS'] : false;
        if ($option) {
                $selected_ls=htmlentities($_POST['listLS'], ENT_QUOTES, "UTF-8");
		$_SESSION['LS']=$selected_ls;
        }
	//======ЗАПРОС КВИТАНЦИИ ДЛЯ CALC
	$SQL_DATE = "alter session set nls_date_format = 'dd.mm.yyyy'";
	$RES_SQL_DATE = oci_parse($conn, $SQL_DATE);
	oci_execute($RES_SQL_DATE);
	
	$qr_data = 'ST00012|Name=АО "Цифровые Решения"|PersonalAcc=40702810505000000291|BankName=_|BIC=041203602|CorrespAcc=_|PersAcc='.$_SESSION['LS'].'|PaymPeriod='.date("mY", strtotime($date_coupon)).'|Category=0';
   	QRcode::png($qr_data, $filename, $errorCorrectionLevel, $matrixPointSize, 6);
    ///////////////////////
  	//Копирование квитанций
    ///////////////////////
        $sql_date_coupon = "select add_months(last_day(trunc(sysdate))+1, -rownum) from pay_ls where rownum < 13";	
	$sql_get_ls_adress = 'select ADDRESS from SARV_PAY.PAY_LS WHERE NUM='.$_SESSION['LS'];
	$res_sql_get_ls_adress = oci_parse($conn, $sql_get_ls_adress);
	oci_define_by_name($res_sql_get_ls_adress, 'ADDRESS', $ls_address);
	oci_execute($res_sql_get_ls_adress);
	oci_fetch($res_sql_get_ls_adress);
	$sql_get_calc = "select distinct
                                COUPON_KIND_NUM as USLUGA_NUM
				,NVL((select moniker from SARV_PAY.PAY_COUPON_KIND where num=pc.COUPON_KIND_NUM),(select moniker from SARV_PAY.PAY_COUPON_KIND_1 where num=pc.COUPON_KIND_NUM)) AS USLUGA
                                ,NVL(SALDO,0) as DOLG
                                ,NVL(CALC,0) as NACH
                                ,(RECALC-ADVANS) as PERERASCH
                                ,NVL(SALDO,0)+NVL(CALC,0)+(RECALC-ADVANS) as ITOGO
                        from SARV_PAY.PAY_COUPON_ALL pc WHERE pc.lsnum=".$selected_ls." and pc.dt_coupon=to_date('".$date_coupon."','dd.mm.yyyy') ORDER BY USLUGA_NUM";
	$res_sql_get_calc = oci_parse($conn, $sql_get_calc);
	oci_execute($res_sql_get_calc);
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
    background-color: #68a916;
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
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 id="header">Начисления и задолжность<h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Начисления и задолжность</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
		    <div class="invoice p-3 mb-3">
                <div class="col-12" style="height: 197px;">
                  <h4>
                    <small id="qrcod" class="float-right" style="z-index: 100;position: absolute;top: 62px;right: 90px;"><?php echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" /><hr/>';?>   </small>
                    <small id="plati" class="float-right" style="z-index: 0;float: right!important;margin-top: -10px;"><img src="../personal/images/qr.png"><hr>   </small>
					<div id="header2"><i class="fas fa-globe"></i>Начисления за <?php echo $_SESSION['current_month'] . " " .date("Y", strtotime($date_coupon)); ?></div>
					    <div class="row invoice-info">
							<div class="col-sm-10 invoice-col" style="font-family: Times, Times New Roman, Georgia, serif; font-size: 14pt;">
									<form action="" onsubmit="return beforeSubmit()" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">				
									    <div class="PeriodSelect" style="padding:10px;">
											<strong id="rp">Расчетный период: </strong>
												<select name="month" id="months" style="box-sizing: border-box;margin: 0 0 0 10px;width:200px; font-family: Times, Times New Roman, Georgia, serif; font-size: 14pt;" class="custom-select" onchange='this.form.submit()'>    <!-- -->
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
 
													$query = "select * from (select add_months(trunc(sysdate,'MM'), -rownum+1) d
															  from pay_ls where rownum < 13) where d>=to_date('01.01.2021','dd.mm.yyyy')";
													$state=oci_parse($conn, $query);
													oci_execute($state, OCI_COMMIT_ON_SUCCESS);
													echo '<option hidden selected="selected"  value="">'.$_SESSION['selected_month'].'</option>';
													while($row = oci_fetch_array($state, OCI_BOTH)){
														$month = $_monthsList[date(".m.", strtotime($row[0]))]; 
														$mm = date("m", strtotime($row[0]));
														echo "<option style='' value=".$row[0].">". $month. " ".date("Y", strtotime($row[0])) ."</option>";													
													}
												?>												
												</select>
											<br>
										</div>	
										<strong id="adr" style="padding:10px;">Адрес: </strong><?php echo $ls_address; ?>
										<noscript><input type="submit" value="Submit"></noscript>
									</form>
							</div>
						</div>
                  </h4>
                </div>
				<div class="col-12 table-responsive">
					<table id="datatable-buttons" class="styled-table" style="width:100%">
					<thead>
						<tr>
							<th rowspan="2">Код услуги</th>
							<th rowspan="2">Услуга</th>
							<th rowspan="2" style="text-align: center;">Долг (руб.)</th>
							<th rowspan="2" style="text-align: center;">Начислено (руб.)</th>
							<th rowspan="2" style="text-align: center;">Перерасч.(руб.)</th>
							<th rowspan="2" style="text-align: center;">Итого (руб.)</th>
						</tr>
					</thead>
					<tbody>
						<tr>
						<?php
							while (oci_fetch($res_sql_get_calc)){
								echo "<tr>";
								echo "<td>". oci_result($res_sql_get_calc, 'USLUGA_NUM') . "</td>"; 
								echo "<td>". oci_result($res_sql_get_calc, 'USLUGA') . "</td>";
								echo "<td class='dolg' style='text-align: center;'>". oci_result($res_sql_get_calc, 'DOLG') . "</td>";
								echo "<td class='nach' style='text-align: center;'>". oci_result($res_sql_get_calc, 'NACH') . "</td>";
								echo "<td class='prt' style='text-align: center;'>". oci_result($res_sql_get_calc, 'PERERASCH') . "</td>";
								echo "<td class='itog' style='text-align: center;'>". oci_result($res_sql_get_calc, 'ITOGO') . "</td>";
								echo "</tr>";
							}   
						?>
						</tr>
					</tbody>
					</table>
				</div>
				<!--Table end-->
					<div class="row no-print">
						<div class="col-12">
							<a href="https://online.sberbank.ru/CSAFront/payOrderPaymentLogin.do?ReqId=643273358286&ST=ufs.billing" target="_blank" class="btn btn-success float-right" style="margin-right: 5px;background-color:#68a916;"><i class="far fa-credit-card"></i> Оплатить через СербанкОнлайн</a>
							<a href=<?php print_r($coupon_filename); ?> id="get_fn" target="_blank" class="btn btn-default float-right" style="margin-right: 5px;"><i class="fas fa-download"></i> Скачать квитанцию</a>                   
						</div>
					</div>
				</div>
				</div>
			</div>
          </div><!-- /.col -->
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
<!-- ./wrapper -->

<?php require "../scripts.php" ?>
<script>
var fn_result = "<?= $fn_result ?>";

var get_fn = document.getElementById("get_fn");
var qrcod = document.getElementById("qrcod");
var plati = document.getElementById("plati");
var header = document.getElementById("header");
var header2 = document.getElementById("header2");
var rp = document.getElementById("rp");
var adr = document.getElementById("adr");
var adrstr = document.getElementById("adrstr");



var table = document.getElementById("datatable-buttons");
var ths = table.getElementsByTagName('th');
var tds_dolg = table.getElementsByClassName('dolg');

for(var i=0;i<tds_dolg.length;i++){
	tds_dolg[i].innerText=tds_dolg[i].innerText.replace(/,/, '.')
	if (tds_dolg[i].innerText[0]=='.'){
		tds_dolg[i].innerText=tds_dolg[i].innerText.replace(/./, '0.')
	}
}

var tds_nach = table.getElementsByClassName('nach');

for(var i=0;i<tds_nach.length;i++){
	tds_nach[i].innerText=tds_nach[i].innerText.replace(/,/, '.')
	if (tds_nach[i].innerText[0]=='.'){
		tds_nach[i].innerText=tds_nach[i].innerText.replace(/./, '0.')
	}
}

var tds_prt = table.getElementsByClassName('prt');

for(var i=0;i<tds_prt.length;i++){
	tds_prt[i].innerText=tds_prt[i].innerText.replace(/,/, '.')
	if (tds_prt[i].innerText[0]=='.'){
		tds_prt[i].innerText=tds_prt[i].innerText.replace(/./, '0.')
	}
}

var sum = 0;
var tds_itog = table.getElementsByClassName('itog');

for(var i=0;i<tds_itog.length;i++){
	tds_itog[i].innerText=tds_itog[i].innerText.replace(/,/, '.')
	if (tds_itog[i].innerText[0]=='.'){
		tds_itog[i].innerText=tds_itog[i].innerText.replace(/./, '0.')
	}
	if(parseFloat(tds_itog[i].innerText)>0)
		sum += isNaN(tds_itog[i].innerText) ? 0 : parseFloat(tds_itog[i].innerText);
}

var row = table.insertRow(table.rows.length);
var cell = row.insertCell(0);

cell.setAttribute('colspan', ths.length);
cell.setAttribute('style','margin: 10px;padding: 10px;border: 1px solid #1c781c;font-weight:bold;')

var totalBalance  = document.createTextNode('Итого к оплате: ' + sum.toFixed(2) + ' руб.');
cell.appendChild(totalBalance);


if (fn_result==0){
get_fn.hidden = true;
}
else
{
get_fn.hidden = false;
}

if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
	qrcod.style="z-index: 100;/* position: absolute; */top: 62px;right: 90px;"
	plati.hidden = true;
	header.outerHTML = '<h5>' + header.innerHTML + '</h5>';
	header2.outerHTML = '<h5>' + header.innerHTML + '</h5>';
	rp.outerHTML = '<h6><strong>' + rp.innerHTML + '</strong></h6>';
	adr.outerHTML = '<h6><strong>' + adr.innerHTML + '</strong></h6>';
	adrstr.outerHTML = '<h6>' + adrstr.innerHTML + '</h6>';
}else{

}
</script>
</script>
</body>
</html>

