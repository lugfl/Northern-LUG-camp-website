<?php

// Defaultwerte fuer die Config

define ('WEB_DATEFORMAT','%d.%m.%Y, %H:%m');
define ('WEB_NEWSPAGE','./news.php');
define ('WEB_NEWSTEASER_ANZAHL',3);
define ('DEBUG',0);


// Variable auf true setzen, damit Seiten mit Formularen disabled werden.
$MAINTENANCE_MODE = false;

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

// ID des Events, mit dem die zwei Euro verbucht werden
$EVENT_SCHWIMMEN['abzeichen_event_id'] = 0;

// ID des Events, mit dem die Hallenbuchung und Quota geregelt wird.
$EVENT_SCHWIMMEN['schwimmhalle_event_id'] = 0;

if(is_file('.htconfig.php')) {
	include_once('.htconfig.php');
}

?>
