<?php

require_once('lib/func.pear_mail.php');
require_once('Mail/RFC822.php');

$PAGE['anmeldung']['name'] = "Anmeldung";
$PAGE['anmeldung']['navilevel'] = 1;
$PAGE['anmeldung']['login_required'] = 0;
$PAGE['anmeldung']['phpclass'] = 'HtmlPage_anmeldung';
$PAGE['anmeldung']['parent'] = 'root';

class HtmlPage_anmeldung extends HtmlPage {

	// Inputdaten
	var $in = Array();

	// Error
	var $err = Array();

	function _readInput() {
		$this->in['mode'] = http_get_var('mode');

		$this->in['nickname']		= http_get_var("anmeldung_form_nickname");
		$this->in['passwort']		= http_get_var("anmeldung_form_passwort");
		$this->in['passwort2']		= http_get_var("anmeldung_form_passwort2");
		$this->in['email']		= http_get_var("anmeldung_form_email");

		$this->in['vorname']		= http_get_var("anmeldung_form_vorname");
		$this->in['nachname']		= http_get_var("anmeldung_form_nachname");
		$this->in['strasse']		= http_get_var("anmeldung_form_strasse");
		$this->in['haus']		= http_get_var("anmeldung_form_haus");
		$this->in['plz']		= http_get_var("anmeldung_form_plz");
		$this->in['ort']		= http_get_var("anmeldung_form_ort");
		$this->in['landid']		= http_get_var("anmeldung_form_landid");
		$this->in['landnew']		= http_get_var("anmeldung_form_landnew");
		$this->in['geb_d']		= http_get_var("anmeldung_form_geb_d");
		$this->in['geb_m']		= http_get_var("anmeldung_form_geb_m");
		$this->in['geb_y']		= http_get_var("anmeldung_form_geb_y");

		$this->in['lugid']		= http_get_var("anmeldung_form_lug");
		$this->in['lugnew']		= http_get_var("anmeldung_form_lugnew");

		$this->in['vegetarier']		= http_get_var("anmeldung_form_vegetarier");
		$this->in['events']		= http_get_var("anmeldung_form_events");
		if($this->in['events'] == '') { $this->in['events'] = Array(); }

		$this->in['anreise']		= http_get_var("anmeldung_form_anreise");
		$this->in['ankunft']		= http_get_var("anmeldung_form_ankunft");
		$this->in['abfahrt']		= http_get_var("anmeldung_form_abfahrt");
	}

