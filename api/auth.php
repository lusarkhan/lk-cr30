<?php
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
include './../sg-config.php';

include './../sg-includes/functions/funct.php';

session_start();
date_default_timezone_set('Europe/Samara');
$today = date('d.m.Y');
 
header('Content-Type: application/json; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ob_start();
$errors = array();
$streamArr = array();
$data = array();

function Login(){
	echo "1";
}
$ee = implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, 10), 10));

    $dt_to_sql="";
    $_sg_reg_id="";
	$SQL_DATE = "alter session set nls_date_format = 'dd.mm.yyyy'";
	$RES_SQL_DATE = oci_parse($conn, $SQL_DATE);
	oci_execute($RES_SQL_DATE);
	
	if(isset($_GET['token']) and $_GET['token']<>'')
	{
		$token=addslashes($_GET['token']);
		$token=htmlspecialchars($token,ENT_QUOTES, 'UTF-8');
		$token=strval($token);
		
		$sql_get_token_count="SELECT COUNT(*) AS NUM_ROWS FROM SG_REG WHERE AUTH_TOKEN='" .$token ."'";
		$res_sql_get_token_count = oci_parse($conn, $sql_get_token_count);
		oci_define_by_name($res_sql_get_token_count, 'NUM_ROWS', $num_rows);
		oci_execute($res_sql_get_token_count);
		oci_fetch($res_sql_get_token_count);
		
		if ($num_rows>0)
		{		
			if(isset($_GET['token']) and isset($_GET['uname']) and $_GET['uname']<>'' and isset($_GET['upass']) and $_GET['upass']<>'') 
			{
				$login = addslashes(htmlspecialchars($_GET['uname']));
				$login = mb_strtolower($login);
				$login=htmlspecialchars($login,ENT_QUOTES, 'UTF-8');
				$login=strval($login);
				$sql_get_salt = "select SALT from sg_reg WHERE LOGIN='".$login."' AND STATUS=1";
				$get_salt_query = oci_parse($conn, $sql_get_salt);
				oci_execute($get_salt_query);
				
				$row = oci_fetch_assoc($get_salt_query);
				$password = md5(md5(trim($_GET['upass'])).$row['SALT']);
				
				$uname=$_GET['uname'];


				$sql_get_sg_reg_login = "select COUNT(*) AS NUM_ROWS from SG_REG where LOGIN='" . $uname."' AND STATUS=1";
				$res_sql_get_sg_reg_login  = oci_parse($conn, $sql_get_sg_reg_login);
				oci_define_by_name($res_sql_get_sg_reg_login, 'NUM_ROWS', $num_rows_logins);
				oci_execute($res_sql_get_sg_reg_login);
				oci_fetch($res_sql_get_sg_reg_login);
				
				if ($num_rows_logins>0)   
				{
					$row_query = "select COUNT(*) AS NUM_ROWS from sg_reg WHERE LOGIN='".$login."' AND PASS='".$password."' AND STATUS=1";
					$get_row_query = oci_parse($conn, $row_query);
					oci_define_by_name($get_row_query, 'NUM_ROWS', $num_rows);
					oci_execute($get_row_query);
					oci_fetch($get_row_query);
					
					if ($num_rows == 1) { 
						$sql_get_id="SELECT ID,ACTIVE_HEX FROM SG_REG WHERE LOGIN='" . $uname."'";
						$res_sql_get_id = oci_parse($conn, $sql_get_id);
						oci_define_by_name($res_sql_get_id, 'ID', $sg_reg_id);
						oci_define_by_name($res_sql_get_id, 'ACTIVE_HEX', $sg_reg_active_hex);
						oci_execute($res_sql_get_id);
						oci_fetch($res_sql_get_id);
						
						$header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

						$payload = json_encode([$sg_reg_id]);

						$base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

						$base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

						$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $sg_reg_active_hex, true);

						$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

						$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
						$result = json_encode(['auth_status'=>'success','sgregid_'=>$sg_reg_id,'jwt_'=>$jwt]);
						print $result;
					}
					else{print json_encode(["auth_status"=>"error"]);}
				}else{print json_encode(["auth_status"=>"error"]);} 
			}					
			elseif(isset($_GET['token']) and isset($_GET['jwt']) and $_GET['jwt']<>'' and isset($_GET['uid']) and $_GET['uid']<>'')  
			{	
			
				$u_id=addslashes($_GET['uid']);
				$u_id = preg_replace("/[^0-9]/", "", $u_id);
				$u_id=str_replace('.','',$u_id);
				$u_id=str_replace(':','',$u_id);
				$u_id=str_replace("'",'',$u_id);
				$u_id=htmlspecialchars($u_id,ENT_QUOTES, 'UTF-8');
				$u_id=stripslashes($u_id);
				
				$u_jwt=addslashes($_GET['jwt']);
				$u_jwt=htmlspecialchars($u_jwt,ENT_QUOTES, 'UTF-8');
				$u_jwt=stripslashes($u_jwt);
				
				$sql_get_id="SELECT ID,ACTIVE_HEX FROM SG_REG WHERE ID='" . $u_id."'";
				$res_sql_get_id = oci_parse($conn, $sql_get_id);
				oci_define_by_name($res_sql_get_id, 'ID', $sg_reg_id_);
				oci_define_by_name($res_sql_get_id, 'ACTIVE_HEX', $sg_reg_active_hex_);
				oci_execute($res_sql_get_id);
				oci_fetch($res_sql_get_id);
				
				$header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
				$payload = json_encode([$sg_reg_id_]);
				$base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
				$base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
				$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $sg_reg_active_hex_, true);
				$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
				$jwt_2 = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

				if($u_jwt==$jwt_2){

					$sql_get_id="SELECT ID,FAM,IM,OTCH,LS FROM SG_REG WHERE ID='" . $u_id ."'";
					$res_sql_get_id = oci_parse($conn, $sql_get_id);
					oci_define_by_name($res_sql_get_id, 'ID', $sg_reg_id);
					oci_define_by_name($res_sql_get_id, 'FAM', $sg_reg_fam);
					oci_define_by_name($res_sql_get_id, 'IM', $sg_reg_im);
					oci_define_by_name($res_sql_get_id, 'OTCH', $sg_reg_otch);
					oci_define_by_name($res_sql_get_id, 'LS', $sg_reg_ls);
					oci_execute($res_sql_get_id);
					oci_fetch($res_sql_get_id);
					$_sg_reg_id=$sg_reg_id;
				
			
					$query = "select * from (select add_months(trunc(sysdate,'MM'), -rownum+1) d from pay_ls where rownum < 13) where d>=to_date('01.01.2021','dd.mm.yyyy')";
					$state=oci_parse($conn, $query);
					oci_execute($state, OCI_COMMIT_ON_SUCCESS);
				    while($row = oci_fetch_array($state, OCI_BOTH)){
						$mm = date("m", strtotime($row[0]));
						$date_coupon = $row[0];
						$sql_get_payments = "select dtz_pay AS DTZPAY, (select name from Max_payments_collector where kp_num=m.kp_num) AS SBOR,
																		coupon_kind AS CKND,
																		(select name from Max_payments_service where coupon_kind=m.coupon_kind and rownum=1) AS USLUGA,
																		coupon_start AS DTBEG, coupon_end AS DETEND,
																		summa AS SUMMA from Max_payments_all m 
																		where lsnum_dtpay='".$sg_reg_ls."'||'_'||to_char(trunc(to_date('01.".$mm.".".date("Y", strtotime($row[0]))."','dd.mm.yyyy'),'MM'),'mm.yyyy') 
																		and id_parent is null order by 1,2,3,4,5";
																		
						$res_get_payments = oci_parse($conn, $sql_get_payments);
						oci_execute($res_get_payments);
						while (($pay_row=oci_fetch_array($res_get_payments))){									   
							$data = array(
									"sql_status"=> "success",
							        "sgregid"=>$_sg_reg_id,
									"fio"=>$sg_reg_fam.' '.$sg_reg_im.' '.$sg_reg_otch,
									'payment' => array(
										'DTZPAY' => $pay_row['DTZPAY'],
										'SBOR' => $pay_row['SBOR'],
										'CKND' => $pay_row['CKND'],
										'USLUGA' => $pay_row['USLUGA'],
										'DTBEG' => $pay_row['DTBEG'],
										'DETEND' => $pay_row['DETEND'],
										'SUMMA' =>	$pay_row['SUMMA']
									)
								);
								 $layers = explode(',', date("M", strtotime($row[0])));
								foreach ($layers as $layer) {
									$layersPart = explode(':', $layer);
									foreach ($layersPart as $key => $value) {
										$data['payment']['MONTH'] = $value;
									}
								}			
						}
						$streamArr[] = $data;				
			        }
						
					print_r(json_encode($streamArr));	
				 }else{$result = array("sql_status"=>"error","sgregid"=>0);
                                         print json_encode($result);}
            }
			elseif(isset($_GET['token']) and isset($_GET['jwt_']) and $_GET['jwt_']<>'' and isset($_GET['uid_']) and $_GET['uid_']<>'' and isset($_GET['act_']) and $_GET['act_']==1)  
			{	
				$u_id=addslashes($_GET['uid_']);
				$u_id = preg_replace("/[^0-9]/", "", $u_id);
				$u_id=str_replace('.','',$u_id);
				$u_id=str_replace(':','',$u_id);
				$u_id=str_replace("'",'',$u_id);
				$u_id=htmlspecialchars($u_id,ENT_QUOTES, 'UTF-8');
				$u_id=stripslashes($u_id);
				
				$u_jwt=addslashes($_GET['jwt_']);
				$u_jwt=htmlspecialchars($u_jwt,ENT_QUOTES, 'UTF-8');
				$u_jwt=stripslashes($u_jwt);
				
				$sql_get_id="SELECT ID,ACTIVE_HEX FROM SG_REG WHERE ID='" . $u_id."'";
				$res_sql_get_id = oci_parse($conn, $sql_get_id);
				oci_define_by_name($res_sql_get_id, 'ID', $sg_reg_id_);
				oci_define_by_name($res_sql_get_id, 'ACTIVE_HEX', $sg_reg_active_hex_);
				oci_execute($res_sql_get_id);
				oci_fetch($res_sql_get_id);
				
				$header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
				$payload = json_encode([$sg_reg_id_]);
				$base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
				$base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
				$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $sg_reg_active_hex_, true);
				$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
				$jwt_2 = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

				if($u_jwt==$jwt_2){

						$sql_get_ls = "select * from t_lk_ls where T_SG_REG_ID=".$u_id . " and role_id=1";								
						$res_get_ls = oci_parse($conn, $sql_get_ls);
						oci_execute($res_get_ls);
						while ($ls_rows=oci_fetch_array($res_get_ls,OCI_BOTH)){
							foreach ($ls_rows as $row) {
								$sql_get_id="SELECT FIO,NUM,ADDRESS FROM PAY_LS WHERE NUM=" . $ls_rows['LS'];
								$res_sql_get_id = oci_parse($conn, $sql_get_id);
							
								oci_define_by_name($res_sql_get_id, 'FIO', $pay_ls_fio);
								oci_define_by_name($res_sql_get_id, 'NUM', $pay_ls_num);
								oci_define_by_name($res_sql_get_id, 'ADDRESS', $pay_ls_address);
								oci_execute($res_sql_get_id);
								oci_fetch($res_sql_get_id);
	
								$data = array(
										"ls_id"=> $ls_rows['ID'],
										"ls_address"=>$pay_ls_address,
										"ls_num"=>$ls_rows['LS'],
										"ls_fio"=>$pay_ls_fio,
										'status' => $ls_rows['STATUS']
								);	
							}
								//}
								$streamArr[] = $data;		
							
						}

		                        print_r(json_encode($streamArr));
						
						
				 }else{$result = array("sql_status"=>"error","sgregid"=>0);
                     print json_encode($result);}
            }
			elseif(isset($_GET['token']) and isset($_GET['jwt_']) and $_GET['jwt_']<>'' and isset($_GET['uid_']) and $_GET['uid_']<>'' and isset($_GET['act_']) and $_GET['act_']==2)  
			{	
				$u_id=addslashes($_GET['uid_']);
				$u_id = preg_replace("/[^0-9]/", "", $u_id);
				$u_id=str_replace('.','',$u_id);
				$u_id=str_replace(':','',$u_id);
				$u_id=str_replace("'",'',$u_id);
				$u_id=htmlspecialchars($u_id,ENT_QUOTES, 'UTF-8');
				$u_id=stripslashes($u_id);
				
				$u_jwt=addslashes($_GET['jwt_']);
				$u_jwt=htmlspecialchars($u_jwt,ENT_QUOTES, 'UTF-8');
				$u_jwt=stripslashes($u_jwt);
				
				$sql_get_id="SELECT ID,ACTIVE_HEX FROM SG_REG WHERE ID='" . $u_id."'";
				$res_sql_get_id = oci_parse($conn, $sql_get_id);
				oci_define_by_name($res_sql_get_id, 'ID', $sg_reg_id_);
				oci_define_by_name($res_sql_get_id, 'ACTIVE_HEX', $sg_reg_active_hex_);
				oci_execute($res_sql_get_id);
				oci_fetch($res_sql_get_id);
				

				$header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

				$payload = json_encode([$sg_reg_id_]);

				$base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

				$base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

				$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $sg_reg_active_hex_, true);

				$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

				$jwt_2 = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

				if($u_jwt==$jwt_2){

						$sql_get_ls = "select * from t_lk_ls where T_SG_REG_ID=".$u_id;								
						$res_get_ls = oci_parse($conn, $sql_get_ls);
						oci_execute($res_get_ls);

						while ($ls_rows=oci_fetch_array($res_get_ls,OCI_BOTH)){
							foreach ($ls_rows as $row) {
								$sql_get_id="SELECT FIO,NUM,ADDRESS FROM PAY_LS WHERE NUM=" . $ls_rows['LS'];
								$res_sql_get_id = oci_parse($conn, $sql_get_id);
							
								oci_define_by_name($res_sql_get_id, 'FIO', $pay_ls_fio);
								oci_define_by_name($res_sql_get_id, 'NUM', $pay_ls_num);
								oci_define_by_name($res_sql_get_id, 'ADDRESS', $pay_ls_address);
								oci_execute($res_sql_get_id);
								oci_fetch($res_sql_get_id);
	
								$data = array(
										"ls_id"=> $ls_rows['ID'],
										"ls_address"=>$pay_ls_address,
										"ls_num"=>$ls_rows['LS'],
										"ls_fio"=>$pay_ls_fio,
										'status' => $ls_rows['STATUS']
								);	
							}
								$streamArr[] = $data;		
							
						}
						
			            print_r(json_encode($streamArr));
						
						
				 }else{$result = array("sql_status"=>"error","sgregid"=>0);
                     print json_encode($result);} 
            }
			elseif(isset($_GET['token']) and isset($_GET['jwt_']) and $_GET['jwt_']<>'' and isset($_GET['uid_']) and $_GET['uid_']<>'' and isset($_GET['act_']) and $_GET['act_']==3 and isset($_GET['s_ls']) and $_GET['s_ls']<>'')  
			{	
				$u_id=addslashes($_GET['uid_']);
				$u_id = preg_replace("/[^0-9]/", "", $u_id);
				$u_id=str_replace('.','',$u_id);
				$u_id=str_replace(':','',$u_id);
				$u_id=str_replace("'",'',$u_id);
				$u_id=htmlspecialchars($u_id,ENT_QUOTES, 'UTF-8');
				$u_id=stripslashes($u_id);
				
				$u_jwt=addslashes($_GET['jwt_']);
				$u_jwt=htmlspecialchars($u_jwt,ENT_QUOTES, 'UTF-8');
				$u_jwt=stripslashes($u_jwt);

				$s_ls = addslashes($_GET['s_ls']);
                                $s_ls = preg_replace("/[^0-9]/", "", $s_ls);
                                $s_ls = str_replace('.','',$s_ls);
                                $s_ls = str_replace(':','',$s_ls);
                                $s_ls = str_replace("'",'',$s_ls);
                                $s_ls = htmlspecialchars($s_ls,ENT_QUOTES, 'UTF-8');
                                $s_ls = stripslashes($s_ls);
				
				$sql_get_id="SELECT ID,ACTIVE_HEX FROM SG_REG WHERE ID='" . $u_id."'";
				$res_sql_get_id = oci_parse($conn, $sql_get_id);
				oci_define_by_name($res_sql_get_id, 'ID', $sg_reg_id_);
				oci_define_by_name($res_sql_get_id, 'ACTIVE_HEX', $sg_reg_active_hex_);
				oci_execute($res_sql_get_id);
				oci_fetch($res_sql_get_id);
				
				$header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

				$payload = json_encode([$sg_reg_id_]);

				$base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

				$base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

				$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $sg_reg_active_hex_, true);

				$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

				$jwt_2 = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

				if($u_jwt==$jwt_2 and strlen($s_ls)==9){

								$sql_get_id="SELECT FIO,NUM,ADDRESS FROM PAY_LS WHERE NUM=" . $s_ls;
								$res_sql_get_id = oci_parse($conn, $sql_get_id);
							
								oci_define_by_name($res_sql_get_id, 'FIO', $pay_ls_fio);
								oci_define_by_name($res_sql_get_id, 'NUM', $pay_ls_num);
								oci_define_by_name($res_sql_get_id, 'ADDRESS', $pay_ls_address);
								oci_execute($res_sql_get_id);
								oci_fetch($res_sql_get_id);
	
								$data = array(
										"ls_address"=>$pay_ls_address,
										"ls_num"=>$pay_ls_num,
										"ls_fio"=>$pay_ls_fio
								);	
								$streamArr[] = $data;		
						
			            print_r(json_encode($streamArr));
						
						
				 }else{$result = array("sql_status"=>"error","sgregid"=>0);
                     print json_encode($result);}
            }		
			else{$result = array("sql_status"=>"error","sgregid"=>0);
                      print json_encode($result);}
                }else{echo "Invalid token.";} 
        }else{echo "Invalid token.";}
?>


