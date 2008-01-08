<?php

// Defaultwerte fuer die Config

define ('WEB_DATEFORMAT','%d.%m.%Y, %H:%m');
define ('WEB_NEWSPAGE','./news.php');
define ('WEB_NEWSTEASER_ANZAHL',3);
define ('DEBUG',0);



$DB['DEFAULT']['user'] = 'test';
$DB['DEFAULT']['pass'] = 'test';
$DB['DEFAULT']['name'] = 'test';
$DB['DEFAULT']['host'] = 'localhost';

// Konfiguration fuer MailTX
$MAILER['localhost'] = 'www.lug-camp-2008.de';
$MAILER['host'] = 'localhost';
$MAILER['auth'] = false;
//$MAILER['username'] = '';
//$MAILER['password'] = '';

if(is_file('.htconfig.php')) {
	include_once('.htconfig.php');
}

?>