	function _validateInput() {
	
		if(!preg_match('/^[a-z0-9-_.@!?:;,]{3,30}$/i',$this->in['nickname'])) {
			// Fehler im Nickname
			$this->err['nickname'] = '<p>Dieser Nickname enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (3-30 Zeichen)</p>';
		}
		$nickname_query	= my_query("SELECT accountid FROM account WHERE username='".my_escape_string($this->in['nickname'])."';");
		if(mysql_num_rows($nickname_query) != 0) {
			$this->err['nickname'] = '<p>Dieser Nickname ist bereits vergeben!</p>';
		}
		
		if($this->in['passwort'] == '') {
			$this->err['passwort'] = '<p>Du hast kein Passwort angegeben!</p>';
		}elseif($this->in['passwort'] != $this->in['passwort2']) {
			$this->err['passwort'] = '<p>Deine Passw&ouml;rter stimmen nicht &uuml;berein!</p>';
		}

		if(!preg_match('/^[a-z0-9-_.@äöüß]{8,50}$/i',$this->in['email'])) {
			// Fehler in der Mailadresse
			$this->err['email'] = '<p>Diese Adresse enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (8-50 Zeichen)</p>';
		}

		$mrfc822 = new Mail_RFC822();
		if($mrfc822->isValidInetAddress($this->in['email']) == FALSE) {
			$this->err['email'] = '<p>Diese Adresse ist ung&uuml;ltig!</p>';
		}

		if(!preg_match('/^[[:alpha:]]{3,40}$/i',$this->in['vorname'])) {
			// Fehler im Vornamen
			$this->err['vorname'] = '<p>Dieser Vorname enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (3-40 Zeichen)</p>';
		}

		if(!preg_match('/^[[:alpha:]]{3,40}$/i',$this->in['nachname'])) {
			// Fehler im Nachnamen
			$this->err['nachname'] = '<p>Dieser Nachname enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (3-40 Zeichen)</p>';
		}

		if(!preg_match('/^[[:alpha:]. -]{3,40}$/i',$this->in['strasse'])) {
			// Fehler in der Strasse
			$this->err['strasse'] = '<p>Dieser Stra&szlig;enname enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (3-40 Zeichen)</p>';
		}

		if(!preg_match('/^[[:alpha:]0-9]{1,5}$/i',$this->in['haus'])) {
			// Fehler in der Hausnummer
			$this->err['haus'] = '<p>Diese Hausnummer enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (1-5 Zeichen)</p>';
		}

		if(!preg_match('/^[[:alpha:]0-9.: -]{3,10}$/i',$this->in['plz'])) {
			// Fehler in der PLZ
			$this->err['plz'] = '<p>Diese Postleitzahl enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (3-10 Zeichen)</p>';
		}

		if(!preg_match('/^[[:alpha:]]{3,40}$/i',$this->in['ort'])) {
			// Fehler im Ort
			$this->err['ort'] = '<p>Dieser Ort enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (3-40 Zeichen)</p>';
		}

		if($this->in['landid'] == 0)
		{
			if(!preg_match('/^[[:alpha:].:,; -]{3,50}$/i',$this->in['landnew'])) {
				// Fehler im neu angegebenen Land
				$this->err['land'] = '<p>Du hast kein Land angebeben oder der Name enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (3-40 Zeichen)</p>';
			}
			// Wenn der Name bereits in der DB existiert wird später einfach die existierende ID übernommen, die Überprüfung dafür erfolgt in schritt2
		}else{
			// Wenn ein Land ausgewählt ist, ein neu angegebenes ignorieren
			$this->in['landnew'] = '';
		}

		$geb_err = '';
		if(!preg_match('/^[0-9]{1,2}$/i',$this->in['geb_d']) || $this->in['geb_d'] > 31 || $this->in['geb_d'] < 1 ) {
			$geb_err .= '<p>Der Tag ist nicht richtig angegeben.</p>';
		}
		if(!preg_match('/^[0-9]{1,2}$/i',$this->in['geb_m']) || $this->in['geb_m'] > 12 || $this->in['geb_m'] < 1 ) {
			$geb_err .= '<p>Der Monat ist nicht richtig angegeben.</p>';
		}
		if(!preg_match('/^[0-9]{4}$/i',$this->in['geb_y']) || $this->in['geb_y'] > 2000 || $this->in['geb_y'] < 1900 ) {
			$geb_err .= '<p>Das Jahr ist nicht richtig angegeben.</p>';
		}
		if($geb_err != '') { $this->err['geb'] = $geb_err; }

		if($this->in['lugid'] == 0)
		{
			if(!preg_match('/^[[:alpha:].:,;]{3,50}$/i',$this->in['lugnew'])) {
				// Fehler in der neu angegebenen Lug
				$this->err['lug'] = '<p>Du hast keine LUG angebeben oder der Name enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (3-40 Zeichen)</p>';
			}
			// Wenn der Name bereits in der DB existiert wird später einfach die existierende ID übernommen, die Überprüfung dafür erfolgt in schritt2
		}else{
			// Wenn eine LUG ausgewählt ist, eine neu angegebene ignorieren
			$this->in['lugnew'] = '';
		}

		if($this->in['vegetarier'] != 1) { $this->in['vegetarier'] = 0; }
	}


	function user_angemeldet() { # $user
		my_connect();
		# in der DB prüfen ob benutzer bereits angemeldet ist
		$ret = 0;
		#$user_query = my_query("SELECT * FROM account WHERE nickname = '$user'");
		return $ret;
	}

	# durch $user kann die Funktion auch für eine Übersicht verwendet werden
	function anmeldung_status() { # $user
		$ret = '';
		my_connect();
		# welchen Status hat die anmeldung ? (Alles OK, noch nicht bezahlt, Anmeldung unvollständig, Einverständnis fehlt)
		$ret .= '<p>Status:</p>';
		return $ret;
	}

