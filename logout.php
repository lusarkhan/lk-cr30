<?php
 session_start();
header("X-XSS-Protection: 1; mode=block");
 
 include './sg-config.php'; 
 
 $host=$config['DB_HOST'];
 $dbuser=$config['DB_USERNAME'];
 $dbpass=$config['DB_PASSWORD'];
 $dbloginget=$config['DB_LOGIN_SQL'];
 $dbuserscountget=$config['DB_USERSCOUNTSQL'];
 $conn = oci_connect($dbuser, $dbpass, $host);
 
 if(isset($_SESSION['ID_USER']))
 {
 $insert_sessions_dt_end = "UPDATE T_LK_SESSIONS SET DT_END=SYSDATE WHERE DT_END is NULL AND T_SG_REG_ID=".$_SESSION['ID_USER'];
 $ex_insert_sessions_dt_end =oci_parse($conn, $insert_sessions_dt_end );
 oci_execute($ex_insert_sessions_dt_end);
 }
 
 require('./sg-includes/deletefile.php'); 

 $hash_ls = md5($_SESSION['LS']);
 $destination_path = '/var/www/html/lk.host.ru/personal/downloads/'.$hash_ls;


 $qr_files = glob($destination_path.'/'.$_SESSION['LS'].'-*.png');
 foreach ($qr_files as $file) {
    if (is_file($file)) {
        unlink($file);
    }
 }

 $possibleFiles = glob($destination_path.'/'.$_SESSION['LS'].'_*.pdf'); 
 foreach ($possibleFiles as $file) {
    if (is_file($file)) {
        unlink($file);
    }
    rmdir($destination_path);
 }

 $_SESSION = array();
 if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
	
	header('Location:'.SG_HOST.'/login');
}

session_destroy();

?>
