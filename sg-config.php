<?php
$config=array(
'DB_HOST'=>'//10.0.0.1:1521/db',
'DB_USERNAME'=>'username',
'DB_PASSWORD'=>'password',
'DB_DATABASE'=>'db',
'DB_LOGIN_SQL'=>'select ID_USER, LOGIN from PAY_USERS WHERE LOGIN=:$login AND PASS=:$password',
'DB_USERSCOUNTSQL'=>'select COUNT(*) AS NUM_ROWS from PAY_USERS WHERE LOGIN=:$login AND PASS=:$password',
);

 $host=$config['DB_HOST'];
 $dbuser=$config['DB_USERNAME'];
 $dbpass=$config['DB_PASSWORD'];
 $conn = oci_connect($dbuser, $dbpass, $host,'UTF8');
 
 //Адрес базы данных
 define('SG_DBSERVER','//10.0.0.1:1521/db');

 //Логин БД
 define('SG_DBUSER','username');

 //Пароль БД
 define('SG_DBPASSWORD','password');

 //БД
 define('SG_DATABASE','db');

 //Префикс БД
 define('SG_DBPREFIX','SG_');

 define('SG_BR_USER','user');
 
 define('SG_BR_PSWRD','password');

 //Errors
 define('SG_ERROR_CONNECT','Немогу соеденится с БД');

 //Errors
define('SG_NO_DB_SELECT','Данная БД отсутствует на сервере');

define('SG_ERR_LS_EMPTY','Поле лицевого счета не может быть пустым');

define('SG_ERR_LS_ERROR','Не правильно введен лицевой счет');

define('SG_ERR_LS_SORRY','К сожалению ЛС: <b>');

define('SG_ERR_LS_ADDED','</b> уже добавлен!');

 define('SG_ERR_LS_NOTFOUND','</b> не найден!'); 

//Адрес хоста сайта
define('SG_HOST','https://'. $_SERVER['SERVER_NAME'] .'');

//Адрес почты от кого host
define('SG_MAIL_AUTOR','<noreply@host.ru>');

define( 'SG_KEY',         '' );
define( 'SECURE_AUTH_KEY',  '' );
define( 'LOGGED_IN_KEY',    '' );
define( 'NONCE_KEY',        '' );
define( 'AUTH_SALT',        '' );
define( 'SECURE_AUTH_SALT', '' );
define( 'LOGGED_IN_SALT',   '' );
define( 'NONCE_SALT',       '' ); 
?>
