<?php

$PAGE['shirts_csv']['name'] = "T-Shirts CSV";
$PAGE['shirts_csv']['navilevel'] = 1;
$PAGE['shirts_csv']['login_required'] = 1;
$PAGE['shirts_csv']['phpclass'] = 'HtmlPage_shirts_csv';
$PAGE['shirts_csv']['parent'] = 'root';

require_once('lib/inc.database.php');

class HtmlPage_shirts_csv extends HtmlPage {

	var $name = "T-Shirts CSV";
	var $navilevel = 1;
	var $login_required = 1;
	var $barcodeupdates = 0;
	function HtmlPage_shirts_csv() {
	}
	
	function getContent() {
    		$ret = '';
		$SQL = "SELECT aa.accountid,aa.artikelid,aa.anzahl,aa.crdate,aa.groesse,aa.bezahlt,a.username,ea.name AS artikelname ";
		$SQL .= "FROM event_account_artikel aa ";
		$SQL .= "LEFT JOIN account a ON a.accountid = aa.accountid ";
		$SQL .= "LEFT JOIN event_artikel ea ON ea.artikelid = aa.artikelid ";
		$SQL .= "ORDER BY crdate ";
		$res = my_query($SQL);
		
		if($res) {
			if(mysql_num_rows($res) >0) {
				$csv = '"LfdNr";"Name";"Farbe";"Größe";"Anzahl";"Bestelldatum";"bezahlt";"ausgegeben"'."\n";
				$ctr = 1;
			
				while($row = mysql_fetch_assoc($res)) {
					$csv .= $ctr.';"'.$row['username'].'";"'.$row['artikelname'].'";"'.$row['groesse'].'";"'.$row['anzahl'].'";"'.$row['crdate'].'";"'.($row['bezahlt'] ? "     X" : "").'";""';
					$csv .= "\n";
					$ctr++;
				}
			}
			mysql_free_result($res);
		}
		$handle = fopen("tmp/shirts.csv","w");
		fwrite($handle, $csv);
		fclose($handle);
		$ret .= '<a href="tmp/shirts.csv">klick</a>';
		return $ret;
	}
}
?>