	function anmeldung_form() {
		$ret = '';
		my_connect();
		# Formular zur Anmeldung ausgeben
		$ret .= '
			<p>
			<div id="anmeldung_form">
			<form action="?" method="post">
				<input type="hidden" name="p" value="anmeldung"/>
				<input type="hidden" name="mode" value="speichermich"/>
			<dl>

				<dt><label for-id="anmeldung_form_nickname">Account-/Nickname*</label></dt>
				<dd><input id="anmeldung_form_nickname" type="text" name="anmeldung_form_nickname" value="'.$this->in['nickname'].'" />
		';
		if(isset($this->err['nickname'])) {
			$ret .= '<p class="error">'.$this->err['nickname'].'</p>';
		}
		$ret .= '
				</dd>

				<dt><label for-id="anmeldung_form_passwort">Accountpasswort*</label></dt>
				<dd><input id="anmeldung_form_passwort" type="password" name="anmeldung_form_passwort" size="15" value="'.$this->in['passwort'].'" /></dd>

				<dt><label for-id="anmeldung_form_passwort2">Passwort wiederholen*</label></dt>
				<dd><input id="anmeldung_form_passwort2" type="password" name="anmeldung_form_passwort2" size="15" value="'.$this->in['passwort2'].'" />
		';
		if(isset($this->err['passwort'])) {
			$ret .= '<p class="error">'.$this->err['passwort'].'</p>';
		}
		$ret .= '
				</dd>

				<dt><label for-id="anmeldung_form_email">E-Mail*</label></dt>
				<dd><input id="anmeldung_form_email" type="text" name="anmeldung_form_email" size="30" value="'.$this->in['email'].'" />
		';
		if(isset($this->err['email'])) {
			$ret .= '<p class="error">'.$this->err['email'].'</p>';
		}
		$ret .= '
				</dd>

				<dt><label for-id="anmeldung_form_vorname">Vorname*</label></dt>
				<dd><input id="anmeldung_form_vorname" type="text" name="anmeldung_form_vorname" value="'.$this->in['vorname'].'" />
		';
		if(isset($this->err['vorname'])) {
			$ret .= '<p class="error">'.$this->err['vorname'].'</p>';
		}
		$ret .= '</dd>

				<dt><label for-id="anmeldung_form_nachname">Nachname*</label></dt>
				<dd><input id="anmeldung_form_nachname" type="text" name="anmeldung_form_nachname" value="'.$this->in['nachname'].'" />
		';
		if(isset($this->err['nachname'])) {
			$ret .= '<p class="error">'.$this->err['nachname'].'</p>';
		}
		$ret .= '</dd>

				<dt><label for-id="anmeldung_form_strasse">Stra&szlig;e* und Hausnummer*</label></dt>
				<dd>
					<input id="anmeldung_form_strasse" type="text" name="anmeldung_form_strasse" value="'.$this->in['strasse'].'" />
					<input id="anmeldung_form_haus" type="text" name="anmeldung_form_haus" value="'.$this->in['haus'].'" size="4" />
		';
		if(isset($this->err['strasse'])) {
			$ret .= '<p class="error">'.$this->err['strasse'].'</p>';
		}
		if(isset($this->err['haus'])) {
			$ret .= '<p class="error">'.$this->err['haus'].'</p>';
		}
		$ret .= '
				</dd>

				<dt><label for-id="anmeldung_form_plz">PLZ* und Ort*</label></dt>
				<dd>
					<input id="anmeldung_form_plz" type="text" name="anmeldung_form_plz" value="'.$this->in['plz'].'" size="8" />
					<input id="anmeldung_form_ort" type="text" name="anmeldung_form_ort" value="'.$this->in['ort'].'" />
		';
		if(isset($this->err['plz'])) {
			$ret .= '<p class="error">'.$this->err['plz'].'</p>';
		}
		if(isset($this->err['ort'])) {
			$ret .= '<p class="error">'.$this->err['ort'].'</p>';
		}
		$ret .= '
				</dd>

				<dt><label for-id="anmeldung_form_land">Land*</label></dt>
				<dd>
					<select id="anmeldung_form_landid" name="anmeldung_form_landid">
					<option value="0"></option>';
		$land_query = my_query("SELECT * FROM event_land");
		while($land_row = mysql_fetch_object($land_query))
		{
			if($this->in['landid'] == $land_row->landid) {
				$ret .= '<option value="'.$land_row->landid.'" selected="selected">'.$land_row->name.'</option>';
			}else{
				$ret .= '<option value="'.$land_row->landid.'">'.$land_row->name.'</option>';
			}
		}
		$ret .= '
					</select> ausw&auml;hlen oder neues Land anlegen 
					<input id="anmeldung_form_landnew" type="text" name="anmeldung_form_landnew" value="'.$this->in['landnew'].'" size="30" />
		';
		if(isset($this->err['land'])) {
			$ret .= '<p class="error">'.$this->err['land'].'</p>';
		}

		$ret .= '</dd>

				<dt><label for-id="anmeldung_form_geb_d">Geburtsdatum (dd-mm-yyyy)*</label></dt>
				<dd>
					<input id="anmeldung_form_geb_d" type="text" name="anmeldung_form_geb_d" value="'.$this->in['geb_d'].'" size="2" maxlength="2" />
					<input id="anmeldung_form_geb_m" type="text" name="anmeldung_form_geb_m" value="'.$this->in['geb_m'].'" size="2" maxlength="2" />
					<input id="anmeldung_form_geb_y" type="text" name="anmeldung_form_geb_y" value="'.$this->in['geb_y'].'" size="4" maxlength="4" />
		';
		if(isset($this->err['geb'])) {
			$ret .= '<p class="error">'.$this->err['geb'].'</p>';
		}
		$ret .= '
				</dd>

				<dt><label for-id="anmeldung_form_lug">LUG*</label></dt>
				<dd>
					<select id="anmeldung_form_lug" name="anmeldung_form_lug">
					<option value="0"></option>';
		$lugs_query = my_query("SELECT * FROM event_lug");
		while($lugs_row = mysql_fetch_object($lugs_query))
		{
			if($this->in['lugid'] == $lugs_row->lugid) {
				$ret .= '<option value="'.$lugs_row->lugid.'" selected="selected">'.$lugs_row->name.'</option>';
			}else{
				$ret .= '<option value="'.$lugs_row->lugid.'">'.$lugs_row->name.'</option>';
			}
		}
		$ret .= '
					</select> ausw&auml;hlen oder neue LUG anlegen 
					<input id="anmeldung_form_lugnew" type="text" name="anmeldung_form_lugnew" value="'.$this->in['lugnew'].'" size="30" />
		';
		if(isset($this->err['lug'])) {
			$ret .= '<p class="error">'.$this->err['lug'].'</p>';
		}
		$ret .= '
				</dd>
				<h3>Optionale Angaben</h3>

				<dt><label for-id="anmeldung_form_vegetarier">Essen</label></dt>
				<dd><input id="anmeldung_form_vegetarier" type="checkbox" name="anmeldung_form_vegetarier" value="1"';
				if($this->in['vegetarier'] == 1) { $ret .= ' checked="checked" '; }
				$ret .= ' /> Ich bin Vegetarier</dd>


				<dt><label for-id="anmeldung_form_events[]">Veranstaltungen</label></dt>
				<dd>Ich m&ouml;chte an folgenden Veranstaltungen w&auml;hrend des Lugcamps teilnehmen.</dd>';

		
		$events_query = my_query("SELECT * FROM event_event");
		while($events_row = mysql_fetch_object($events_query)) {
			$ret_quota = '';
			if($events_row->quota) {
				$event_quota_SQL	= "SELECT * FROM event_anmeldung_event WHERE eventid='".$events_row->eventid."'";
				$event_quota_query	= my_query($event_quota_SQL);
				$event_member		= mysql_num_rows($event_quota_query);
				if(($events_row->quota-$event_member) > 0) {
					$ret .= '<dd><input id="anmeldung_form_events[]" name="anmeldung_form_events[]" type="checkbox" value="'.$events_row->eventid;
					if(in_array($events_row->eventid,$this->in['events'])) { $ret .= ' checked="checked" '; }
					$ret .= '" /> '.$events_row->name.' (+'.number_format($events_row->charge,2,",",".").'&euro;';
					$ret .= ' und noch '.($events_row->quota-$event_member).' Pl&auml;tze frei).</dd>';
				}
			}else{
					$ret .= '<dd><input id="anmeldung_form_events[]" name="anmeldung_form_events[]" type="checkbox" value="'.$events_row->eventid;
					if(in_array($events_row->eventid,$this->in['events'])) { $ret .= ' checked="checked" '; }
					$ret .= '" /> '.$events_row->name.' (+'.number_format($events_row->charge,2,",",".").'&euro;).</dd>';
			}
		}

		if(isset($this->err['events'])) {
			$ret .= '<p class="error">'.$this->err['events'].'</p>';
		}

		$ret .= '		
				<dt><label for-id="anmeldung_form_anreise">Anreise</label></dt>
				<dd><select id="anmeldung_form_anreise" name="anmeldung_form_anreise">
					<option value="0"></option>
					<option value="2"';
					if($this->in['anreise'] == 2) { $ret .= ' selected="selected" '; }
					$ret .= '>Ich komme mit der Bahn und m&ouml;chte von euch abgeholt werden</option><option value="3"';
					if($this->in['anreise'] == 3) { $ret .= ' selected="selected" '; }
					$ret .= '>Ich komme mit dem PKW und brauche einen Parkplatz</option><option value="4"';
					if($this->in['anreise'] == 4) { $ret .= ' selected="selected" '; }
					$ret .= '>Ich komme mit dem Wohnwagen/Wohnmobil und brauche einen Stellplatz</option><option value="5"';
					if($this->in['anreise'] == 5) { $ret .= ' selected="selected" '; }
					$ret .= '>Ich komme mit dem Schiff und m&ouml;chte von euch abgeholt werden</option><option value="6"';
					if($this->in['anreise'] == 6) { $ret .= ' selected="selected" '; }
					$ret .= '>Ich komme mit dem Flugzeug und m&ouml;chte von euch abgeholt werden</option><option value="1"';
					if($this->in['anreise'] == 1) { $ret .= ' selected="selected" '; }
					$ret .= '>Ich komme irgendwie anders zum Camp</option>
				</select>
		';
		if(isset($this->err['anreise'])) {
			$ret .= '<p class="error">'.$this->err['anreise'].'</p>';
		}
		$ret .= '
				</dd>

				<dt><label for-id="anmeldung_form_ankunft">vorraussichtlicher Ankunftstag und -zeit (nur angeben, wenn du abgeholt werden willst)</label></dt>
				<dd>
					<input id="anmeldung_form_ankunft" type="text" name="anmeldung_form_ankunft" value="'.$this->in['ankunft'].'" /> 
					falls du keinen genauen Zeitpunkt festlegen kannst, oder der Zug mal wieder Versp&auml;tung hat, 
					informiere uns einfach rechtzeitig &uuml;ber das Orga-Telefon (0171 - 233 85 96).
		';
		if(isset($this->err['ankunft'])) {
			$ret .= '<p class="error">'.$this->err['ankunft'].'</p>';
		}
		$ret .= '
				</dd>

				<dt><label for-id="anmeldung_form_abfahrt">vorraussichtlicher Abfahrtstag und -zeit (nur angeben, wenn du gefahren werden willst)</label></dt>
				<dd>
					<input id="anmeldung_form_abfahrt" type="text" name="anmeldung_form_abfahrt" value="'.$this->in['abfahrt'].'" /> 
					falls du keinen genauen Zeitpunkt festlegen kannst, informiere einfach einen von uns rechtzeitig auf dem Camp 
					oder &uuml;ber das Orga-Telefon (0171 - 233 85 96).
		';
		if(isset($this->err['abfahrt'])) {
			$ret .= '<p class="error">'.$this->err['abfahrt'].'</p>';
		}
		$ret .= '
				</dd>';

				# kommt später - brauchen noch Bilder, wer kauft schon die Katze im Sack
				#<dt><label for-id="anmeldung_form_veranstaltungen[]">Veranstaltungen</label></dt>
				#<dd>
				#	<input id="anmeldung_form_veranstaltungen[]" type="checkbox" name="anmeldung_form_veranstaltungen[]" value="tux" /> 
				#	Ich m&ouml;chte Freitag das Tux-Schwimmabzeichen machen. (+??&euro;) 
				#</dd>
				#<dd>
				#	<input id="anmeldung_form_veranstaltungen[]" type="checkbox" name="anmeldung_form_veranstaltungen[]" value="flens" /> 
				#	Ich m&ouml;chte die Flensburger Brauerei besichtigen. (+??&euro;) 
				#</dd>

				#<dt><label for-id="anmeldung_form_merchandising[]">Merchandising</label></dt>
				#<dd>
				#	<input id="anmeldung_form_merchandising[]" type="checkbox" name="anmeldung_form_merchandising[]" value="tshirt" /> 
				#	Ich will das Lug-Camp 2008 T-Shirt in Gr&ouml;&szlig;e 
				#	<select id="anmeldung_form_tshirt" name="anmeldung_form_tshirt">
				#	<option></option><option>S</option><option>M</option><option>L</option><option>XL</option></select>
				#</dd>
		$ret .= '
			</dl>
			<p><input type="submit" name="anmeldung_submit" value="Ich bin dabei!" /></p>
			</form>
			</div>
			</p>
			<p>* Pflichtfelder</p>
		';
		return $ret;
	}

