<?php

$PAGE['kasse']['name'] = "Kasse";
$PAGE['kasse']['navilevel'] = 1;
$PAGE['kasse']['login_required'] = 1;
$PAGE['kasse']['phpclass'] = 'HtmlPage_kasse';
$PAGE['kasse']['parent'] = 'root';

class HtmlPage_kasse extends HtmlPage {

	var $name = "Kasse";
	var $navilevel = 1;
	var $login_required = 1;

	function HtmlPage_kasse() {
	}
	
	function getContent() {
		$nickname 	= http_get_var('nickname');
		$action		= http_get_var('action');
		if($action == 'pay') {
			$type			= http_get_var('type');
			$accountid		= http_get_Var('accountid');
			$anmeldungid	= http_get_var('anmeldungid');
			$events_bezahlt	= http_get_var('events_bezahlt');
			$artikel_bezahlt	= http_get_var('artikel_bezahlt');
			if($events_bezahlt) {
				foreach($events_bezahlt as $eventid=>$event_bezahlt) {
					if($event_bezahlt == 'on') {
						$SQL = "UPDATE event_anmeldung_event SET bezahlt = NOW()";
						$SQL .= "WHERE anmeldungid = '".$anmeldungid."' AND eventid='".$eventid."'";
						$res = my_query($SQL);
						$ret .= '<p>Event #'.$eventid.' als bezahlt markiert!</p>';
					}
				}
			}
			if($artikel_bezahlt) {
				foreach($artikel_bezahlt as $artikelid=>$artikelbezahlt) {
					if($artikelbezahlt == 'on') {
						$SQL = "UPDATE event_account_artikel SET bezahlt = NOW()";
						$SQL .= "WHERE accountid = '".$accountid."' AND artikelid='".$artikelid."'";
						$res = my_query($SQL);
						$ret .= '<p>Artikel #'.$artikelid.' als bezahlt markiert!</p>';
					}
				}
			}
		} else {
			if($nickname) {
				$SQL = "SELECT * FROM account WHERE username = '$nickname'";
				$search_query = my_query($SQL);
				if(mysql_num_rows($search_query) >0) {
					$ret = '<p>Gefundene Nicknames:</p>';
					while($row = mysql_fetch_assoc($search_query)) {
						$ret .= '<a href="?p=account&accountid='.$row["accountid"].'">'.$row["username"].'</a>';
					}
				}
			} else {
				$ret = '
				<h1>Kasse</h1>
				<p>Hier kann Jan eintragen wer schon wof&uuml;r bezahlt hat.</p>
				<form action="?p=kasse" method="post">
				Nickname: <input type="text" name="nickname" />
				<p><input type="submit" value=" Suchen " /></p>
				</form>
				';
			}
		}
		return $ret;
	}

}


?>
