<?php

$PAGE['mycamp_shop']['name'] = "Camp-Shop";
$PAGE['mycamp_shop']['navilevel'] = 2;
$PAGE['mycamp_shop']['login_required'] = 1;
$PAGE['mycamp_shop']['phpclass'] = 'HtmlPage_mycamp_shop';
$PAGE['mycamp_shop']['parent'] = 'mycamp';

class HtmlPage_mycamp_shop extends HtmlPage {

	var $kauf = Array();
	var $errctr = 0;

	function _readInput() {
		
		$kaufe = http_get_var('kaufe');
		if(is_array($kaufe)) {
			// Es ist etwas gekauft worden
			$this->kauf = $kaufe;
		}

	}

	function _validateInput() {
		$tmp = Array();
		foreach($this->kauf as $artikelid=>$daten) {
			if(!is_numeric($artikelid)) {
				$this->errctr++;	// Artikelid nicht korrekt
			}else{
				if(isset($daten['anzahl']) && $daten['anzahl']!='' ) {
					if(is_numeric($daten['anzahl']))
						$tmp[$artikelid] = $daten;
					else
						$this->errctr++; // Anzahl ist falsch
				}else{
					// Kein Artikel
				}
			}
		} // foreach
		$this->kauf = $tmp;
		return $this->errctr;
	}

	function getArtikelList() {
		global $_SESSION;

		$ret = '';
		$this->_readInput();
		$this->_validateInput();
		if($this->errctr==0) {
			// Es sind keine Fehler aufgetreten, also in den Warenkorb

			if(!isset($_SESSION['warenkorb']))
				$_SESSION['warenkorb'] = Array();

			foreach($this->kauf as $artikelid=>$daten) {
				$pairs = Array();
				array_push($pairs,'accountid='.$_SESSION['_accountid']);
				array_push($pairs,'artikelid='.$artikelid);
				array_push($pairs,'anzahl='.$daten['anzahl']);

				if(isset($daten['groesse']) && $daten['groesse']!='') {
					// Ein Artikel mit Groessenangabe
					array_push($pairs,"groesse='".my_escape_string($daten['groesse'])."'");
				}else{
					// Ein Artikel ohne Groessenangabe
				}
				array_push($pairs,'crdate=NOW()');
				$SQLinsert = "INSERT INTO event_account_artikel SET ".join(",",$pairs);
				my_query($SQLinsert);
			}
		}

		$SQL1 = "SELECT * FROM event_artikel WHERE kaufab<NOW() AND kaufbis>NOW() ORDER BY name";
		$res1 = my_query($SQL1);

		if($res1) {
			if(mysql_num_rows($res1)>0) {
				$ret .= '
				<form action="?" method="post">
				<div>
				<input type="hidden" name="p" value="mycamp_shop"/>
				<table class="datatable1">
					<caption>Bestellungen</caption>
					<tr>
						<th>Artikel</th>
						<th></th>
						<th>Beschreibung</th>
						<th>St&uuml;ckpreis</th>
						<th>Gr&ouml;sse</th>
						<th>Anzahl</th>
					</tr>
				';
				while($row1 = mysql_fetch_assoc($res1)) {

					$anzahl = '';
					$selgr = '';
					if($this->errctr>0) {
						// Werte nur ins Formular uebernehmen, wenn ein Fehler auftrat
						if(isset($this->kauf[$row1['artikelid']]) && isset($this->kauf[$row1['artikelid']]['anzahl']) && is_numeric($this->kauf[$row1['artikelid']]['anzahl']))
							$anzahl = $this->kauf[$row1['artikelid']]['anzahl'];
						if(isset($this->kauf[$row1['artikelid']]) && isset($this->kauf[$row1['artikelid']]['groesse']) && $this->kauf[$row1['artikelid']]['groesse']!='')
							$selgr = $this->kauf[$row1['artikelid']]['groesse'];
					}
					$ret .= '
					<tr>
						<td>'.$row1['name'].'</td>
						<td>
					';
					if(isset($row1['pic']) && $row1['pic']!='') {
						$fileurl = './images/shop/'.$row1['pic'];
						if(is_file($fileurl)) {
							$ret .= '<img src="'.$fileurl.'" width="100"/>';
						}
					}
					$ret .= '
						</td>
						<td>'.$row1['beschreibung'].'</td>
						<td>'.$row1['preis'].'</td>
						<td><select name="kaufe['.$row1['artikelid'].'][groesse]">
					';

					$gr = $row1['groessen'] ? $row1['groessen'] : '';
					$arr_gr = split(',',$gr);
					foreach( $arr_gr as $g) {
						$sel = '';
						if($selgr==$g)
							$sel = ' selected="selected"';
						$ret .= '
							<option value="'.$g.'"'.$sel.'>'.$g.'</option>
						';
					}

					$ret .= '</select></td>
						<td><input type="text" size="10" name="kaufe['.$row1['artikelid'].'][anzahl]" value="'.$anzahl.'"/></td>
					</tr>
					';
				}
				$ret .= '
					<tr>
						<td colspan="5"><input type="submit" value="Bestellen"/></td>
					</tr>
				</table>
				';
				if($this->errctr>0) {
					$ret .= '
					<p class="error">
					Bitte &uuml;berpr&uuml;fe noch einmal Deine Eingaben. Es ist ein Fehler aufgetreten.
					</p>
					';
				}
				$ret .= '
				</div>
				</form>
				<p>
				Die Bestellungen, die Du hier abschickst, kannst Du unter <a href="?p=rechnung">Rechnung</a> jederzeit einsehen
				und solange diese noch nicht bezahlt bzw. von uns extern bestellt wurden auch wieder l&ouml;schen. Mehrfachbestellungen sind jederzeit m&ouml;glich.
				</p>
				';

			} // if num_rows res1
		} // if res1
		return $ret;
	}

	function getContent() {
		global $_SESSION;

    $ret = '
		<h1>Die Top Fan-Artikel zum Camp</h1>
		';
		$ret .= $this->getArtikelList();

		return $ret;
	}

}


?>
