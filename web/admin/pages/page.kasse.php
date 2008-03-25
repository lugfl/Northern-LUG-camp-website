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
						$SQL = "UPDATE event_anmeldung_event SET bezahlt = NOW() ";
						$SQL .= "WHERE anmeldungid = '".$anmeldungid."' AND eventid='".$eventid."'";
						$res = my_query($SQL);
						$ret .= '<p>Event #'.$eventid.' als bezahlt markiert!</p>';
					}
				}
			}
			if($artikel_bezahlt) {
				foreach($artikel_bezahlt as $artikelid=>$artikelbezahlt) {
					if($artikelbezahlt == 'on') {
						$SQL = "UPDATE event_account_artikel SET bezahlt = NOW() ";
						$SQL .= "WHERE accountid = '".$accountid."' AND accountartikelid='".$artikelid."'";
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
				$anmeldungen = 0;
				$topay = 0;
				$payed = 0;
				
				$eSQL = "SELECT eventid,charge FROM event_event";
				$equery = my_query($eSQL);
				$earray = array();
				while($row = mysql_fetch_assoc($equery)) {
					$earray[$row['eventid']] = $row['charge'];
				}
				
				$aSQL = "SELECT artikelid,preis FROM event_artikel";
				$aquery = my_query($aSQL);
				$aarray = array();
				while($row = mysql_fetch_assoc($aquery)) {
					$aarray[$row['artikelid']] = $row['preis'];
				}
				
				$SQL = "SELECT * FROM event_anmeldung_event";
				$query = my_query($SQL);
				while($row = mysql_fetch_object($query)) {
					$anmeldungen++;
					$topay = $topay+$earray[$row->eventid];
					if($row->bezahlt != NULL)  {
						$payed = $payed+$earray[$row->eventid];
					}
				}
				
				$SQL = "SELECT * FROM event_account_artikel";
				$query = my_query($SQL);
				while($row = mysql_fetch_object($query)) {
					$topay = $topay+$aarray[$row->artikelid]*$row->anzahl;
					if($row->bezahlt != NULL)  {
						$payed = $payed+$aarray[$row->artikelid]*$row->anzahl;
					}
				}
				
				$ret = '
				<h1>Kasse</h1>
				<p class="bezahlt">Anmeldungen: '.$anmeldungen.'</p>
				<p class="bezahlt">Gesamtsumme zu zahlen: '.$topay.' &euro;</p>
				<p class="bezahlt">Gesamtsumme als bezahlt verbucht: '.$payed.' &euro;</p>
				<p class="zuzahlen">Defizit: '.($topay-$payed).' &euro;</p>
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
