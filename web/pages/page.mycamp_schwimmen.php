<?php

$PAGE['mycamp_schwimmen']['name'] = "Schwimmabzeichen";
$PAGE['mycamp_schwimmen']['navilevel'] = 2;
$PAGE['mycamp_schwimmen']['login_required'] = 1;
$PAGE['mycamp_schwimmen']['phpclass'] = 'HtmlPage_mycamp_schwimmen';
$PAGE['mycamp_schwimmen']['parent'] = 'mycamp';

class HtmlPage_mycamp_schwimmen extends HtmlPage {

	var $tuxe = Array(); // Liste der Abzeichen


	function HtmlPage_mycamp_schwimmen() {
		global $EVENT_SCHWIMMEN;

		if(!isset($EVENT_SCHWIMMEN['abzeichen_event_id']) || !is_numeric($EVENT_SCHWIMMEN['abzeichen_event_id'])) {
			$EVENT_SCHWIMMEN['abzeichen_event_id'] = 0;
		}
		if(!isset($EVENT_SCHWIMMEN['schwimmhalle_event_id']) || !is_numeric($EVENT_SCHWIMMEN['schwimmhalle_event_id'])) {
			$EVENT_SCHWIMMEN['schwimmhalle_event_id'] = 0;
		}

		my_connect();
	}

	function checkAbzeichenAnmeldung($anmeldungid) {
		global $EVENT_SCHWIMMEN;

		$ret = 0;

		if(is_numeric($anmeldungid)) {
			$SQL = "SELECT COUNT(*) AS ctr FROM event_anmeldung_event WHERE eventid=".$EVENT_SCHWIMMEN['abzeichen_event_id']." AND anmeldungid=" . $anmeldungid;
			$res = my_query($SQL);
			$row = mysql_fetch_assoc($res);
			$ret = $row['ctr'];
			mysql_free_result($res);
		}
		return $ret;
	}

	function checkSchwimmhalleAnmeldung($anmeldungid) {
		global $EVENT_SCHWIMMEN;

		$ret = 0;

		if(is_numeric($anmeldungid)) {
			$SQL = "SELECT COUNT(*) AS ctr FROM event_anmeldung_event WHERE eventid=".$EVENT_SCHWIMMEN['schwimmhalle_event_id']." AND anmeldungid=" . $anmeldungid;
			$res = my_query($SQL);
			$row = mysql_fetch_assoc($res);
			$ret = $row['ctr'];
			mysql_free_result($res);
		}
		return $ret;
	}

	function getAnmeldungen($anmeldungid) {
		global $EVENT_SCHWIMMEN;

		$ret = Array();

		if(is_numeric($anmeldungid)) {
			$SQL = "SELECT e.eventid FROM event_event e LEFT JOIN event_anmeldung_event ae ON e.eventid=ae.eventid ";
			$SQL .= " WHERE e.parent=".$EVENT_SCHWIMMEN['abzeichen_event_id']." AND ae.anmeldungid=" . $anmeldungid;
			$res = my_query($SQL);
			while($row = mysql_fetch_assoc($res)) {
				array_push($ret,$row['eventid']);
			}
			mysql_free_result($res);
		}
		return $ret;
		
	}

	function getTuxEvents() {
		global $EVENT_SCHWIMMEN;

		$ret = Array();

		$SQL = "SELECT e.*,COUNT(ae.anmeldungid) AS teilnehmerzahl ";
		$SQL .= " ,IF(e.buchanfang<NOW() AND e.buchende>NOW() AND parent IS NOT NULL,1,0) AS editable ";
		$SQL .= " FROM event_event e LEFT JOIN event_anmeldung_event ae ON e.eventid=ae.eventid ";
		$SQL .= " WHERE e.parent=".$EVENT_SCHWIMMEN['abzeichen_event_id'];
		$SQL .= " GROUP BY e.eventid ";
		$SQL .= " ORDER BY e.sort";
		$res = my_query($SQL);
		while($row = mysql_fetch_assoc($res)) {
			if($row['teilnehmerzahl'] <= $row['quota']) {
				$row['buchungmoeglich'] = 1;
			}else{
				$row['buchungmoeglich'] = 0;
			}
			array_push($ret,$row);
		}
		mysql_free_result($res);
		return $ret;
	}

