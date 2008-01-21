<?php

require_once('lib/inc.database.php');

$PAGE['lugcamp_stats']['name'] = "Statistiken";
$PAGE['lugcamp_stats']['navilevel'] = 2;
$PAGE['lugcamp_stats']['login_required'] = 0;
$PAGE['lugcamp_stats']['phpclass'] = 'HtmlPage_lugcamp_stats';
$PAGE['lugcamp_stats']['parent'] = 'lugcamp';

class HtmlPage_lugcamp_stats extends HtmlPage {

	var $ceventid = 0;
	
	function HtmlPage_lugcamp_stats() {
		global $CURRENT_EVENT_ID;

		// Feststellen fuer welches Event wir grade anmelden
		if(!isset($CURRENT_EVENT_ID) || !is_numeric($CURRENT_EVENT_ID))
			$this->ceventid = 0;
		else
			$this->ceventid = $CURRENT_EVENT_ID;


		// Datenbankverbindung aufbauen
		my_connect();
	}

	function _stats_luganmeldungen() {
		$ret = '<h2>Von welchen LUGs kommen die Teilnehmer</h2>';

		$SQL = "SELECT l.name,l.url,COUNT(a.anmeldungid) AS anmeldungen ";
		$SQL .= " FROM event_anmeldung a ";
		$SQL .= " LEFT JOIN event_lug l ON a.lugid=l.lugid ";
		$SQL .= " LEFT JOIN event_anmeldung_event ae ON a.anmeldungid=ae.anmeldungid ";
		$SQL .= " WHERE ae.eventid=".$this->ceventid;
		$SQL .= " GROUP BY l.lugid ";
		$SQL .= " ORDER BY l.name ";
		$res1 = my_query($SQL);
		if($res1 && mysql_num_rows($res1)>0) {
			$ret .= '<table class="datatable1">
				<caption>&Uuml;bersicht der LUGs</caption>
				<thead>
					<th>LUG</th>
					<th>Homepage</th>
					<th>Anzahl Anmeldungen</th>
				</thead>
			';
			$ges = 0;
			while($row1 = mysql_fetch_assoc($res1)) {
				$ctr = '';

				$ret .= '<tr>';
				$ret .= '<td>'.$row1['name'].'</td>';
				$ret .= '<td>';
				if(isset($row1['url']) && $row1['url']!='') {
					$ret .= '<a href="'.$row1['url'].'">'.$row1['url'].'</a>';
				}
				$ret .= '</td>';
				$ret .= '<td>'.$row1['anmeldungen'].'</td>';
				$ret .= '</tr>';
				$ges += $row1['anmeldungen'];
			}
			$ret .= '</table>
			<p>
				Somit haben wir insgesamt <span class="bold">'.$ges.'</span> Anmeldungen zum Camp.
			</p>
			';
			mysql_free_result($res1);
		}

		return $ret;
	}

	function _stats_eventanmeldungen() {
		$ret .= '
			<h2>Wie viele Anmeldungen haben wir zu den einzelnen Veranstaltungen</h2>
		';
		$SQL = "SELECT e.name,COUNT(ea.anmeldungid) AS anmeldungen ";
		$SQL .= " FROM event_anmeldung_event ea ";
		$SQL .= " LEFT JOIN event_event e ON ea.eventid=e.eventid ";
		$SQL .= " WHERE e.eventid=".$this->ceventid . " OR e.parent=".$this->ceventid;
		$SQL .= " GROUP BY e.eventid ";
		$SQL .= " ORDER BY e.name ";
		$res1 = my_query($SQL);
		if($res1 && mysql_num_rows($res1)>0) {
			$ret .= '<table class="datatable1">
				<caption>&Uuml;bersicht der LUGs</caption>
				<thead>
					<th>Veranstaltung</th>
					<th>Anzahl Anmeldungen</th>
				</thead>
			';
			$ges = 0;
			while($row1 = mysql_fetch_assoc($res1)) {
				$ctr = '';

				$ret .= '<tr>';
				$ret .= '<td>'.$row1['name'].'</td>';
				$ret .= '<td>'.$row1['anmeldungen'].'</td>';
				$ret .= '</tr>';
				$ges += $row1['anmeldungen'];
			}
			$ret .= '</table>
			';
			mysql_free_result($res1);
		}
		return $ret;
	}
	
	function getContent() {
    $ret = '<h1>Ein paar Zahlen zum Camp</h1>';
		$ret .= $this->_stats_luganmeldungen();
		$ret .= $this->_stats_eventanmeldungen();
		return $ret;
	}

}


?>
