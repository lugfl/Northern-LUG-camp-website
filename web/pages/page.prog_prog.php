<?php

$PAGE['prog_prog']['name'] = "Programm";
$PAGE['prog_prog']['navilevel'] = 2;
$PAGE['prog_prog']['login_required'] = 0;
$PAGE['prog_prog']['phpclass'] = 'HtmlPage_prog_prog';
$PAGE['prog_prog']['parent'] = 'prog';

class HtmlPage_prog_prog extends HtmlPage {

	var $ceventid = 0;

	function HtmlPage_prog_prog() {
		global $CURRENT_EVENT_ID;
		if(isset($CURRENT_EVENT_ID) && is_numeric($CURRENT_EVENT_ID))
			$this->ceventid = $CURRENT_EVENT_ID;		
		my_connect();
	}

	function getContent() {
    $ret = '
			<h1>Programm&uuml;bersicht</h1>
		';
		$SQL = "SELECT UNIX_TIMESTAMP(start) AS start,UNIX_TIMESTAMP(ende) AS ende,";
		$SQL .= "titel,beschreibung,kategorie FROM event_programm ";
		$SQL .= "WHERE kategorie LIKE '%programm%' ";
		$SQL .= " AND eventid=".$this->ceventid;
		$SQL .= " ORDER BY start";
		$res = my_query($SQL);
		if(mysql_num_rows($res)>0) {
			$ret .= '
				<table class="datatable1">
			';
			$letztertag = "";
			while($row = mysql_fetch_assoc($res)) {
				$tag = date("d.m.y",$row['start']);
				$endetag = date("d.m.y",$row['ende']);
				if($tag!=$letztertag) {
					$letztertag = $tag;
				}else{
					$tag = "";
				}
				$start = date("H:i",$row['start']);

				$ende = date("H:i",$row['ende']);
				$ret .= '
					<tr>
						<td>'.$tag.'</td>
						<td>'.$start.' - '.$ende.'</td>
						<td>'.htmlentities($row['titel']).'</td>
						<td>'.htmlentities($row['kategorie']).'</td>
					</tr>
				';
				if(strlen($row['beschreibung'])>0) {
					$ret .= '
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td colspan="2">'.$row['beschreibung'].'</td>
					</tr>
					';
				} // if beschreibung
			} // while
			$ret .= '
				</table>
			';
		}else{
			$ret .='
			<p>
			Noch stehen die Daten leider nicht bereit.
			</p>
			';
		}
		$ret .= '
<p>Wenn ihr selber Vortr&auml;ge halten wollt, mailt bitte eine kurze Zusammenfassung an <b>call4paper (at) lug-camp-2008.de</b>, damit wir den Vortrag ins Programm einbauen k&ouml;nnen.</p>
		';
		return $ret;
	}

}


?>
