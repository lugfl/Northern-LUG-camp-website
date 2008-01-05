<?php

require_once('lib/inc.database.php');

$PAGE['lugcamp_stats']['name'] = "Statistiken";
$PAGE['lugcamp_stats']['navilevel'] = 2;
$PAGE['lugcamp_stats']['login_required'] = 0;
$PAGE['lugcamp_stats']['phpclass'] = 'HtmlPage_lugcamp_stats';
$PAGE['lugcamp_stats']['parent'] = 'lugcamp';

class HtmlPage_lugcamp_stats extends HtmlPage {

	function _stats_luganmeldungen() {
		global $CURRENT_EVENT_ID;

		// Feststellen fuer welches Event wir grade anmelden
		if(!isset($CURRENT_EVENT_ID) || !is_numeric($CURRENT_EVENT_ID))
			$ceventid = 0;
		else
			$ceventid = $CURRENT_EVENT_ID;


		// Datenbankverbindung aufbauen
		my_connect();

		$ret = '<h2>Von welchen LUGs kommen die Teilnehmer</h2>';

		$SQL = "SELECT name,url FROM event_lug  ";
		$SQL .= " ORDER BY name ";
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
			while($row1 = mysql_fetch_assoc($res1)) {
				$ctr = '';
				// @TODO Counterabfrage	

				$ret .= '<tr>';
				$ret .= '<td>'.$row1['name'].'</td>';
				$ret .= '<td>';
				if(isset($row1['url']) && $row1['url']!='') {
					$ret .= '<a href="'.$row1['url'].'">'.$row1['url'].'</a>';
				}
				$ret .= '</td>';
				$ret .= '<td>'.$ctr.'</td>';
				$ret .= '</tr>';
			}
			$ret .= '</table>';
			mysql_free_result($res1);
		}
		return $ret;
	}

	function getContent() {
    $ret = '<h1>Ein paar Zahlen zum Camp</h1>';
		$ret .= $this->_stats_luganmeldungen();
		return $ret;
	}

}


?>
