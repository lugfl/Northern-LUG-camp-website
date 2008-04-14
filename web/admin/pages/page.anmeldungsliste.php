<?php

$PAGE['anmeldungsliste']['name'] = "Anmeldungen";
$PAGE['anmeldungsliste']['navilevel'] = 1;
$PAGE['anmeldungsliste']['login_required'] = 1;
$PAGE['anmeldungsliste']['phpclass'] = 'HtmlPage_anmeldungsliste';
$PAGE['anmeldungsliste']['parent'] = 'root';

require_once('lib/inc.database.php');

class HtmlPage_anmeldungsliste extends HtmlPage {

	var $name = "Anmeldungen";
	var $navilevel = 1;
	var $login_required = 1;
	var $barcodeupdates = 0;
	function HtmlPage_anmeldungsliste() {
	}
	
	function paystatus($anmeldungid,$accountid,$events_array,$artikel_array) {
		$e_SQL = "SELECT eventid,bezahlt FROM event_anmeldung_event WHERE anmeldungid = '".$anmeldungid."'";
		$e_res = my_query($e_SQL);
		while($e_row = mysql_fetch_assoc($e_res)) {
			$topay += $events_array[$e_row['eventid']];
			if($e_row['bezahlt'] != 0) {
				$payed += $events_array[$e_row['eventid']];
			}
		}
						
		$a_SQL = "SELECT artikelid,anzahl,bezahlt FROM event_account_artikel WHERE accountid = '".$accountid."'";
		$a_res = my_query($a_SQL);
		while($a_row = mysql_fetch_assoc($a_res)) {
			$topay += $a_row['anzahl']*$artikel_array[$a_row['artikelid']];
			if($a_row['bezahlt'] != 0) {
				$payed +=  $a_row['anzahl']*$artikel_array[$a_row['artikelid']];
			}
		}
						
		return array('topay'=>$topay,'payed'=>$payed);
	}
	
	function getContent() {
		$this->barcodeupdate();
		$sort = http_get_var('s');
		if($sort == '') { $sort = "crdate"; }
    		$ret = '';
		$SQL = "SELECT a.accountid,a.username,a.acl,UNIX_TIMESTAMP(a.crdate) AS crdate,a.active, l.name AS lugname, ";
		$SQL .= " an.anmeldungid,an.vorname,an.nachname ";
		$SQL .= " FROM account a LEFT JOIN event_lug l ON a.lugid=l.lugid ";
		$SQL .= " LEFT JOIN event_anmeldung an ON a.accountid=an.accountid ";
		if($sort != 'pay') { $SQL .= " ORDER BY ".my_escape_string($sort); }
		$res = my_query($SQL);
		
		if($res) {
			if(mysql_num_rows($res) >0) {
				$ret .= '
				<table>
					<tr>
						<th>LfdNr</th>
						<th><a href="?p=anmeldungsliste&s=username" alt="nach Benutzername sortieren">Benutzername</a></th>
						<th><a href="?p=anmeldungsliste&s=vorname" alt="nach Vorname sortieren">Vorname</a></th>
						<th><a href="?p=anmeldungsliste&s=nachname" alt="nach Nachname sortieren">Nachname</a></th>
						<th><a href="?p=anmeldungsliste&s=lugname" alt="nach LUG sortieren">LUG</a></th>
						<th><a href="?p=anmeldungsliste&s=crdate" alt="nach Anmeldedatum sortieren">Anmeldedatum</a></th>
						<th><a href="?p=anmeldungsliste&s=active" alt="nach Accountstatus sortieren">Accountstatus</a></th>
						<th><a href="?p=anmeldungsliste&s=pay" alt="nach Bezahlstatus sortieren">Bezahlstatus</a></th>
					</tr>
				';
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
				
				if($sort == 'pay') {
					$anm_array = array();
					while($row = mysql_fetch_assoc($res)) {
						$pay_array = $this->paystatus($row['anmeldungid'],$row['accountid'],$events_array,$artikel_array);
						$anm_array[$row['anmeldungid']] = $pay_array['topay']-$pay_array['payed'];
					}
					arsort($anm_array);
					foreach(array_keys($anm_array) as $anmeldungid) {
						$SQL = "SELECT a.accountid,a.username,a.acl,UNIX_TIMESTAMP(a.crdate) AS crdate,a.active, l.name AS lugname, ";
						$SQL .= " an.anmeldungid,an.vorname,an.nachname ";
						$SQL .= " FROM account a LEFT JOIN event_lug l ON a.lugid=l.lugid ";
						$SQL .= " LEFT JOIN event_anmeldung an ON a.accountid=an.accountid ";
						$SQL .= " WHERE an.anmeldungid = $anmeldungid";
						$res2 = mysql_query($SQL);
						$anm_data = mysql_fetch_array($res2);
						
						if($anm_array[$anmeldungid] == 0) {
							$paystatus = '<div class="bezahlt">Alles bezahlt</div>';
						} else {
							$paystatus = '<div class="zuzahlen">'.$anm_array[$anmeldungid].' &euro; zu zahlen</div>';
						}
						
						$crdate = date("d.m.Y G:i:s",$anm_data['crdate']);
						$act = ($anm_data['active'] ? 'aktiv': '- <a href="?p=remail&a='.$anm_data['accountid'].'">Aktivierungsmail senden</a>');
						$ret .= '
						<tr>
							<td>'.$ctr.'</td>
							<td><a href="?p=account&accountid='.$anm_data['accountid'].'">'.$anm_data['username'].'</a></td>
							<td>'.$anm_data['vorname'].'</td>
							<td>'.$anm_data['nachname'].'</td>
							<td>'.$anm_data['lugname'].'</td>
							<td>'.$crdate.'</td>
							<td>'.$act.'</td>
							<td>'.$paystatus.'</td>
						</tr>
						';
						$ctr++;
					}
				} else {
					while($row = mysql_fetch_assoc($res)) {
						$pay_array = $this->paystatus($row['anmeldungid'],$row['accountid'],$events_array,$artikel_array);
						if($pay_array['topay']-$pay_array['payed'] == 0) {
							$paystatus = '<div class="bezahlt">Alles bezahlt</div>';
						} else {
							$paystatus = '<div class="zuzahlen">'.($pay_array['topay']-$pay_array['payed']).' &euro; zu zahlen</div>';
						}
						
						$crdate = date("d.m.Y G:i:s",$row['crdate']);
						$act = ($row['active'] ? 'aktiv': '- <a href="?p=remail&a='.$row['accountid'].'">Aktivierungsmail senden</a>');
						$ret .= '
						<tr>
							<td>'.$ctr.'</td>
							<td><a href="?p=account&accountid='.$row['accountid'].'">'.$row['username'].'</a></td>
							<td>'.$row['vorname'].'</td>
							<td>'.$row['nachname'].'</td>
							<td>'.$row['lugname'].'</td>
							<td>'.$crdate.'</td>
							<td>'.$act.'</td>
							<td>'.$paystatus.'</td>
						</tr>
						';
						$ctr++;
					}
					$ret .= '
					</table>
					';
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
