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
		global $EVENT_SCHWIMMEN;

		// Checken, ob die Seite wegen Wartungsarbeiten ausgeschaltet werden soll.
		// Funktion checkMaintenance() kommt aus class.HtmlPage.php
		$ret = $this->checkMaintenance();
		if($ret!='')
			return $ret;


		// Pruefen ob etwas geloescht werden soll
		$a = http_get_var('a');
		$accountartikelid = http_get_var('accountartikelid');
		$eventid = http_get_var('eventid');

		if($a=='d' && is_numeric($accountartikelid)) {
			$SQLdel = "DELETE FROM event_account_artikel WHERE accountartikelid=".$accountartikelid." AND ";
			$SQLdel .= " accountid=".$_SESSION['_accountid'];
			my_query($SQLdel);
		}else if($a=='d' && is_numeric($eventid)) {
			$anmeldungid = http_get_var('anmeldungid');
			if(is_numeric($anmeldungid)) {
				if($eventid == $EVENT_SCHWIMMEN['schwimmhalle_event_id']) {
					// Wenn die Anmeldung zum Schwimmen im Hallenbad geloscht werden soll,
					// muessen gleichzeitig auch die pruefungsanmeldungen raus.
					$sa = $EVENT_SCHWIMMEN['abzeichen_event_id'];
					$SQL = "SELECT eventid FROM event_event WHERE eventid=".$sa." OR parent=".$sa;

					$res = my_query($SQL);
					$sch = Array();
					while($row = mysql_fetch_assoc($res)) {
						array_push($sch,$row['eventid']);
					}
					mysql_free_result($res);
					if(count($sch)>0) {
						$SQL = "DELETE FROM event_anmeldung_event WHERE eventid IN (".join($sch,",").")";

						my_query($SQL);
					}
				}
				$SQLdel = "DELETE FROM event_anmeldung_event WHERE eventid=".$eventid." AND anmeldungid=".$anmeldungid;
				my_query($SQLdel);
			}
		}

		// Feststellen fuer welches Event wir grade anmelden
		$ceventid = 0;
		if(isset($CURRENT_EVENT_ID) && is_numeric($CURRENT_EVENT_ID))
			$ceventid = $CURRENT_EVENT_ID;

   		$ret = '
				<h1>Kosten</h1>
				<p>Auf dieser Seite kannst Du sehen, welche Anmeldungen und Eink&auml;ufe Du get&auml;tigt hast und ob diese bereits bezahlt sind.</p>
			';

		$zuzahlen = 0;
		$barzuzahlen = 0;
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
					$SQL2 = "SELECT e.*,UNIX_TIMESTAMP(ae.bezahlt) AS bezahlt ";
					$SQL2 .= " ,IF(e.buchanfang<NOW() AND e.buchende>NOW() AND parent IS NOT NULL,1,0) AS editable ";
					$SQL2 .= " FROM event_event e ";
					$SQL2 .= " LEFT JOIN event_anmeldung_event ae ON e.eventid=ae.eventid ";
					$SQL2 .= " WHERE ae.anmeldungid=".$anmeldungid;
					$SQL2 .= " AND ( e.parent=".$ceventid." OR e.eventid=".$ceventid.")";
					$SQL2 .= " ORDER BY e.sort";
					$res2 = my_query($SQL2);
					if($res2) {
						if(mysql_num_rows($res2)>0) {
							$ret .= '<table class="datatable1">
							<caption><h3>Anmeldungen</h3></caption>
							<thead>
								<tr>
									<th>Veranstaltung</th>
									<th>Preis</th>
									<th>Bezahlstatus</th>
									<th>Aktion</th>
								</tr>
							</thead>
							<tbody>
							';
							$barzahlhinweis=0;
							while($row2 = mysql_fetch_assoc($res2)) {
								$bezahlstatus = "-";
								if(isset($row2['bezahlt']) && $row2['bezahlt'] != 0) {
									$bezahlstatus = "ja";
								}
								if($row2['hidden'] == 1) {
									$row2['editable'] = 0;
								}

								$cmd = '';
								if($bezahlstatus=='-' && $row2['editable']) {
									$cmd = '<a href="?p=rechnung&anmeldungid='.$anmeldungid.'&eventid='.$row2['eventid'].'&a=d">abmelden</a>';
								}

								if($bezahlstatus == "-") {
									// wenn noch nicht bezahlt, dann zum gesamtbetrag zurechnen
									if($row2['barzahlung']==0) {
										$zuzahlen += $row2['charge'];
									}
								}
								$betrag = number_format($row2['charge'],2,',','.') . " &euro;";
								if($row2['barzahlung']==1) {
									$barzahlhinweis = 1;
									$betrag = "*";
								}
								$ret .= '
									<tr>
										<td>'.$row2['name'].'</td>
										<td style="text-align:center;">'.$betrag.'</td>
										<td style="text-align:center;">'.$bezahlstatus.'</td>
										<td class="aktion">'.$cmd.'</td>
									</tr>
								';
							} // while
							$ret .= '</tbody></table>';
							if($barzahlhinweis) {
								$ret .= '
								<p><b>*</b> f&uuml;r die LPI-Pr&uuml;fungen mu&szlig;t Du dich noch auf <a href="http://lpievent.lpi-german.de/">lpievent.lpi-german.de</a> anmelden. Die Pr&uuml;fungsgeb&uuml;hren m&uuml;ssen direkt beim Pr&uuml;fer bezahlt werden. &Uuml;ber die H&ouml;he der Geb&uuml;hren kannst Du Dich auf unserer <a href="?p=prog_lpi">LPI-Seite</a> informieren.</p>
								';
							}
						} // if num_rows
						mysql_free_result($res2);
					} // if res2

				} // while personen
				mysql_free_result($res1);
			} // if res1
			$ret .= '<br/>';

			// Auflisten, welche Artikel gekauft wurden.
			$SQL2 = "SELECT aa.accountartikelid,a.*,aa.groesse,aa.anzahl,(aa.anzahl*a.preis) AS gesamtpreis ";
			$SQL2 .= " ,UNIX_TIMESTAMP(aa.bezahlt) AS bezahlt ";
			$SQL2 .= " ,IF(a.kaufab<NOW() AND a.kaufbis>NOW(),1,0) AS editable ";
			$SQL2 .= " FROM event_account_artikel aa ";
			$SQL2 .= " LEFT JOIN event_artikel a ON aa.artikelid=a.artikelid ";
			$SQL2 .= " WHERE aa.accountid=".$_SESSION['_accountid'];
			$res2 = my_query($SQL2);
			if($res2) {
				if(mysql_num_rows($res2)>0) {
					$ret .= '
						<table class="datatable1">
						<caption><h3>Bestellungen</h3></caption>
							<thead>
								<tr>
									<th>Artikel</th>
									<th>Gr&ouml;sse</th>
									<th>Anzahl</th>
									<th>Einzelpreis</th>
									<th>Gesamtpreis</th>
									<th>Bezahlstatus</th>
									<th>Aktion</th>
								</tr>
							</thead>
							<tbody>
					';
					while($row2 = mysql_fetch_assoc($res2)) {
						$bezahlstatus='-';
						if(isset($row2['bezahlt']) && $row2['bezahlt'] != 0) {
							$bezahlstatus = "ja";
						}
						$groesse = '-';
						if(isset($row2['groesse']) && $row2['groesse']!='')
							$groesse = $row2['groesse'];

						$cmd = '';
						if($bezahlstatus=='-' && $row2['editable']) {
							$cmd = '<a href="?p=rechnung&accountartikelid='.$row2['accountartikelid'].'&a=d">l&ouml;schen</a>';
						}

						if($bezahlstatus == "-") {
							// wenn noch nicht bezahlt, dann zum gesamtbetrag zurechnen
							$zuzahlen += $row2['gesamtpreis'];
						}
						$ret .= '
							<tr>
								<td>'.$row2['name'].'</td>
								<td style="text-align:center;">'.$groesse.'</td>
								<td style="text-align:center;">'.$row2['anzahl'].'</td>
								<td style="text-align:right;">'.number_format($row2['preis'],2,',','.').' &euro;</td>
								<td style="text-align:right;">'.number_format($row2['gesamtpreis'],2,',','.').' &euro;</td>
								<td style="text-align:center;">'.$bezahlstatus.'</td>
								<td class="aktion">'.$cmd.'</td>
							</tr>
						';
					}
					$ret .= '
						</tbody>
						</table>
					';
				} // if num_rows res2
				mysql_free_result($res2);
			} // if res2 (Auflistung der Artikel)

			$ret .= '
			<p>
				Der Gesamtbetrag, den Du inkl. Deiner Anmeldung und evtl. Zusatzbestellungen zu zahlen hast betr&auml;gt <b>'.number_format($zuzahlen,2,',','.').' &euro;</b>. Damit die Anmeldung g&uuml;ltig ist, muss die <b>&Uuml;berweisung bis zum 23.04.08</b> bei uns eingegangen sein.
			</p>
			';
			// hab das mal mit in die if-clause genommen, ist ja unsinnig, dass das ausgegeben wird wenn keine accountid gesetzt ist - stefan
			$SQL3 = "SELECT username FROM account WHERE accountid=".$_SESSION['_accountid'];
			$res3 = my_query($SQL3);
			if($res3) {
				if(mysql_num_rows($res3)>0) {
					while($row3 = mysql_fetch_assoc($res3)) {
						$nickname = $row3['username'];
						$ret .= '
						<h1>Bankverbindung</h1>
						<p>
						Bitte &uuml;berweise den noch ausstehenden Betrag auf das folgende Konto:
						</p>
						<p>
							<address>
							Kontoinhaber: LUG Flensburg e.V.<br/>
							Bank: Union Bank AG<br/>
							BLZ: 215 201 00<br/>
							Kto: 16632<br/>
							</address>
							Verwendungszweck: LC 2008 '.$nickname.'
						</p>
						<p>
							F&uuml;r Sammel&uuml;berweisungen einer LUG oder aus dem Ausland setzt euch bitte mit unserem Kassenwart in Verbindung, den ihr per Mail an
							<a href="mailto:kasse@lug-camp-2008.de">kasse@lug-camp-2008.de</a> erreicht.
						</p>
						';
					}
				}
				mysql_free_result($res3);
			}
		} // if is_numeric accountid

		return $ret;
	}

}


?>
