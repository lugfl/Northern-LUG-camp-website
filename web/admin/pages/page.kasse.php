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
			$anmeldungid		= http_get_var('anmeldungid');
			$events_bezahlt		= http_get_var('events_bezahlt');
			$artikel_bezahlt	= http_get_var('artikel_bezahlt');
			$uSQL = "SELECT username,email FROM account WHERE accountid = '".$accountid."'";
			$uquery = my_query($uSQL);
			$uarray = mysql_fetch_array($uquery);
			$username = $uarray['username'];
			$email = $uarray['email'];

			$msg = 'Hallo '.$username.',\n\n';
			$msg .= 'Soeben wurden folgende Events und/oder Artikel, die du für das LugCamp 2008 gebucht hast als bezahlt markiert:\n\n';
			
			$nomsg = $msg;

			$eSQL = "SELECT eventid,bezahlt FROM event_anmeldung_event ";
			$eSQL .= "WHERE anmeldungid = '".$anmeldungid."'";
			$eres = my_query($eSQL);
			while($row = mysql_fetch_object($eres)) {
				if($row->bezahlt == NULL && $events_bezahlt[$row->eventid] == 'on') {
					$SQL = "UPDATE event_anmeldung_event SET bezahlt = NOW() ";
					$SQL .= "WHERE anmeldungid = '".$anmeldungid."' AND eventid='".$row->eventid."'";
					$res = my_query($SQL);
					$ret .= '<p>Event #'.$row->eventid.' als bezahlt markiert!</p>';
					
					$iSQL = "SELECT * FROM event_event WHERE eventid='".$row->eventid."'";
					$ires = my_query($iSQL);
					$iarray = mysql_fetch_array($ires);
					
					$msg .= $iarray['name'].'\n';
				} elseif($row->bezahlt != NULL && !$events_bezahlt[$row->eventid]) {
					$SQL = "UPDATE event_anmeldung_event SET bezahlt = NULL ";
					$SQL .= "WHERE anmeldungid = '".$anmeldungid."' AND eventid='".$row->eventid."'";
					$res = my_query($SQL);
					$ret .= '<p>Event #'.$row->eventid.' NICHT MEHR als bezahlt markiert!</p>';	
				}
			}
			
			$aSQL = "SELECT * FROM event_account_artikel ";
			$aSQL .= "WHERE accountid = '".$accountid."'";
			$ares = my_query($aSQL);
			while($row = mysql_fetch_object($ares)) {
				if($row->bezahlt == NULL && $artikel_bezahlt[$row->accountartikelid] == 'on') {
					$SQL = "UPDATE event_account_artikel SET bezahlt = NOW() ";
					$SQL .= "WHERE accountid = '".$accountid."' AND accountartikelid='".$row->accountartikelid."'";
					$res = my_query($SQL);
					$ret .= '<p>Artikel #'.$row->accountartikelid.' als bezahlt markiert!</p>';
					
					$iSQL = "SELECT * FROM event_artikel WHERE artikelid='".$row->artikelid."'";
					$ires = my_query($iSQL);
					$iarray = mysql_fetch_array($ires);
					
					if($row->groesse) {
						$groesse = ' Größe '.$row->groesse.' ';
					} else { $groesse = ''; }
										
					$msg .= $row->anzahl.'x '.$iarray['name'].$groesse.'\n';					
				} elseif($row->bezahlt != NULL && !$artikel_bezahlt[$row->accountartikelid]) {
					$SQL = "UPDATE event_account_artikel SET bezahlt = NULL ";
					$SQL .= "WHERE accountid = '".$accountid."' AND accountartikelid='".$row->accountartikelid."'";
					$res = my_query($SQL);
					$ret .= '<p>Artikel #'.$row->accountartikelid.' NICHT MEHR als bezahlt markiert!</p>';
				}
			}

			if($nomsg != $msg) {
				$msg .= '\nGruß\nJan Boysen\nKassenwart Lug Flensburg';
				$send_mail = my_mailer('kasse@lug-camp-2008.de',$email,'Geldeingang LugCamp 2008',$msg);
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
				<p class="bezahlt">Gesamtsumme zu zahlen: '.number_format($topay,2,',','.').' &euro;</p>
				<p class="bezahlt">Gesamtsumme als bezahlt verbucht: '.number_format($payed,2,',','.').' &euro;</p>
				<p class="zuzahlen">Defizit: '.number_format(($topay-$payed),2,',','.').' &euro;</p>
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
