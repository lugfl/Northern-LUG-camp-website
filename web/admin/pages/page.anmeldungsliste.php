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

	function HtmlPage_anmeldungsliste() {
	}
	
	function getContent() {
    		$ret = '';
		$SQL = "SELECT a.username,a.acl,UNIX_TIMESTAMP(a.crdate) AS crdate,a.active, l.name AS lugname, ";
		$SQL .= " an.vorname,an.nachname ";
		$SQL .= " FROM account a LEFT JOIN event_lug l ON a.lugid=l.lugid ";
		$SQL .= " LEFT JOIN event_anmeldung an ON a.accountid=an.accountid ";
		$SQL .= " ORDER BY crdate ";
		$res = my_query($SQL);
		
		if($res) {
			if(mysql_num_rows($res) >0) {
				$ret .= '
				<table>
					<tr>
						<th>Benutzername</th>
						<th>Vorname</th>
						<th>Nachname</th>
						<th>LUG</th>
						<th>Anmeldedatum</th>
						<th>Accountstatus</th>
					</tr>
				';
				while($row = mysql_fetch_assoc($res)) {
					$crdate = date("d.m.Y G:i:s",$row['crdate']);
					$act = ($row['active'] ? 'aktiv': '-');
					$ret .= '
					<tr>
						<td>'.$row['username'].'</td>
						<td>'.$row['vorname'].'</td>
						<td>'.$row['nachname'].'</td>
						<td>'.$row['lugname'].'</td>
						<td>'.$crdate.'</td>
						<td>'.$act.'</td>
					</tr>
					';
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
