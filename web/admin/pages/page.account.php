<?php

require_once('lib/inc.database.php');

$PAGE['account']['name'] = "Account Info";
$PAGE['account']['navilevel'] = 2;
$PAGE['account']['login_required'] = 1;
$PAGE['account']['phpclass'] = 'HtmlPage_account';
$PAGE['account']['parent'] = 'anmeldungsliste';
$PAGE['account']['hidden'] = 1;

class HtmlPage_account extends HtmlPage {

	var $name = "Account Info";
	var $navilevel = 1;
	var $login_required = 1;
	var $accountid = 0;
	var $p = '';
	var $errors = Array();
	var $errctr = 0;
	var $a = ''; // Action

	function HtmlPage_account() {
	}
	
	function _readInput() {
		$this->p = http_get_var('p');
		$this->a = http_get_var('a');

		if($this->p=='account') {
			$tmp = http_get_var('accountid');
			if(is_numeric($tmp))
				$this->accountid = $tmp;
		}
	}

	function _validateInput() {
		if($this->accountid!=0) {
			
		}else{
			$this->errors['accountid'] = 'Falsche Accountid.';
		}
	}

	function getAccountContent($daten) {
		$ret = '
			<table class="datatable1">
				<tr>
					<th>Benutzername</th>
					<td>'.$daten['username'].'</td>
				</tr>
				<tr>
					<th>EMail</th>
					<td>'.$daten['email'].'</td>
				</tr>
				<tr>
					<th>LUG</th>
					<td>'.$daten['lugname'].'</td>
				</tr>
				<tr>
					<th>ACL</th>
					<td>'.$daten['acl'].'</td>
				</tr>
				<tr>
					<th>Aktiv</th>';
			
			if($daten['active'] == 1) {
				$ret .= '<td>'.$daten['active'].'</td>';
			} else {
				$ret .= '<td>Noch nicht aktiviert ! - <a href="?p=remail&a='.$daten['accountid'].'">Aktivierungsmail senden</a></td>';
			}
			$ret .='</tr>
				<tr>
					<th>Anmeldungen</th>
					<td><ul>
				';
				foreach($daten['anmeldung'] as $anmeldungid=>$anmeldung) {
					$ret .= '<li>'.$anmeldung['vorname'].' '.$anmeldung['nachname'].'
					<p>
						'.$anmeldung['strasse'].' '.$anmeldung['hausnr'].'<br/>
						'.$anmeldung['plz'].' '.$anmeldung['ort'].', '.$anmeldung['landname'].'<br/>
						'.$anmeldung['email'].'<br/>
						'.($anmeldung['vegetarier']?'Vegetarier':'kein Vegetarier').'<br/>
						'.($anmeldung['bemerkung']?'<span class="head">Bemerkung</span>: '.nl2br($anmeldung['bemerkung']).'<br/>':'').'
					<form action="?p=kasse" method="post">
					<input type="hidden" name="action" value="pay" />
					<input type="hidden" name="accountid" value="'.$daten['accountid'].'" />
					<input type="hidden" name="anmeldungid" value="'.$anmeldungid.'" />
					';
					if(isset($anmeldung['events'])) {
						
						$ret .= '
							<div class="head">Events</div>
							<ul>
						';
						foreach($anmeldung['events'] as $eventid=>$event) {
							$ret .= '
							<li>'.$event['name'].' (Preis: '.number_format($event['charge'],2,',','.').')';
							if($event['bezahlt']) {
								$ret .= ' <input type="checkbox" name="events_bezahlt['.$eventid.']" checked="checked" /> bezahlt ('.$anmeldung['events'][$eventid]['bezahlt'].')';
							} else {
								$ret .= ' <input type="checkbox" name="events_bezahlt['.$eventid.']" /> bezahlt';
							}
							$ret .='</li>';
						}
						$ret .= '
							</ul>
						';
					} // if events
					if(isset($daten['artikel'])) {
						$ret .= '
							<div class="head">Artikel</div>
						<ul>
						';
						foreach($daten['artikel'] as $accountartikelid=>$kauf) {
							$ret .= '
							<li>'.$kauf['name'].', '.$kauf['groesse'].', '.$kauf['anzahl'].' (Gesamtpreis: '.number_format($kauf['gesamtpreis'],2,',','.').')';
							if($kauf['bezahlt']) {
								$ret .= ' <input type="checkbox" name="artikel_bezahlt['.$accountartikelid.']" checked="checked" /> bezahlt ('.$daten['artikel'][$accountartikelid]['bezahlt'].')';
							} else {
								$ret .= ' <input type="checkbox" name="artikel_bezahlt['.$accountartikelid.']" /> bezahlt';
							}
							$ret .= '</li>';
						}
						$ret .= '
						</ul>
						';
					} // if artikel
					$ret .= '
					<input type="submit" value=" Zahlungen übernehmen " />
					</form>
					</p>
					</li>';
					}
				$ret .= '	</ul>
					</td>
				</tr>
			</table>
		';


		return $ret;

	}

