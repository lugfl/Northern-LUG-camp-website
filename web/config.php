<?php

// Defaultwerte fuer die Config

define ('WEB_DATEFORMAT','%d.%m.%Y, %H:%m');
define ('WEB_NEWSPAGE','./news.php');
define ('WEB_NEWSTEASER_ANZAHL',3);
define ('DEBUG',0);

$DB_user = 'test';
$DB_pass = 'test';
$DB_name = 'test';
$DB_host = "localhost";


$DB['DEFAULT']['user'] = $DB_user;
$DB['DEFAULT']['pass'] = $DB_pass;
$DB['DEFAULT']['name'] = $DB_name;
$DB['DEFAULT']['host'] = $DB_host;

if(is_file('.htconfig.php')) {
	include_once('.htconfig.php');
}

?>
