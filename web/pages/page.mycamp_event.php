<?php

$PAGE['mycamp_event']['name'] = "Veranstaltungen";
$PAGE['mycamp_event']['navilevel'] = 2;
$PAGE['mycamp_event']['login_required'] = 1;
$PAGE['mycamp_event']['phpclass'] = 'HtmlPage_mycamp_event';
$PAGE['mycamp_event']['parent'] = 'mycamp';

class HtmlPage_mycamp_event extends HtmlPage {


	function getContent() {
		global $CURRENT_EVENT_ID;
		global $_SESSION;

		// Checken, ob die Seite wegen Wartungsarbeiten ausgeschaltet werden soll.
		// Funktion checkMaintenance() kommt aus class.HtmlPage.php
		$ret = $this->checkMaintenance();
		if($ret!='')
			return $ret;

		my_connect();

		$a = http_get_var('a');
		if($a == 'a') {
			// eine Veranstaltung wurde hinzugebucht
			$anmeldungid = http_get_var('anmeldungid');
			$eventid = http_get_var('eventid');
			if(is_numeric($anmeldungid) && is_numeric($eventid)) {
				$SQLins = 'INSERT INTO event_anmeldung_event SET anmeldungid='.$anmeldungid.',eventid='.$eventid;
				my_query($SQLins);
			}
		}
		// Feststellen fuer welches Event wir grade anmelden
		if(!isset($CURRENT_EVENT_ID) || !is_numeric($CURRENT_EVENT_ID))
			$ceventid = 0;
		else
			$ceventid = $CURRENT_EVENT_ID;

    $ret = '
		<h1>Veranstaltungen</h1>
		';

		$SQL1 = "SELECT a.anmeldungid,a.vorname,a.nachname FROM event_anmeldung a LEFT JOIN event_anmeldung_event ae ON a.anmeldungid=ae.anmeldungid ";
		$SQL1 .= " WHERE a.accountid=".$_SESSION['_accountid']." AND ae.eventid=".$ceventid;
		$res1 = my_query($SQL1);
		if($res1) {
			if(mysql_num_rows($res1)>0) {
				while($row1 = mysql_fetch_assoc($res1)) {
				$ret .= '
					<h2>
						Anmeldungen f&uuml;r '.$row1['vorname'].' '.$row1['nachname'].'
					</h2>
					<table class="datatable1">
							<caption>
								<h3>F&uuml;r die folgenden Veranstaltungen kannst Du Dich noch anmelden:</h3>
							</caption>
						<tr>
							<th>Veranstaltung</th>
							<th>Zeitraum</th>
							<th>Preis</th>
							<th>Aktion</th>
						</tr>
					';
					$SQL2 = "SELECT e.eventid ";
					$SQL2 .= " FROM event_anmeldung_event ae LEFT JOIN event_event e ON ae.eventid=e.eventid ";
					$SQL2 .= " WHERE ae.anmeldungid=".$row1['anmeldungid'].' AND e.parent='.$ceventid;
					$res2 = my_query($SQL2);
					$gebuchte = Array();
					if($res2) {
						while($row2 = mysql_fetch_assoc($res2)) {
							array_push($gebuchte,$row2['eventid']);
						} // while fetch_assoc res2
						mysql_free_result($res2);
					} // if res2

					$SQL3 = "SELECT e.eventid,e.name,UNIX_TIMESTAMP(e.anfang) AS anfang, UNIX_TIMESTAMP(e.ende) AS ende,e.charge,e.quota ";
					$SQL3 .= " , COUNT(ae.anmeldungid) AS teilnehmerzahl ";
					$SQL3 .= " FROM event_event e ";
					$SQL3 .= " LEFT JOIN event_anmeldung_event ae ON e.eventid=ae.eventid ";
					$SQL3 .= " WHERE e.parent=".$ceventid;
					$SQL3 .= " AND e.hidden=0 ";
					$SQL3 .= " AND e.buchanfang<NOW() AND e.buchende>NOW() ";
					if(count($gebuchte)>0) 
						$SQL3 .= " AND e.eventid NOT IN (".join($gebuchte,',').")";
					$SQL3 .= " GROUP BY e.eventid ";
					$SQL3 .= " HAVING (e.quota-teilnehmerzahl)>0 ";
					$res3 = my_query($SQL3);
					if($res3) {
						if(mysql_num_rows($res3)>0) {
							while($row3 = mysql_fetch_assoc($res3)) {
								$ret .= '
									<tr>
										<td>'.$row3['name'].'</td>
										<td>'.date("d.m.y H:i",$row3['anfang']).' - '.date("d.m.y H:i",$row3['ende']).'</td>
										<td>'.number_format($row3['charge'],2,',','.').' &euro;</td>
										<td><a href="?p=mycamp_event&a=a&anmeldungid='.$row1['anmeldungid'].'&eventid='.$row3['eventid'].'">anmelden</a></td>
									</tr>
								';
							} // while fetch_assoc res3
						}else{
						$ret .= '
							<tr>
								<td colspan="4">Du bist bereits f&uuml;r alle Veranstaltungen angemeldet.</td>
							</tr>
						';
						} // if num_rows res3
					}else{ // if res3
						// Fehler
						print "FEHLER";
						print mysql_error();
					}	
					$ret .= '
					</table>
					';
				} // while Anmeldungen
				$ret .= '
					<p>
					Die Veranstaltungen, f&uuml;r die Du Dich bereits angemeldet hast, kannst Du auf der Seite <a href="?p=rechnung">Rechnung</a> einsehen.
					</p>
				';
			} // if mysql_num_rows res1
			mysql_free_result($res1);

		} // if res1

		return $ret;
	}

}


?>