	function anmeldung_schritt2() {
		global $_SESSION;
		$ret = '';
		my_connect();
		# Ueberpruefung und DB eintrag

		$this->_readInput();

		$kosten			= 45;

		if($this->in['lugnew'] != '') {
			$lug_exists_SQL = "SELECT lugid FROM event_lug WHERE name='".my_escape_string($this->in['lugnew'])."'";
			$lug_exists_query = my_query($lug_exists_SQL);
			if(mysql_num_rows($lug_exists_query) == 0)
			{
				# neue Lug in DB einfuegen und id zurückbekommen
				$lugnew_SQL	= "INSERT INTO event_lug (name,crdate) VALUES ('".my_escape_string($this->in['lugnew'])."',NOW());";
				$lugnew_query	= my_query($lugnew_SQL);
				$this->in['lugid']	= my_insert_id();
			}else{
				$lugid_array	= mysql_fetch_array($lug_exists_query);
				$this->in['lugid']	= $lugid_array['lugid'];
			}
		}

		if($this->in['landnew'] != '') {
			$land_exists_SQL = "SELECT landid FROM event_land WHERE name='".my_escape_string($this->in['landnew'])."'";
			$land_exists_query = my_query($land_exists_SQL);
			if(mysql_num_rows($land_exists_query) == 0)
			{
				# neues Land in DB einfuegen und id zurückbekommen
				$landnew_SQL	= "INSERT INTO event_land (name,crdate) VALUES ('".my_escape_string($this->in['landnew'])."',NOW());";
				$landnew_query	= my_query($landnew_SQL);
				$this->in['landid']	= my_insert_id();
			}else{
				$landid_array	= mysql_fetch_array($land_exists_query);
				$this->in['landid']	= $landid_array['landid'];
			}
		}

		if(($this->in['geb_y'] == 1990 && $this->in['geb_m'] >= 4 && $this->in['geb_d'] >= 1) || ($this->in['geb_y'] > 1990)) {
			$ret .= '<p>Du bist zum LugCamp 2008 noch nicht vollj&auml;hrig! Du brauchst diese unterschriebene <a href="?p=mycamp_einverstaendniserklaerung">Einverst&auml;ndniserkl&auml;rung</a> um am Camp teilzunehmen!</p>';
		}
		
		$geb = date("Y-m-d H:i:s",mktime(0,0,0,$this->in['geb_m'],$this->in['geb_d'],$this->in['geb_y']));

		$account_sql	= "INSERT INTO account (username,passwd,email,crdate,lugid) VALUES ";
		$account_sql	.= "('".my_escape_string($this->in['nickname'])."','".md5(my_escape_string($this->in['passwort']))."','".my_escape_string($this->in['email']);
		$account_sql	.= "',NOW(),".$this->in['lugid'].");";
		$account_query	= my_query($account_sql);
		$account_id	= my_insert_id();

		if($this->in['events'] != NULL) {
			foreach($this->in['events'] as $eventid) {
				$event_anmeldung_sql = "INSERT INTO event_anmeldung_event (anmeldungid,eventid) VALUES ";
				$event_anmeldung_sql .= "('".$account_id."','".$eventid."')";
				$event_anmeldung_query = my_query($event_anmeldung_sql);
				$event_event_sql = "SELECT charge FROM event_event WHERE eventid='".$eventid."'";
				$event_event_query = my_query($event_event_sql);
				$event_event_array = mysql_fetch_array($event_event_query);
				$kosten += $event_event_array['charge'];
			}
		}

		$anmeldung_sql	= "INSERT INTO event_anmeldung (accountid,lugid,vorname,nachname,strasse,hausnr,plz,ort,landid,email,gebdat,vegetarier,arrival,ankunft,abfahrt) VALUES ";
		$anmeldung_sql .= "(".$account_id.",".$this->in['lugid'].",'".my_escape_string($this->in['vorname'])."','".my_escape_string($this->in['nachname'])."'";
		$anmeldung_sql .= ",'".my_escape_string($this->in['strasse'])."','".my_escape_string($this->in['haus'])."','".my_escape_string($this->in['plz'])."'";
		$anmeldung_sql .= ",'".my_escape_string($this->in['ort'])."','".$this->in['landid']."','".my_escape_string($this->in['email'])."'";
		$anmeldung_sql .= ",'".$geb."','".$this->in['vegetarier']."','".$this->in['anreise']."'";
		$anmeldung_sql .= ",'".my_escape_string($this->in['ankunft'])."','".my_escape_string($this->in['abfahrt'])."')";
		$anmeldung_query = my_query($anmeldung_sql);

		if(mysql_errno() == 0) {
			$code = md5($this->in['nickname']);
			if($account_id < 10) { $code .= '0'; }
			if($account_id < 100) { $code .= '0'; }
			$code .= $account_id;
			$msg = "Hallo ".$this->in['vorname'].",\n\n"."Damit deine Anmeldung zum LugCamp 2008 erfolgreich abgeschlossen werden kann,";
			$msg .= " klicke bitte auf folgenden Link:\n\n";
			$msg .= 'http://'.$_SERVER['SERVER_NAME'].'/'.$_SERVER['PATH_INFO'].'?p=anmeldung&code='.$code;
			$msg .= "\n\nDie Kontodaten zur Zahlung werden bald im Loginbereich bekanntgegeben.";
			$msg .= "\nIm Loginbereich wirst Du Zugriff auf alle Daten der Anmeldung bekommen ";
			$msg .= "\nund auch die Anmeldungen fuer Addons (LPI,T-Shirts) nachholen koennen.";
			$msg .= "\n";
			$msg .= "\nNeuigkeiten zur Webseite werden auf der Mailingliste bekanntgegeben.";
			$msg .= "\n\nWir freuen uns auf Dich\n\ndie Mitglieder der LUG Flensburg";
			
			$send_mail	= my_mailer('anmeldung@lug-camp-2008.de',my_escape_string($this->in['email']),'Anmeldung LugCamp 2008',$msg);

			$ret .= '<p>Account erfolgreich erstellt.</p><p>Du solltest jeden Moment eine Aktivierungs-Mail von uns erhalten.</p>';
			$_SESSION['_login_ok'] = 1;
		}
		return $ret;
	}