	function doActions() {
		global $EVENT_SCHWIMMEN;
		// Anmeldung zu einem Event
		$a = http_get_var('a');
		switch($a) {
			case 'a':
				// eine Veranstaltung wurde hinzugebucht
				$anmeldungid = http_get_var('anmeldungid');
				$eventid = http_get_var('eventid');
				if(is_numeric($anmeldungid) && is_numeric($eventid)) {
					$anm = $this->getAnmeldungen($anmeldungid);
					$SQLins = 'INSERT INTO event_anmeldung_event SET anmeldungid='.$anmeldungid.',eventid='.$eventid;
					my_query($SQLins);
					$rc = my_affected_rows();
					if($rc==1 && count($anm)==0) {
						// Beim ersten Schwimmabzeichen muss auch der Ausweis bestellt werden
						$SQLins = 'INSERT INTO event_anmeldung_event SET anmeldungid='.$anmeldungid.',eventid='.$EVENT_SCHWIMMEN['abzeichen_event_id'];
						my_query($SQLins);

					}
				}
				break;
			case 'd':
				// eine Veranstaltung wurde abbestellt
				$anmeldungid = http_get_var('anmeldungid');
				$eventid = http_get_var('eventid');
				if(is_numeric($anmeldungid) && is_numeric($eventid)) {
					// beim letzten Event soll auch der Schwimmausweis gestrichen werden
					$anm = $this->getAnmeldungen($anmeldungid);

					$SQLdel = 'DELETE FROM event_anmeldung_event WHERE anmeldungid='.$anmeldungid.' AND eventid='.$eventid;
					my_query($SQLdel);
					$rc = my_affected_rows();
					$rest = count($anm) - $rc;
					if($rest == 0) {
						// Ausstellung des Abzeichens loeschen
						$SQLdel = 'DELETE FROM event_anmeldung_event WHERE anmeldungid='.$anmeldungid.' AND eventid='.$EVENT_SCHWIMMEN['abzeichen_event_id'];
						my_query($SQLdel);

					}
					

				}
				break;
			default:
				break;
		}
	}

