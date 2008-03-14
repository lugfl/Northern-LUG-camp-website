<?php

$PAGE['vegetarier']['name'] = "Vegetarier";
$PAGE['vegetarier']['navilevel'] = 1;
$PAGE['vegetarier']['login_required'] = 1;
$PAGE['vegetarier']['phpclass'] = 'HtmlPage_vegetarier';
$PAGE['vegetarier']['parent'] = 'root';

require_once('lib/inc.database.php');

class HtmlPage_vegetarier extends HtmlPage {

	var $name = "Vegetarier";
	var $navilevel = 1;
	var $login_required = 1;

	function HtmlPage_vegetarier() {
	}
	
	function getContent() {
    		$ret = '';
		$SQL = "SELECT a.accountid,a.username,a.acl,UNIX_TIMESTAMP(a.crdate) AS crdate,a.active, l.name AS lugname, ";
		$SQL .= " an.anmeldungid,an.vorname,an.nachname ";
		$SQL .= " FROM account a LEFT JOIN event_lug l ON a.lugid=l.lugid ";
		$SQL .= " LEFT JOIN event_anmeldung an ON a.accountid=an.accountid ";
		$SQL .= " WHERE an.vegetarier = 1";
		$res = my_query($SQL);
		
		if($res) {
			if(mysql_num_rows($res) >0) {
				$ret .= '
				<table>
					<tr>
						<th>LfdNr</th>
						<th><a href="?p=anmeldungsliste&s=username" alt="nach Benutzername sortieren">Benutzername</th>
						<th><a href="?p=anmeldungsliste&s=vorname" alt="nach Vorname sortieren">Vorname</th>
						<th><a href="?p=anmeldungsliste&s=nachname" alt="nach Nachname sortieren">Nachname</th>
						<th><a href="?p=anmeldungsliste&s=lugname" alt="nach LUG sortieren">LUG</a></th>
						<th><a href="?p=anmeldungsliste&s=crdate" alt="nach Anmeldedatum sortieren">Anmeldedatum</th>
						<th><a href="?p=anmeldungsliste&s=active" alt="nach Accountstatus sortieren">Accountstatus</th>
						<th>Bezahlstatus</th>
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

				while($row = mysql_fetch_assoc($res)) {
					$topay = 0;
					$payed = 0;
					
					$e_SQL = "SELECT eventid,bezahlt FROM event_anmeldung_event WHERE anmeldungid = '".$row['anmeldungid']."'";
					$e_res = my_query($e_SQL);
					while($e_row = mysql_fetch_assoc($e_res)) {
						$topay += $events_array[$e_row['eventid']];
						if($e_row['bezahlt'] != 0) {
							$payed += $events_array[$e_row['eventid']];
						}
					}
					
					$a_SQL = "SELECT artikelid,bezahlt FROM event_account_artikel WHERE accountid = '".$row['accountid']."'";
					$a_res = my_query($a_SQL);
					while($a_row = mysql_fetch_assoc($a_res)) {
						$topay += $artikel_array[$a_row['artikelid']];
						if($a_row['bezahlt'] != 0) {
							$payed += $artikel_array[$a_row['artikelid']];
						}
					}
					
					if($topay-$payed == 0) {
						$paystatus = '<div class="bezahlt">Alles bezahlt</div>';
					} else {
						$paystatus = '<div class="zuzahlen">'.($topay-$payed).'&euro; zu zahlen</div>';
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
			mysql_free_result($res);
		}
		
		return $ret;
	}

}


?>