	function anmeldung_activate() {
		$ret = '';
		my_connect();
		$crypt = http_get_var("code");
		$id = intval(substr($crypt,-3));
		$code = substr($crypt,0,-3);
		$account_query = my_query("SELECT username,active FROM account WHERE accountid='".$id."'");
		$account_array = mysql_fetch_array($account_query);
		$code_real = md5($account_array[0]);
		if($account_array[1] == 1) {
			$ret .= '<p>Dein Account ist bereits aktiviert!</p>';
		}
		elseif($code == $code_real)
		{
			$activation_query = my_query("UPDATE account SET active='1' WHERE accountid='".$id."'");
			if($activation_query)
			{
				$ret .= '<p>Dein Account ist nun aktiviert!</p>';
			}
		}
		else
		{
			$ret .= '<p>Dein Aktivierungscode ist falsch!</p>';
		}
		return $ret;
	}


	function getContent() {
		# Damit die Anmeldung erst ab dem 01.01.2008 0:00:01 Uhr funktioniert
		$ret = '<h1>Anmeldung</h1>';
		#if(time() <= mktime(0,0,1,1,1,2008)) # richtiger Timestamp!
		//if(time() <= mktime(21,31,0,12,31,2007))
		if(time() <= mktime(00,00,0,01,01,2008))
    		{
			$ret .= '
				<p>	
				Hier kannst du dich ab dem 01.01.2008 0:00:01 Uhr f&uuml;r das Camp anmelden.
				</p>
			';
		}
		else
		{
			$this->_readInput();
			if($this->in['mode'] == 'speichermich') {
				my_connect();
				$this->_validateInput();
			}
			if(http_get_var("anmeldung_submit") != "")
			{
				if(count($this->err)!=0) {
					# Formular anzeigen
					$ret .= $this->anmeldung_form();
				}else{
					$ret .= $this->anmeldung_schritt2();
				}
			}
			elseif(http_get_var("code") != '')
			{
				$ret .= $this->anmeldung_activate();
			}
			else
			{
				# Formular anzeigen
				$ret .= $this->anmeldung_form();
			}
		}
		return $ret;
	}



}


?>
