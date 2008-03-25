<?php

require_once('../lib/func.pear_mail.php');
require_once('Mail/RFC822.php');

$PAGE['remail']['name'] = "Aktivierungsmail";
$PAGE['remail']['navilevel'] = 1;
$PAGE['remail']['login_required'] = 1;
$PAGE['remail']['phpclass'] = 'HtmlPage_remail';
$PAGE['remail']['parent'] = 'none';

class HtmlPage_remail extends HtmlPage {

	var $name = "Aktivierungsmail";
	var $navilevel = 1;
	var $login_required = 1;

	function HtmlPage_remail() {
	}
	
	function getContent() {
		$accountid 	= http_get_var('a');
		
		$SQL = "SELECT a.accountid,a.username,a.email,ea.vorname FROM account a LEFT JOIN event_anmeldung ea ON a.accountid = ea.accountid WHERE a.accountid='".$accountid."'";
		$res = my_query($SQL);
		$row = mysql_fetch_assoc($res);

		$code = md5($row['username']);
		if($accountid < 10) { $code .= '0'; }
		if($accountid < 100) { $code .= '0'; }
		$code .= $accountid;
		$msg = "Hallo ".$row['vorname'].",\n\n"."Damit deine Anmeldung zum LugCamp 2008 erfolgreich abgeschlossen werden kann,";
		$msg .= " klicke bitte auf folgenden Link:\n\n";
		$msg .= 'http://'.$_SERVER['SERVER_NAME'].'/'.str_replace('admin/','',get_script_name()).'?p=anmeldung&code='.$code;
		$msg .= "\n\nDie Kontodaten zur Zahlung findest Du im Loginbereich.";
		$msg .= "\nIm Loginbereich wirst Du Zugriff auf alle Daten der Anmeldung bekommen ";
		$msg .= "\nund auch die Anmeldungen fuer Addons (LPI,T-Shirts) nachholen koennen.";
		$msg .= "\n";
		$msg .= "\nNeuigkeiten zur Webseite werden auf der Mailingliste bekanntgegeben.";
		$msg .= "\nDie Anmeldeseite der Mailingliste findest Du unter http://lists.lugcamp.org/cgi-bin/mailman/listinfo/teilnehmer";
		$msg .= "\n\nWir freuen uns auf Dich\n\ndie Mitglieder der LUG Flensburg";
		
		$send_mail	= my_mailer('anmeldung@lug-camp-2008.de',my_escape_string($row['email']),'Anmeldung LugCamp 2008',$msg);
		
		$ret .= '<p></p>';

		return $ret;
	}

}


?>
