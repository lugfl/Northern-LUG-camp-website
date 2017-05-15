<?php

$PAGE['shirts']['name'] = "T-Shirts";
$PAGE['shirts']['navilevel'] = 1;
$PAGE['shirts']['login_required'] = 1;
$PAGE['shirts']['phpclass'] = 'HtmlPage_shirts';
$PAGE['shirts']['parent'] = 'root';

require_once('lib/inc.database.php');

class HtmlPage_shirts extends HtmlPage {

	var $name = "T-Shirts";
	var $navilevel = 1;
	var $login_required = 1;

	function HtmlPage_shirts() {
	}
	
	function getContent() {
    		$ret = '';
		$SQL = "SELECT * FROM event_artikel";
		$res = my_query($SQL);
		$ret .= '
		<table>
			<tr>
				<th>eventid</th>
				<th>Artikel</th>
				<th>Gr&ouml&szlig;e</th>
				<th>Anzahl</th>
			</tr>
		';
		
		while($row = mysql_fetch_object($res))
		{
			$anzahl = 0;
			$groesse = '';
			$eintrag = '';
			$SQL = "SELECT * FROM event_account_artikel WHERE artikelid = '".$row->artikelid."' ORDER BY groesse";
			$res2 = my_query($SQL);
			while($brow = mysql_fetch_object($res2)) {
				if($groesse != $brow->groesse && $groesse != '') {
					$ret .= '<tr><td>'.$row->eventid.'</td><td>'.$row->name.'</td><td>'.$groesse.'</td><td>'.$anzahl.'</td></tr>';
					$anzahl = $brow->anzahl;
				} else {
					$anzahl += $brow->anzahl;		
				}
				$groesse = $brow->groesse;
			}
			$ret .= '<tr><td>'.$row->eventid.'</td><td>'.$row->name.'</td><td>'.$groesse.'</td><td>'.$anzahl.'</td></tr>';
		}
		$ret .= '
		</table>
		';
		mysql_free_result($res);
		return $ret;
	}
}


?>
