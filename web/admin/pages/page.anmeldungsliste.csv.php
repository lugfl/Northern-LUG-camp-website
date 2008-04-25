<?php

$PAGE['anmeldungsliste_csv']['name'] = "Anmeldungen CSV";
$PAGE['anmeldungsliste_csv']['navilevel'] = 1;
$PAGE['anmeldungsliste_csv']['login_required'] = 1;
$PAGE['anmeldungsliste_csv']['phpclass'] = 'HtmlPage_anmeldungsliste_csv';
$PAGE['anmeldungsliste_csv']['parent'] = 'root';

require_once('lib/inc.database.php');

class HtmlPage_anmeldungsliste_csv extends HtmlPage {

	var $name = "Anmeldungen CSV";
	var $navilevel = 1;
	var $login_required = 1;
	var $barcodeupdates = 0;
	function HtmlPage_anmeldungsliste_csv() {
	}
	
	function paystatus($anmeldungid,$accountid,$events_array,$artikel_array) {
		$array = array();
		$e_SQL = "SELECT eventid,bezahlt FROM event_anmeldung_event WHERE anmeldungid = '".$anmeldungid."'";
		$e_res = my_query($e_SQL);
		while($e_row = mysql_fetch_assoc($e_res)) {
			$array['events'][$e_row['eventid']] = $e_row['bezahlt'];
			$topay += $events_array[$e_row['eventid']];
			if($e_row['bezahlt'] != 0) {
				$payed += $events_array[$e_row['eventid']];
			}
		}
						
		$a_SQL = "SELECT artikelid,anzahl,bezahlt FROM event_account_artikel WHERE accountid = '".$accountid."'";
		$a_res = my_query($a_SQL);
		while($a_row = mysql_fetch_assoc($a_res)) {
			$array['artikel'][$a_row['artikelid']] = $a_row['bezahlt'];
			$topay += $a_row['anzahl']*$artikel_array[$a_row['artikelid']];
			if($a_row['bezahlt'] != 0) {
				$payed +=  $a_row['anzahl']*$artikel_array[$a_row['artikelid']];
			}
		}
		
		$array['topay'] = $topay;
		$array['payed'] = $payed;
		return $array;
	}
	
	function getContent() {
		$this->barcodeupdate();
    		$ret = '';
		$SQL = "SELECT a.accountid,a.username,a.acl,UNIX_TIMESTAMP(a.crdate) AS crdate,a.active, l.name AS lugname, ";
		$SQL .= " an.anmeldungid,an.vorname,an.nachname ";
		$SQL .= " FROM account a LEFT JOIN event_lug l ON a.lugid=l.lugid ";
		$SQL .= " LEFT JOIN event_anmeldung an ON a.accountid=an.accountid ";
		$SQL .= " ORDER BY vorname"; 
		$res = my_query($SQL);
		
		if($res) {
			if(mysql_num_rows($res) >0) {
				$ret .= '"Nr";"Vorname";"Name";"Nickname";"LUG";"Beitrag";"T-Shirts";"Brauerei";"Schwimmabzeichen"'."\n";
				$ctr = 1;

				$events_SQL = "SELECT eventid,charge FROM event_event";
				$events_res = my_query($events_SQL);
				$events_array = array();
				while($events_row = mysql_fetch_assoc($events_res)) {
					$events_array[$events_row['eventid']] = $events_row['charge'];
				}

				$artikel_SQL = "SELECT artikelid,preis FROM event_artikel";
				$artikel_res = my_query($artikel_SQL);
				$artikel_array = array();
				while($artikel_row = mysql_fetch_assoc($artikel_res)) {
					$artikel_array[$artikel_row['artikelid']] = $artikel_row['preis'];
				}
				
			
				while($row = mysql_fetch_assoc($res)) {
					$pay_array = $this->paystatus($row['anmeldungid'],$row['accountid'],$events_array,$artikel_array);
						
					$crdate = date("d.m.Y G:i:s",$row['crdate']);
					$act = ($row['active'] ? 'aktiv': '- <a href="?p=remail&a='.$row['accountid'].'">Aktivierungsmail senden</a>');
					$ret .= $ctr.';"'.$row['vorname'].'";"'.$row['nachname'].'";"'.$row['username'].'";"'.$row['lugname'].'";"'.$paystatus.'"';
					$ret .= '"'..'";"'..'";"'..'"';
					$ret .= "\n";
					$ctr++;
				}
			}
			mysql_free_result($res);
		}
		
		return $ret;
	}

	function barcodeupdate() {
		$SQL = "UPDATE event_anmeldung SET barcode=CONCAT('C08',IF(CHAR_LENGTH(HEX(anmeldungid))=1,CONCAT('0',HEX(anmeldungid)),HEX(anmeldungid))) WHERE barcode IS NULL";
		my_query($SQL);
		$updates = my_affected_rows();
		if($updates!=0) {
			$this->barcodeupdates = $updates;	// Merken das ein Barcodeupdate gemacht wurde
		}
	} // function barcodeupdate
}


?>
