<?php

$PAGE['rechnung']['name'] = "Rechnung";
$PAGE['rechnung']['navilevel'] = 2;
$PAGE['rechnung']['login_required'] = 1;
$PAGE['rechnung']['phpclass'] = 'HtmlPage_rechnung';
$PAGE['rechnung']['parent'] = 'mycamp';

class HtmlPage_rechnung extends HtmlPage {

	function getContent() {
		global $_SESSION;
		global $CURRENT_EVENT_ID;

		// Feststellen fuer welches Event wir grade anmelden
		$ceventid = 0;
		if(isset($CURRENT_EVENT_ID) && is_numeric($CURRENT_EVENT_ID))
			$ceventid = $CURRENT_EVENT_ID;

   		$ret = '
				<h1>Kosten</h1>
				<p>Auf dieser Seite kannst Du sehen, welche Anmeldunge und Eink&auml;ufe Du get&auml;tigt hast und ob diese bereits Bezahlt sind.</p>
			';
		if(is_numeric($_SESSION['_accountid'])) {
			// Abfragen welche Personen angemeldet sind
			$SQL1 = "SELECT a.* ";
			$SQL1 .= " FROM event_anmeldung a ";
			$SQL1 .= " LEFT JOIN event_anmeldung_event ae ON a.anmeldungid=ae.anmeldungid ";
			$SQL1 .= " WHERE a.accountid=".$_SESSION['_accountid']." AND ae.eventid=".$ceventid;
			$res1 = my_query($SQL1);
			if($res1) {
				while($row1 = mysql_fetch_assoc($res1) ) { // alle Personen durchgehen
					$anmeldungid = $row1['anmeldungid'];

					$ret .= '<h2>'.$row1['vorname']." " . $row1['nachname'].'</h2>';


					// Auflisten, welche Events alle gebucht wurden
					$SQL2 = "SELECT e.* FROM event_event e ";
					$SQL2 .= " LEFT JOIN event_anmeldung_event ae ON e.eventid=ae.eventid ";
					$SQL2 .= " WHERE ae.anmeldungid=".$anmeldungid;
					$res2 = my_query($SQL2);
					if($res2) {
						if(mysql_num_rows($res2)>0) {
							$ret .= '<table class="datatable1">
							<caption>Anmeldungen</caption>
							';

							while($row2 = mysql_fetch_assoc($res2)) {
								$bezahlstatus = "?";
								$ret .= '
									<tr>
										<td>'.$row2['name'].'</td>
										<td>'.$row2['charge'].'</td>
										<td>'.$bezahlstatus.'</td>
									</tr>
								';
							} // while
							$ret .= '</table>';
						} // if num_rows
						mysql_free_result($res2);
					} // if res2

				} // while personen
				mysql_free_result($res1);
			} // if res1


			// Auflisten, welche Artikel gekauft wurden.
			$SQL2 = "SELECT a.* FROM event_account_artikel aa ";
			$SQL2 .= " LEFT JOIN event_artikel a ON aa.artikelid=a.artikelid ";
			$SQL2 .= " WHERE aa.accountid=".$_SESSION['_accountid'];
			$res2 = my_query($SQL2);
			if($res2) {
				if(mysql_num_rows($res2)>0) {
					$ret .= '
						<table class="datatable1">
						<caption>Bestellungen</caption>
					';
					while($row2 = mysql_fetch_assoc($res2)) {
						$bezahlstatus='?';
						$ret .= '
							<tr>
								<td>'.$row2['name'].'</td>
								<td>'.$row2['preis'].'</td>
								<td>'.$bezahlstatus.'</td>
							</tr>
						';
					}
					$ret .= '
						</table>
					';
				} // if num_rows res2
				mysql_free_result($res2);
			} // if res2

		} // if is_numeric accountid
		return $ret;
	}

}


?>
