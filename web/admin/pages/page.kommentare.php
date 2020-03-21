<?php

$PAGE['kommentare']['name'] = "Kommentare";
$PAGE['kommentare']['navilevel'] = 1;
$PAGE['kommentare']['login_required'] = 1;
$PAGE['kommentare']['phpclass'] = 'HtmlPage_kommentare';
$PAGE['kommentare']['parent'] = 'root';

require_once('lib/inc.database.php');

class HtmlPage_kommentare extends HtmlPage {

	var $name = "Kommentare";
	var $navilevel = 1;
	var $login_required = 1;

	function __construct() {
	}
	
	function getContent() {
    		$ret = '';
		$SQL = "SELECT a.accountid,a.username,a.acl,UNIX_TIMESTAMP(a.crdate) AS crdate,a.active, ";
		$SQL .= " an.anmeldungid,an.vorname,an.nachname,an.bemerkung,an.admin_bemerkung ";
		$SQL .= " FROM event_anmeldung an LEFT JOIN account a ON an.accountid=a.accountid ";
		$SQL .= " WHERE an.bemerkung != '' OR an.admin_bemerkung != ''";
		$res = my_query($SQL);
		
		if($res) {
			if(mysqli_num_rows($res) >0) {
				$ret .= '
				<table>
					<tr>
						<th>LfdNr</th>
						<th><a href="?p=anmeldungsliste&s=username" alt="nach Benutzername sortieren">Benutzername</th>
						<th><a href="?p=anmeldungsliste&s=vorname" alt="nach Vorname sortieren">Vorname</th>
						<th><a href="?p=anmeldungsliste&s=nachname" alt="nach Nachname sortieren">Nachname</th>
						<th><a href="?p=anmeldungsliste&s=crdate" alt="nach Anmeldedatum sortieren">Anmeldedatum</th>
						<th><a href="?p=anmeldungsliste&s=active" alt="nach Accountstatus sortieren">Accountstatus</th>
						<th>Kommentar</th>
						<th>Admin Bemerkung</th>
					</tr>
				';
				$ctr = 1;

				while($row = mysqli_fetch_assoc($res)) {
					$crdate = date("d.m.Y G:i:s",$row['crdate']);
					$act = ($row['active'] ? 'aktiv': '- <a href="?p=remail&a='.$row['accountid'].'">Aktivierungsmail senden</a>');
					$ret .= '
					<tr>
						<td>'.$ctr.'</td>
						<td><a href="?p=account&accountid='.$row['accountid'].'">'.$row['username'].'</a></td>
						<td>'.$row['vorname'].'</td>
						<td>'.$row['nachname'].'</td>
						<td>'.$crdate.'</td>
						<td>'.$act.'</td>
						<td>'.$row['bemerkung'].'</td>
						<td>'.$row['admin_bemerkung'].'</td>
					</tr>
					';
					$ctr++;
				}
				$ret .= '
				</table>
				';
			}
			mysqli_free_result($res);
		}
		
		return $ret;
	}

}


?>
