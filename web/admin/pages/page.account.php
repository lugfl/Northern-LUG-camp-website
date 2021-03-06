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
	private $messages;

	function __construct() {
	}

	function updateAdminBemerkung($anmeldungid,$bemerkung) {
		$SQL = "UPDATE event_anmeldung SET admin_bemerkung = '".$bemerkung."' WHERE anmeldungid = '".$anmeldungid."'";
		$res = my_query($SQL);
		return '<p>Admin-Bemerkung ge�ndert !</p>';
	}
	
	function editAccount($accountid,$email) {
		$SQL = "UPDATE account SET email = '".$email."' WHERE accountid = '".$accountid."'";
		$res = my_query($SQL);
		return '<p>Email-Adresse ge�ndert !</p>';
	}

	function _readInput() {
		$this->p = http_get_var('p');
		$this->a = http_get_var('a');
		$this->an = http_get_var('an');
		$this->action = http_get_Var('action');
		$this->abemerkung = http_get_var('admin_bemerkung');
		$this->e_email = http_get_var('e_email');

		if($this->p=='account') {
			$tmp = http_get_var('accountid');
			if(is_numeric($tmp))
				$this->accountid = $tmp;
			if($this->action == 'abemerkung') {
				$this->messages = $this->updateAdminBemerkung($this->an,$this->abemerkung);
			} elseif ($this->action == 'edit') {
				$this->messages = $this->editAccount($this->accountid,$this->e_email);
			}
		}
	}

	function _validateInput() {
		if($this->accountid!=0) {
			
		}else{
			$this->errors['accountid'] = 'Falsche Accountid.';
		}
	}

	function getAccountContent($daten) {	
		$ret = '';
		$logintime = null;
		// define item to prevent php notice messages
		if(!array_key_exists('anmeldung', $daten))
			$daten['anmeldung'] = ARRAY();

		if($this->messages) {
			$ret = $this->messages;
		}
		$ret .= '
			<table class="datatable1">
				<form method="post" action="'.get_script_name().'?p=account&accountid='.$daten['accountid'].'&action=edit">
				<tr>
					<th>Benutzername</th>
					<td>'.$daten['username'].'</td>
				</tr>
				<tr>
					<th>EMail</th>
					<td><input type="text" size="40" name="e_email" value="'.$daten['email'].'" /></td>
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
			
			$llogin_SQL = "SELECT * FROM logins WHERE accountid='".$daten['accountid']."' ORDER BY logintime DESC";
			$llogin_res = my_query($llogin_SQL);
			if (my_affected_rows() != 0) {
				$llogin_row = mysqli_fetch_assoc($llogin_res);
				$logintime = $llogin_row['logintime'];
			}
			
			$ret .='</tr>
				<tr>
					<th>Letzter Login</th>
					<td>'.$logintime.'</td>
				</tr>
				<tr>
					<th></th>
					<td><input type="submit" value="�nderungen �bernehmen" /></td>
				</tr>
				</form>
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
					$zuzahlen = 0;
					if(isset($anmeldung['events'])) {
						
						$ret .= '
							<div class="head">Events</div>
							<ul>
						';
						foreach($anmeldung['events'] as $eventid=>$event) {
							$ret .= '
							<li>'.$event['name'].' (Preis: '.number_format($event['charge'],2,',','.').')';
							if($event['bezahlt']) {
								$ret .= ' <input type="checkbox" name="events_bezahlt['.$eventid.']" value="on" checked="checked" /> bezahlt ('.$anmeldung['events'][$eventid]['bezahlt'].')';
							} else {
								$ret .= ' <input type="checkbox" name="events_bezahlt['.$eventid.']" value="on" /> bezahlt';
								$zuzahlen += $event['charge'];
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
								$ret .= ' <input type="checkbox" name="artikel_bezahlt['.$accountartikelid.']" value="on" checked="checked" /> bezahlt ('.$daten['artikel'][$accountartikelid]['bezahlt'].')';
							} else {
								$ret .= ' <input type="checkbox" name="artikel_bezahlt['.$accountartikelid.']" value="on" /> bezahlt';
								$zuzahlen += $kauf['gesamtpreis'];
							}
							$ret .= '</li>';
						}
						$ret .= '
						</ul>
						';
					} // if artikel
					$ret .= '</p>';
					if($zuzahlen == 0) { $ret .= '<p class="bezahlt">Alles bezahlt !</p>';}
					else { $ret .= '<p class="zuzahlen">Insgesamt zu zahlender Betrag: '.$zuzahlen.' &euro;</p>';}
					$ret .= '
						<p><dd><input type="submit" value=" Zahlungen �bernehmen " /></dd></p>
					</form>
					<p>
						<form method="post" action="?p=account&action=abemerkung">
						<input type="hidden" name="accountid" value="'.$daten['accountid'].'" />
						<input type="hidden" name="an" value="'.$anmeldungid.'" />
						<div class="head">Admin-Bemerkung (f&uuml;r User nicht sichtbar)</div>
						<dd><textarea name="admin_bemerkung" rows="4" cols="45">'.$anmeldung['admin_bemerkung'].'</textarea></dd>
						<dd><input type="submit" value="&Uuml;bernehmen" /></dd>
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
				$row1 = mysqli_fetch_assoc($res1);
				if($row1) {
					$ret = $row1;
				}
				mysqli_free_result($res1);
			}

			$SQL2 = "SELECT a.anmeldungid,a.vorname,a.nachname,a.strasse,a.hausnr,a.plz,a.ort,a.land,a.email,a.vegetarier,a.arrival,a.ankunft,a.abfahrt,a.bemerkung,a.admin_bemerkung, ";
			$SQL2 .= " l.name AS lugname, la.name as landname ";
			$SQL2 .= " FROM event_anmeldung a LEFT JOIN event_lug l ON a.lugid=l.lugid ";
			$SQL2 .= " LEFT JOIN event_land la ON a.landid=la.landid";
			$SQL2 .= " WHERE accountid=".$accountid;
			$res2 = my_query($SQL2);
			if($res2) {
				while($row2 = mysqli_fetch_assoc($res2)) {
					$ret['anmeldung'][$row2['anmeldungid']] = $row2;

					// Events fuer diese Anmeldung
					$SQL3 = "SELECT e.eventid,e.name,e.charge,ae.bezahlt ";
					$SQL3 .= " FROM event_anmeldung_event ae LEFT JOIN event_event e ON ae.eventid=e.eventid ";
					$SQL3 .= " WHERE ae.anmeldungid=".$row2['anmeldungid'];
					$res3 = my_query($SQL3);
					if($res3) {
						while($row3 = mysqli_fetch_assoc($res3)) {
							$ret['anmeldung'][$row2['anmeldungid']]['events'][$row3['eventid']] = $row3;
						}
						mysqli_free_result($res3);
					} // if res3
				} // while fetch_assoc Anmeldungen
				mysqli_free_result($res2);

				$SQL4 = "SELECT aa.accountartikelid,a.name,a.preis,a.pic,aa.anzahl,aa.groesse,aa.bezahlt,(aa.anzahl*a.preis) AS gesamtpreis ";
				$SQL4 .= " FROM event_account_artikel aa ";
				$SQL4 .= " LEFT JOIN event_artikel a ON aa.artikelid=a.artikelid ";
				$SQL4 .= " WHERE aa.accountid=".$accountid;
				$res4 = my_query($SQL4);
				if($res4) {
					while($row4 = mysqli_fetch_assoc($res4)) {
						
						$ret['artikel'][$row4['accountartikelid']] = $row4;
					}
					mysqli_free_result($res4);
				}
			}
		} // if is_numeric accountid
		return $ret;
	}
}


?>
