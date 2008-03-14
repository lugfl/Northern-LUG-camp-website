<?php

$PAGE['eventanmeldungen']['name'] = "Event-Anmeldungen";
$PAGE['eventanmeldungen']['navilevel'] = 1;
$PAGE['eventanmeldungen']['login_required'] = 1;
$PAGE['eventanmeldungen']['phpclass'] = 'HtmlPage_eventanmeldungen';
$PAGE['eventanmeldungen']['parent'] = 'root';

require_once('lib/inc.database.php');

class HtmlPage_eventanmeldungen extends HtmlPage {

	var $name = "Event-Anmeldungen";
	var $navilevel = 1;
	var $login_required = 1;

	function HtmlPage_eventanmeldungen() {
	}
	
	function getContent() {
    		$ret = '';
		
		$s_event = http_get_var('event');
		if($s_event == '') { $s_event = 1; }	
		$sort = http_get_var('s');
		if($sort == '') { $sort = "username"; }	
		
		$e_SQL = "SELECT eventid,name FROM event_event";
		$e_res = my_query($e_SQL);
		
		$ret .= 'Nur Anmeldungen für folgendes Event: <form method="post"><select name="event">';
		while($e_row = mysql_fetch_assoc($e_res)) {
			if($s_event == $e_row['eventid']) {
				$ret .= '<option value="'.$e_row['eventid'].'" selected="selected">'.$e_row['name'].'</option>';
			} else {
				$ret .= '<option value="'.$e_row['eventid'].'">'.$e_row['name'].'</option>';
			}
		}
		$ret .= '</select> <input type="submit" value=" OK "></form>';
		


		$SQL = "SELECT e.anmeldungid, e.eventid, ea.vorname, ea.nachname, ea.lugid, a.accountid, a.username, l.name AS lugname FROM event_anmeldung_event e";
		$SQL .= " LEFT JOIN event_anmeldung ea ON e.anmeldungid = ea.anmeldungid ";
		$SQL .= " LEFT JOIN account a ON ea.accountid = a.accountid ";
		$SQL .= " LEFT JOIN event_lug l ON a.lugid=l.lugid ";
		$SQL .= " WHERE e.eventid = ".$s_event;
		$SQL .= " ORDER BY ".my_escape_string($sort);
		
		$res = my_query($SQL);
		
		if($res) {
			if(mysql_num_rows($res) >0) {
				$ret .= '
				<table>
					<tr>
						<th>LfdNr</th>
						<th><a href="?p=eventanmeldungen&s=username" alt="nach Benutzername sortieren">Benutzername</th>
						<th><a href="?p=eventanmeldungen&s=vorname" alt="nach Vorname sortieren">Vorname</th>
						<th><a href="?p=eventanmeldungen&s=nachname" alt="nach Nachname sortieren">Nachname</th>
						<th><a href="?p=eventanmeldungen&s=lugname" alt="nach LUG sortieren">LUG</a></th>
					</tr>
				';
				$ctr = 1;

				while($row = mysql_fetch_assoc($res)) {		
					$ret .= '
					<tr>
						<td>'.$ctr.'</td>
						<td><a href="?p=account&accountid='.$row['accountid'].'">'.$row['username'].'</a></td>
						<td>'.$row['vorname'].'</td>
						<td>'.$row['nachname'].'</td>
						<td>'.$row['lugname'].'</td>
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