	function getContent() {
		$this->_readInput();
		$this->_validateInput();

		$ret = '
			<h1>Account Informationen</h1>
		';
		if(count($this->errors)>0) {
			$ret .= '
				<p class="error">
			';
			foreach($this->errors as $k=>$str) {
				$ret .= $str.'<br/>';
			}
			$ret .= '
				</p>
			';
		} // if errors

		switch($this->a) {
			default:
				if($this->accountid!=0) {
					$daten = $this->_loadAccount($this->accountid);
					$ret .= $this->GetAccountContent($daten);
					//echo '<pre>'; var_dump($daten); echo '</pre>';

				}
			break;
		}
		return $ret;
	}

	function _loadAccount($accountid) {
		$ret = Array();
		if(is_numeric($accountid)) {
			$SQL1 = "SELECT a.accountid,a.username,a.email,UNIX_TIMESTAMP(a.crdate) AS crdate,a.acl,a.active,l.name lugname,l.lugid  ";
			$SQL1 .= " FROM account a LEFT JOIN event_lug l ON a.lugid=l.lugid WHERE a.accountid=".$accountid;
			$res1 = my_query($SQL1);
			if($res1) {
				$row1 = mysql_fetch_assoc($res1);
				if($row1) {
					$ret = $row1;
				}
				mysql_free_result($res1);
			}

			$SQL2 = "SELECT a.anmeldungid,a.vorname,a.nachname,a.strasse,a.hausnr,a.plz,a.ort,a.land,a.email,a.vegetarier,a.arrival,a.ankunft,a.abfahrt,a.bemerkung, ";
			$SQL2 .= " l.name AS lugname, la.name as landname ";
			$SQL2 .= " FROM event_anmeldung a LEFT JOIN event_lug l ON a.lugid=l.lugid ";
			$SQL2 .= " LEFT JOIN event_land la ON a.landid=la.landid";
			$SQL2 .= " WHERE accountid=".$accountid;
			$res2 = my_query($SQL2);
			if($res2) {
				while($row2 = mysql_fetch_assoc($res2)) {
					$ret['anmeldung'][$row2['anmeldungid']] = $row2;

					// Events fuer diese Anmeldung
					$SQL3 = "SELECT e.eventid,e.name,e.charge,ae.bezahlt ";
					$SQL3 .= " FROM event_anmeldung_event ae LEFT JOIN event_event e ON ae.eventid=e.eventid ";
					$SQL3 .= " WHERE ae.anmeldungid=".$row2['anmeldungid'];
					$res3 = my_query($SQL3);
					if($res3) {
						while($row3 = mysql_fetch_assoc($res3)) {
							$ret['anmeldung'][$row2['anmeldungid']]['events'][$row3['eventid']] = $row3;
						}
						mysql_free_result($res3);
					} // if res3
				} // while fetch_assoc Anmeldungen
				mysql_free_result($res2);

				$SQL4 = "SELECT aa.accountartikelid,a.name,a.preis,a.pic,aa.anzahl,aa.groesse,aa.bezahlt,(aa.anzahl*a.preis) AS gesamtpreis ";
				$SQL4 .= " FROM event_account_artikel aa ";
				$SQL4 .= " LEFT JOIN event_artikel a ON aa.artikelid=a.artikelid ";
				$SQL4 .= " WHERE aa.accountid=".$accountid;
				$res4 = my_query($SQL4);
				if($res4) {
					while($row4 = mysql_fetch_assoc($res4)) {
						
						$ret['artikel'][$row4['accountartikelid']] = $row4;
					}
					mysql_free_result($res4);
				}
			}
		} // if is_numeric accountid
		return $ret;
	}
}


?>
