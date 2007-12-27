<?php

$PAGE['mycamp_anmeldung']['name'] = "Anmeldung";
$PAGE['mycamp_anmeldung']['navilevel'] = 2;
$PAGE['mycamp_anmeldung']['login_required'] = 0;
$PAGE['mycamp_anmeldung']['phpclass'] = 'HtmlPage_mycamp_anmeldung';
$PAGE['mycamp_anmeldung']['parent'] = 'mycamp';

class HtmlPage_mycamp_anmeldung extends HtmlPage {

	function user_angemeldet() { # $user
		# in der DB prüfen ob benutzer bereits angemeldet ist
		$ret = 0;
		#$user_query = my_query("SELECT * FROM account WHERE nickname = '$user'");
		return $ret;
	}

	# durch $user kann die Funktion auch für eine Übersicht verwendet werden
	function anmeldung_status($user) {
		# welchen Status hat die anmeldung ? (Alles OK, noch nicht bezahlt, Anmeldung unvollständig, Einverständnis fehlt)
		$ret .= '<p>Status:</p>';
		return $ret;
	}

	function anmeldung_form() {
		# Formular zur Anmeldung ausgeben
		$ret .= '
			<p>
			<div id="anmeldung_form">
			<form action="./index.php?p=mycamp_anmeldung" method="post">
			<dl>

				<dt><label for-id="anmeldung_form_nickname">Account-/Nickname*</label></dt>
				<dd><input id="anmeldung_form_nickname" type="text" name="anmeldung_form_nickname" /></dd>

				<dt><label for-id="anmeldung_form_passwort">Accountpasswort*</label></dt>
				<dd><input id="anmeldung_form_passwort" type="password" name="anmeldung_form_paswort" /></dd>

				<dt><label for-id="anmeldung_form_email">E-Mail*</label></dt>
				<dd><input id="anmeldung_form_email" type="text" name="anmeldung_form_email" size="30" /></dd>

				<dt><label for-id="anmeldung_form_vorname">Vorname*</label></dt>
				<dd><input id="anmeldung_form_vorname" type="text" name="anmeldung_form_vorname" /></dd>

				<dt><label for-id="anmeldung_form_nachname">Nachname*</label></dt>
				<dd><input id="anmeldung_form_nachname" type="text" name="anmeldung_form_nachname" /></dd>

				<dt><label for-id="anmeldung_form_strasse">Stra&szlig;e und Hausnummer*</label></dt>
				<dd>
					<input id="anmeldung_form_strasse" type="text" name="anmeldung_form_strasse" />
					<input id="anmeldung_form_haus" type="text" name="anmeldung_form_haus" size="4" />
				</dd>

				<dt><label for-id="anmeldung_form_plz">PLZ und Ort*</label></dt>
				<dd>
					<input id="anmeldung_form_plz" type="text" name="anmeldung_form_plz" size="8" />
					<input id="anmeldung_form_ort" type="text" name="anmeldung_form_ort" />
				</dd>

				<dt><label for-id="anmeldung_form_land">Land*</label></dt>
				<dd><input id="anmeldung_form_land" type="text" name="anmeldung_form_land" size="2" value="DE" maxlength="2" /></dd>

				<dt><label for-id="anmeldung_form_geb">Geburtsdatum (d.m.Y)*</label></dt>
				<dd><input id="anmeldung_form_geb" type="text" name="anmeldung_form_geb" size="10" /></dd>

				<!-- Alles aus der Datenbank aufbauen... --/>

				<dt><label for-id="anmeldung_form_lug">LUG</label></dt>
				<dd>
					<select id="anmeldung_form_lug" name="anmeldung_form_lug">
					<option></option>';
		$lugs_query = mysql_query("SELECT * FROM event_lug;"); # my_query() funktioniert nicht...
		while($lugs_row = mysql_fetch_object($lugs_query))
		{
			$ret .= '<option value="'.$lugs_row->lugid.'">'.$lugs_row->name.'</option>';
		}
		$ret .= '
					</select> ausw&auml;hlen oder neue LUG anlegen 
					<input id="anmeldung_form_lugnew" type="text" name="anmeldung_form_lugnew" size="30" />
				</dd>';

				# kommt später
				#<dt><label for-id="anmeldung_form_essen[]">Essen</label></dt>
				#<dd><input id="anmeldung_form_essen[]" type="checkbox" name="anmeldung_form_essen[]" value="grill"> Ich nehme die Grillflatrate in Anspruch</dd>
				#<dd><input id="anmeldung_form_essen[]" type="checkbox" name="anmeldung_form_essen[]" value="kaffe"> Ich nehme die Kaffeeflatrate in Anspruch</dd>
				#<dd><input id="anmeldung_form_essen[]" type="checkbox" name="anmeldung_form_essen[]" value="selbst"> Ich bin Selbstversorger</dd>
				#<dd><input id="anmeldung_form_essen[]" type="checkbox" name="anmeldung_form_essen[]" value="vegetarier"> Ich bin Vegetarier/Veganer</dd>
		
		$ret .= '
				<dt><label for-id="anmeldung_form_anreise">Anreise</label></dt>
				<dd><select id="anmeldung_form_anreise" name="anmeldung_form_anreise">
					<option></option>
					<option value="bahn">Ich komme mit der Bahn und m&ouml;chte von euch abgeholt werden</option>
					<option value="pkw">Ich komme mit dem PKW und brauche einen Parkplatz</option>
					<option value="wohnwagen">Ich komme mit dem Wohnwagen/Wohnmobil und brauche einen Stellplatz</option>
					<option value="schiff">Ich komme mit dem Schiff und m&ouml;chte von euch abgeholt werden</option>
					<option value="flug">Ich komme mit dem Flugzeug und m&ouml;chte von euch abgeholt werden</option>
					<option value="sonstige">Ich komme irgendwie anders zum Camp</option>
				</select></dd>

				<dt><label for-id="anmeldung_form_ankunft">vorraussichtlicher Ankunftstag und -zeit (f&uuml;r Abholung)</label></dt>
				<dd>
					<input id="anmeldung_form_ankunft" type="text" name="anmeldung_form_ankunft" /> 
					falls du keinen genauen Zeitpunkt festlegen kannst, oder der Zug mal wieder Versp&auml;tung hat, 
					informiere uns einfach rechtzeitig &uuml;ber das Orga-Telefon (0171 - 233 85 96)
				</dd>';

				# kommt später
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
		# Ueberpruefung und DB eintrag

		$nickname		= http_get_var("anmeldung_form_nickname");
		$passwort		= md5(http_get_var("anmeldung_form_passwort"));
		$email			= http_get_var("anmeldung_form_email");
		$vorname		= http_get_var("anmeldung_form_vorname");
		$nachname		= http_get_var("anmeldung_form_nachname");
		$strasse		= http_get_var("anmeldung_form_strasse");
		$haus			= http_get_var("anmeldung_form_haus");
		$plz			= http_get_var("anmeldung_form_plz");
		$ort			= http_get_var("anmeldung_form_ort");
		$land			= http_get_var("anmeldung_form_land");
		$geb			= http_get_var("anmeldung_form_geb");
		$lugid			= http_get_var("anmeldung_form_lug");
		$lugnew			= http_get_var("anmeldung_form_lugnew");
		$anreise		= http_get_var("anmeldung_form_anreise");
		$ankunft		= http_get_var("anmeldung_form_ankunft");
		# Eingaben muessen noch auf Exploitversuche ueberprueft werden... eventuell in der http_get_var funktion ?

		$crdate			= date("Y-m-d H:i:s",time());

		if($lugnew != '') {
			# neue Lug in DB einfuegen und id zurückbekommen
			$lugnew_query	= mysql_query("INSERT INTO event_lug (name,crdate) VALUES ('".$lugnew."','".$crdate."');");
			$lug_query	= mysql_query("SELECT LAST_INSERT_ID() FROM event_lug;");
			$lug_array	= mysql_fetch_array($lug_query);
			$lugid		= $lug_array[0];
		}

		$ret_vorher = $ret;
		switch('') {
			case $nickname:
				$ret .= '<p>Gib bitte deinen Accountnamen an!</p>';
				break;
			case $passwort:
				$ret .= '<p>Gib bitte dein Passwort an!</p>';
				break;
			case $email:
				$ret .= '<p>Gib bitte deine E-Mailadresse an!</p>';
				break;
			case $vorname:
				$ret .= '<p>Gib bitte deinen Vornamen an!</p>';
				break;
			case $nachname:
				$ret .= '<p>Gib bitte deinen Nachnamen an!</p>';
				break;
			case $strasse:
				$ret .= '<p>Gib bitte deine Stra&szlig;e an!</p>';
				break;
			case $haus:
				$ret .= '<p>Gib bitte deine Hausnummer an!</p>';
				break;
			case $plz:
				$ret .= '<p>Gib bitte deine Postleitzahl an!</p>';
				break;
			case $ort:
				$ret .= '<p>Gib bitte deinen Ort an!</p>';
				break;
			case $land:
				$ret .= '<p>Gib bitte dein Land an!</p>';
				break;
			case $geb:
				$ret .= '<p>Gib bitte dein Geburtsdatum an!</p>';
				break;
		}
		if($ret == $ret_vorher)
		{
			$account_sql	= "INSERT INTO account (username,passwd,email,crdate,lugid) VALUES ";
			$account_sql	.= "('".$nickname."','".$passwort."','".$email."','".$crdate."','".$lugid."');";
			$account_query	= mysql_query($account_sql);
			if(mysql_errno() == 0) {
				$ret .= '<p>Account erfolgreich erstellt.</p>';
				$_SESSION['_login_ok'] = 1;
			}
		}

		return $ret;
	}

	function getContent() {
		# Damit die Anmeldung erst ab dem 01.01.2008 0:00:01 Uhr funktioniert
		$ret = '<h1>Anmeldung</h1>';
		# if(time() <= 1199142001) # Richtiger Timestamp !
		if(time() <= 1190040001) # Zum testen ;)
    		{
			$ret .= '
				<p>	
				Hier kannst du dich ab dem 01.01.2008 0:00:01 Uhr f&uuml;r das Camp anmelden.
				</p>
			';
		}
		else
		{
			# Anmeldung ist freigeschaltet, bereits angemeldet ? -> Status, sonst Formular
			if(http_get_var("anmeldung_submit") == "Ich bin dabei!")
			{
				$ret .= $this->anmeldung_schritt2();

			}
			elseif($this->user_angemeldet()) # angemeldet ?
			{
				$ret .= 'Status:'; # editieren, etc.
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
