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
		$array = array(
				'brauerei' => "nicht angemeldet",
				'abzeichen' => "",
				'urkunde' => null,
				'camp' => null,
				'shirts' => null,
			);
		$e_SQL = "SELECT eventid,bezahlt FROM event_anmeldung_event WHERE anmeldungid = '".$anmeldungid."'";
		$e_res = my_query($e_SQL);
		while($e_row = mysql_fetch_assoc($e_res)) {
			$topay = $payed = 0;
			if($e_row['eventid'] == 1) {
				$array['brauerei'] = "NICHT bezahlt";
			}
			if($e_row['eventid'] == 3) {
				$array['camp'] = "Nein";
			}
			if($e_row['eventid'] == 4) {
				$array['urkunde'] = "NICHT bezahlt";
			}
			if($e_row['eventid'] == 5 || $e_row['eventid'] == 6 || $e_row['eventid'] == 7 || $e_row['eventid'] == 8) {
				$array['abzeichen'] .= $events_array[$e_row['eventid']]['name'].", ";
			}
			$array['events'][$e_row['eventid']] = $e_row['bezahlt'];
			$topay += $events_array[$e_row['eventid']]['charge'];
			if($e_row['bezahlt'] != 0) {
				if($e_row['eventid'] == 1) {
					$array['brauerei'] = "bezahlt";
				}
				if($e_row['eventid'] == 3) {
					$array['camp'] = "Ja";
				}
				if($e_row['eventid'] == 4) {
					$array['urkunde'] = "bezahlt";
				}
				$payed += $events_array[$e_row['eventid']]['charge'];
			}
		}
		
		if($array['urkunde']) { $array['abzeichen'] .= ' ('.$array['urkunde'].')'; }
						
		$a_SQL = "SELECT artikelid,anzahl,bezahlt,groesse,crdate FROM event_account_artikel WHERE accountid = '".$accountid."'";
		$a_res = my_query($a_SQL);
		while($a_row = mysql_fetch_assoc($a_res)) {
			$array['shirts'] .= $a_row['anzahl']."x ".$artikel_array[$a_row['artikelid']]['name']." ".$a_row['groesse'];
			$array['artikel'][$a_row['artikelid']] = $a_row['bezahlt'];
			$topay += $a_row['anzahl']*$artikel_array[$a_row['artikelid']]['charge'];
			if($a_row['bezahlt'] != 0) {
				$payed +=  $a_row['anzahl']*$artikel_array[$a_row['artikelid']]['charge'];
				$array['shirts'] .= ' (bezahlt)';
			} else {
				$array['shirts'] .= ' (NICHT bezahlt)';
			}
			if ($a_row['artikelid'] > 3 && $a_row['artikelid'] < 7) {
				$array['shirts'] .= ' ('.$a_row['crdate'].')';
			}
			$array['shirts'] .= ", ";
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
    if( isset($CURRENT_EVENT_ID) && $CURRENT_EVENT_ID != 0) {
			$SQL .= ' WHERE an.eventid=' . $CURRENT_EVENT_ID . ' ';
		}
		$SQL .= " ORDER BY vorname"; 
		$res = my_query($SQL);
		
		if($res) {
			if(mysql_num_rows($res) >0) {
				$csv = '"Nr";"Vorname";"Name";"Nickname";"LUG";"Beitrag";"T-Shirts";"Brauerei";"Schwimmabzeichen"'."\n";
				$ctr = 1;

				$events_SQL = "SELECT eventid,name,charge FROM event_event";
				$events_res = my_query($events_SQL);
				$events_array = array();
				while($events_row = mysql_fetch_assoc($events_res)) {
					$events_array[$events_row['eventid']]['charge'] = $events_row['charge'];
					$events_array[$events_row['eventid']]['name'] = $events_row['name'];
				}

				$artikel_SQL = "SELECT artikelid,name,preis FROM event_artikel";
				$artikel_res = my_query($artikel_SQL);
				$artikel_array = array();
				while($artikel_row = mysql_fetch_assoc($artikel_res)) {
					$artikel_array[$artikel_row['artikelid']]['charge'] = $artikel_row['preis'];
					$artikel_array[$artikel_row['artikelid']]['name'] = $artikel_row['name'];
				}
			
				while($row = mysql_fetch_assoc($res)) {
					$pay_array = $this->paystatus($row['anmeldungid'],$row['accountid'],$events_array,$artikel_array);
					$paystatus = $pay_array['camp'];
					$crdate = date("d.m.Y G:i:s",$row['crdate']);
					$csv .= $ctr.';"'.$row['vorname'].'";"'.$row['nachname'].'";"'.$row['username'].'";"'.$row['lugname'].'";"'.$paystatus.'";';
					$csv .= '"'.$pay_array['shirts'].'";"'.$pay_array['brauerei'].'";"'.$pay_array['abzeichen'].'"';
					$csv .= "\n";
					$ctr++;
				}
			}
			mysql_free_result($res);
		}
		$handle = fopen("tmp/anmeldungen.csv","w");
		fwrite($handle, $csv);
		fclose($handle);
		$ret .= '<a href="tmp/anmeldungen.csv">klick</a>';
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
