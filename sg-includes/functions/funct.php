<?php
function dateGap($date1, $date2)
{
	$time = new DateTime($date1);

	$since_time = $time->diff( new DateTime($date2) );
	
	$A['days'] = $since_time->days;
	$A['hours'] = $since_time->days * 24 + $since_time->h;	
	$A['minutes'] = ($since_time->days * 24 * 60) + ($since_time->h * 60) + $since_time->i;	

return $A;
}
function Insert_T_LK_SESSIONS($id_user,$act,$user_agent)
{
        $insert_t_lk_sessions = "INSERT INTO T_LK_SESSIONS (T_SG_REG_ID, DT_BEG, DT_END, IP_ADDR, ACT, HTTP_USER_AGENT)
                                 VALUES ($id_user,SYSDATE,'','".htmlspecialchars($_SERVER['REMOTE_ADDR'])."',$act,$user_agent)";
}


 date_default_timezone_set('Europe/Moscow');
 function escape_str($data)
 {
    if(is_array($data))
    {
        if(get_magic_quotes_gpc())
           $strip_data = array_map("stripslashes", $data);
           $result = array_map("mysql_real_escape_string", $strip_data);
           return  $result;
    }
    else
    {
        if(get_magic_quotes_gpc())
           $data = stripslashes($data);
           $result = mysql_real_escape_string($data);
           return $result;
    }
 }

 function sendMessageMail($to, $from, $title, $message)
 {
   $to = $to;
   $from = $from;

   $subject = $title;
   $subject = '=?utf-8?b?'. base64_encode($subject) .'?=';

   $headers = "Content-type: text/html; charset=\"utf-8\"\r\n";
   $headers .= "From: ". $from ."\r\n";
   $headers .= "MIME-Version: 1.0\r\n";
   $headers .= "Date: ". date('D, d M Y h:i:s O') ."\r\n";

   if(!mail($to, $subject, $message, $headers))
      return 'Ошибка отправки письма!';
   else
      return true;
 }

 function gen_token()
 {
	if (function_exists('com_create_guid') === true) {
		return trim(com_create_guid(), '{}');
	}

	return sprintf(
		'%04X%04X-%04X-%04X-%04X-%04X%04X%04X', 
		mt_rand(0, 65535), 
		mt_rand(0, 65535),
		mt_rand(0, 65535),
		mt_rand(16384, 20479), 
		mt_rand(32768, 49151),
		mt_rand(0, 65535),
		mt_rand(0, 65535), 
		mt_rand(0, 65535)
	);
 }
 
 function showErrorMessage($data)
 {
    $err = '<ul>'."\n";

    if(is_array($data))
    {
        foreach($data as $val)
            $err .= '<li style="color:red;">'. $val .'</li>'."\n";
    }
    else
        $err .= '<li style="color:red;">'. $data .'</li>'."\n";

    $err .= '</ul>'."\n";

    return $err;
 }

 function mysqlQuery($sql)
 {
    $res = oci_parse($conn, $query);
    if(!$res)
    {
        $message  = 'Неверный запрос: ' . mysql_error() . "\n";
        $message .= 'Запрос целиком: ' . $sql;
        die($message);
    }

    return $res;
 }

 function salt()
 {
    $salt = substr(md5(uniqid()), -8);
    return $salt;
 }