	function getEventListContent() {
		global $CURRENT_EVENT_ID;
		global $EVENT_SCHWIMMEN;
		global $_SESSION;


		// Feststellen fuer welches Event wir grade anmelden
		if(!isset($CURRENT_EVENT_ID) || !is_numeric($CURRENT_EVENT_ID))
			$ceventid = 0;
		else
			$ceventid = $CURRENT_EVENT_ID;

    $ret = '
		<h1>Schwimmabzeichen</h1>
		<p>Auf dem Camp in diesem Jahr wird es erstmalig m&ouml;glich sein, Schwimmpr&uuml;fungen der Tux-Serie abzulegen. Auf dieser Seite kannst Du Dich f&uuml;r die Abnahme der Schwimmabzeichen anmelden. Das Ausstellen eines Schwimmabzeichenausweises kostet einmalig 2&euro;. Dabei ist es unwichtig wie viele Pr&uuml;fungen Du ablegst.</p>

		<p>
		Beachte bitte beim &quot;Toten Tux&quot;, dass die Zeit im Schwimmbad nur f&uuml;r diese Pr&uuml;fung ausreichen wird. Andere Pr&uuml;fungen sind dann nicht mehr m&ouml;glich.
		</p>
		';

		$SQL1 = "SELECT a.anmeldungid,a.vorname,a.nachname FROM event_anmeldung a LEFT JOIN event_anmeldung_event ae ON a.anmeldungid=ae.anmeldungid ";
		$SQL1 .= " WHERE a.accountid=".$_SESSION['_accountid']." AND ae.eventid=".$ceventid;
		$res1 = my_query($SQL1);
		if($res1) {
			if(mysql_num_rows($res1)>0) {
				while($anmeldung = mysql_fetch_assoc($res1)) {
				$ret .= '
					<h2>
						Schwimmpr&uuml;fungen f&uuml;r '.$anmeldung['vorname'].' '.$anmeldung['nachname'].'
					</h2>
					';
					$ret .= '<br/>';
					if(count($this->tuxe)>0) {
						$cur_anmeldungen = $this->getAnmeldungen($anmeldung['anmeldungid']);
						$ret .= '
						<table class="datatable1">
						<caption><h3>Schwimmabzeichen</h3></caption>
						<tr>
							<th>Abzeichen</th>
							<th>Beschreibung</th>
							<th>freie Pl&auml;tze</th>
							<th>Aktion</th>
						</tr>
						';
						// Erstmal pruefen, 
						$onlythisingroup_gebucht = 0;
						$notalwaysallowedinthisgroup = 0; // Anzahl angemeldeter Events, die evtl. der "onlythisingroup"-Beschrenkung unterliegen
						foreach($this->tuxe as $abzeichen) {
							if($abzeichen['onlythisingroup'] == 1 && in_array($abzeichen['eventid'],$cur_anmeldungen))
								$onlythisingroup_gebucht = 1;
							if(in_array($abzeichen['eventid'],$cur_anmeldungen)) {
								// wenn das Event gebucht ist
								if($abzeichen['alwaysallowedinthisgroup'] == 0) {
									$notalwaysallowedinthisgroup++;
								}
							}
						}
						foreach($this->tuxe as $abzeichen) {
							$frei = $abzeichen['quota'] - $abzeichen['teilnehmerzahl'];

							// onlythisingroup Funktion ("Kein Event ausser diesem")
							if($abzeichen['onlythisingroup'] == 1) {
								// In dieser Eventgruppe (nach parent) darf entweder dieses oder andere gebucht werden

							}
							$action = "-";
							if($abzeichen['editable'] == 1) {
								if(in_array($abzeichen['eventid'],$cur_anmeldungen)) {
									// abmelden
									$action = '<a href="?p=mycamp_schwimmen&a=d&eventid='.$abzeichen['eventid'].'&anmeldungid='.$anmeldung['anmeldungid'].'">abmelden</a>';
								}else{
									// anmelden
									if($frei>0) {
										if($abzeichen['onlythisingroup'] == 1 ) {
											
											if( $notalwaysallowedinthisgroup==0) {
												$action = '<a href="?p=mycamp_schwimmen&a=a&eventid='.$abzeichen['eventid'].'&anmeldungid='.$anmeldung['anmeldungid'].'">anmelden</a>';
											}
										}else{
											if(!$onlythisingroup_gebucht || $abzeichen['alwaysallowedinthisgroup'])
											$action = '<a href="?p=mycamp_schwimmen&a=a&eventid='.$abzeichen['eventid'].'&anmeldungid='.$anmeldung['anmeldungid'].'">anmelden</a>';
										}
									} // if frei
								}
							} // if editable
							$ret .= '
							<tr>
								<td style="vertical-align:top;">'.$abzeichen['name'].'</td>
								<td>'.nl2br($abzeichen['beschreibung']).'</td>
								<td style="text-align:center">'.$frei.'</td>
								<td>'.$action.'</td>
							</tr>
							';
						}
						$ret .= '
						</table>
						';
					} // if count tuxe
					
				} // while Anmeldungen
			} // if mysql_num_rows res1
			mysql_free_result($res1);

		} // if res1

		return $ret;
	}


	function getContent() {
		global $CURRENT_EVENT_ID;
		global $_SESSION;

		// Checken, ob die Seite wegen Wartungsarbeiten ausgeschaltet werden soll.
		// Funktion checkMaintenance() kommt aus class.HtmlPage.php
		$ret = $this->checkMaintenance();
		if($ret!='')
			return $ret;

		$this->doActions();
		$this->tuxe = $this->getTuxEvents();
		$ret .= $this->getEventListContent();
		return $ret;
	}
}


?>
