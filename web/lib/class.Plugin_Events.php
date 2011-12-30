<?php

require_once('lib/func.pear_mail.php');
require_once('Mail/RFC822.php');
require_once('lib/class.Plugin.php');
require_once('lib/class.Lugs.php');
require_once('lib/class.Countries.php');
require_once('lib/class.Events.php');

/**
 * Register for a new event.
 *
 * mode: save
 *
 * Template-Blocks ($this->smarty_assign['events_block']:
 *  - events_registration_successfull
 *  - events_login_required
 */
class Plugin_Events extends Plugin {

	// Inputdaten
	private $in = Array();

	// Error
	private $err = Array();


	private $pdo = null;
	private $page = null;
	private $domain = null;
	private $events = null;
	private $site = null;
	private $lugs = null;
	private $countries = null;

	private $p = '';

	private $smarty_assign = array();

	private $ALLOWED_FORM_FIELDS = array(
		'passwort',
		'passwort2',
		'email',
		'vorname',
		'nachname',
		'strasse',
		'haus',
		'plz',
		'ort',
		'landid',
		'landnew',
		'geb_d',
		'geb_m',
		'geb_y',
		'lugid',
		'lugnew',
		'vegetarier',
		'events',
		'anreise',
		'ankunft',
		'abfahrt',
		'events'
	);

	function __construct($pdo,$page,$domain,$site) {
		$this->pdo = &$pdo;
		$this->page = &$page;
		$this->domain = &$domain;
		$this->site = &$site;
		$this->events = new Events($this->pdo);
		$this->lugs = new Lugs($this->pdo);
		$this->countries = new Countries($this->pdo);
	}

	public function processInput() {

		if ( ! $this->site->auth_ok() ) {
			$this->smarty_assign['events_block'] = 'events_login_required';
		} else {
			switch( $this->in['mode'] ) {
				case "save":
					// form send. Validate Input
					$this->validateInput();

					if($this->in['lugnew'] != '') {
						$lug = $this->lugs->searchOrAdd($this->in['lugnew']);
						if( $lug != null ) {
							$this->in['lugid'] = $lug['lugid'];
							unset( $this->in['lugnew'] );
						}
					}

					if(isset($this->in['landnew']) && $this->in['landnew'] != '') {
						$land = $this->countries->searchOrAdd($this->in['landnew']);
						if( $land != null ) {
							$this->in['landid']	= $land['landid'];
							unset( $this->in['landnew'] );
						}
					}

					if( $this->hasErrors() ) {
						// display form again
						$this->prepareSmartyForm();
					} else {
						// Save Registration
						$registration_id = $this->events->addEventRegistration($this->in);
					}
					break;
				default:
					$this->prepareSmartyForm();
					break;
			}

		} // else if ! auth_ok
	}

	private function prepareSmartyForm() {
		// Dispplay Event Form
		$this->smarty_assign['lugs'] = $this->lugs->getLugs();
		$this->smarty_assign['countries'] = $this->countries->getCountries();
		$this->smarty_assign['events_list'] = $this->events->getEvents($this->domain['domainid']);
		$this->smarty_assign['events_block'] = 'events_form';
		$this->smarty_assign['p'] = $this->p;
		foreach( $this->ALLOWED_FORM_FIELDS as $f) {
			if( isset($this->in[$f]) ) {
				$this->smarty_assign[$f] = $this->in[$f];
			}
		}
		if( $this->hasErrors() ) {
			foreach( $this->err as $key => $val) {
				$this->smarty_assign['err_'.$key] = $val;
			}
		}
	}

	public function readInput() {
		$this->in['mode'] = http_get_var('mode');
		$this->p = http_get_var('p');

		foreach( $this->ALLOWED_FORM_FIELDS as $f ) {
			$this->in[$f] = http_get_var($f);
		}
		if($this->in['events'] == '') { 
			$this->in['events'] = array();
		}
		if( isset($this->in['bemerkung']) ) {
			$this->in['bemerkung'] = strip_tags($this->in['bemerkung']);
		}

		$aid = $this->site->getMyAccountID();
		if($aid != null) {
			$this->in['accountid'] = $aid;
		}

	}

	protected function validateInput() {
	
		if(!preg_match('/^[[:alpha:]-]{3,40}$/i',$this->in['vorname'])) {
			// Fehler im Vornamen
			$this->err['vorname'] = 'Dieser Vorname enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (3-40 Zeichen)';
		}

		if(!preg_match('/^[[:alpha:]-]{3,40}$/i',$this->in['nachname'])) {
			// Fehler im Nachnamen
			$this->err['nachname'] = 'Dieser Nachname enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (3-40 Zeichen)';
		}

		if(!preg_match('/^[[:alpha:]. -]{3,40}$/i',$this->in['strasse'])) {
			// Fehler in der Strasse
			$this->err['strasse'] = 'Dieser Stra&szlig;enname enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (3-40 Zeichen)';
		}

		if(!preg_match('/^[[:alpha:] a-z0-9]{1,5}$/i',$this->in['haus'])) {
			// Fehler in der Hausnummer
			$this->err['haus'] = 'Diese Hausnummer enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (1-5 Zeichen)';
		}

		if(!preg_match('/^[[:alpha:]0-9.: -]{3,10}$/i',$this->in['plz'])) {
			// Fehler in der PLZ
			$this->err['plz'] = 'Diese Postleitzahl enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (3-10 Zeichen)';
		}

		if(!preg_match('/^[[:alpha:]0-9 -]{3,40}$/i',$this->in['ort'])) {
			// Fehler im Ort
			$this->err['ort'] = 'Dieser Ort enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (3-40 Zeichen)';
		}

		if($this->in['landid'] == 0)
		{
			if(!preg_match('/^[[:alpha:].:,; -]{3,50}$/i',$this->in['landnew'])) {
				// Fehler im neu angegebenen Land
				$this->err['land'] = 'Du hast kein Land angebeben oder der Name enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (3-40 Zeichen)';
			}
			// Wenn der Name bereits in der DB existiert wird später einfach die existierende ID übernommen, die Überprüfung dafür erfolgt in schritt2
		}else{
			// Wenn ein Land ausgewählt ist, ein neu angegebenes ignorieren
			$this->in['landnew'] = '';
		}

		$geb_err = '';
		if( !is_numeric($this->in['geb_d']) || $this->in['geb_d'] > 31 || $this->in['geb_d'] < 1 ) {
			$geb_err .= 'Der Tag ist nicht richtig angegeben.';
		}
		if(!is_numeric($this->in['geb_m']) || $this->in['geb_m'] > 12 || $this->in['geb_m'] < 1 ) {
			$geb_err .= 'Der Monat ist nicht richtig angegeben.';
		}
		if(!is_numeric($this->in['geb_y']) || $this->in['geb_y'] > 2010 || $this->in['geb_y'] < 1900 ) {
			$geb_err .= 'Das Jahr ist nicht richtig angegeben.';
		}

		if($geb_err != '') { 
			$this->err['geb'] = $geb_err;
		} else {
			// Create unixtimestamp from birthday inputfields
			$this->in['geb'] = mktime(0,0,0,$this->in['geb_m'],$this->in['geb_d'],$this->in['geb_y']);
		}

		if($this->in['lugid'] == 0)
		{
			if(!preg_match('/^[[:alpha:]0-9.:,; -]{3,50}$/i',$this->in['lugnew'])) {
				// Fehler in der neu angegebenen Lug
				$this->err['lug'] = 'Du hast keine LUG angebeben oder der Name enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (3-40 Zeichen)';
			}
			// Wenn der Name bereits in der DB existiert wird später einfach die existierende ID übernommen, die Überprüfung dafür erfolgt in schritt2
		}else{
			// Wenn eine LUG ausgewählt ist, eine neu angegebene ignorieren
			$this->in['lugnew'] = '';
		}

		if($this->in['vegetarier'] != 1) { $this->in['vegetarier'] = 0; }

		// Check for required Main-Event and do some cleanup with Input-Trash in Event-Array...
		$events_clenup = array();
		if( isset($this->in['events']) && is_array($this->in['events']) && sizeof($this->in['events'])>0 ) {
			$min_1_mainevent_selected = FALSE;
			foreach( $this->in['events'] as $ev) {
				if( is_numeric($ev) ) {
					$e = $this->events->getEventById($ev);
					if( $e != null ) {
						$events_clenup[] = $ev;
						if( ! isset($e['parent']) || !is_numeric($e['parent']) ) {
							$min_1_mainevent_selected = TRUE;
						}
					}
				}
			}
			if( ! $min_1_mainevent_selected ) {
				$this->err['events'] = 'Du musst Dich f&uuml;r mindestens eine Haupt-Veranstaltung anmelden.';
			}
		} else {
			$this->err['events'] = 'Du musst Dich f&uuml;r mindestens eine Veranstaltung anmelden.';
		}
		$this->in['events'] = $events_clenup; // now Event-Array is a array with 100% integer values
	}

	protected function anmeldung_form() {
		global $CURRENT_EVENT_ID;

		// Feststellen fuer welches Event wir grade anmelden
		if(!isset($CURRENT_EVENT_ID) || !is_numeric($CURRENT_EVENT_ID))
			$ceventid = 0;
		else
			$ceventid = $CURRENT_EVENT_ID;

		$ret = '';
		//my_connect();
		# Formular zur Anmeldung ausgeben

		$ret .= '
				</dd>
				<h3>Optionale Angaben</h3>



				<dt><label for-id="anmeldung_form_events[]">Veranstaltungen</label></dt>
				<dd>Ich m&ouml;chte an folgenden Veranstaltungen teilnehmen.</dd>';
/* TODO
		$events_query = my_query("SELECT * FROM event_event WHERE hidden=0 AND (eventid='".$ceventid."' OR parent='".$ceventid."') ORDER BY parent,name");
		$ret .= '<input type="hidden" name="anmeldung_form_events[]" value="'.$ceventid.'" />';
		while($events_row = mysql_fetch_object($events_query)) {
			$quota_ret		= '';
			$ret .= '<dd><input id="anmeldung_form_events[]" name="anmeldung_form_events[]" type="checkbox" value="'.$events_row->eventid.'"';
			# Hauptevent ?
			if($events_row->eventid == $ceventid) {
				$ret .= ' disabled="disabled" checked="checked"';
			}
			# Schonmal angehakt ?
			elseif(in_array($events_row->eventid,$this->in['events'])) {
				$ret .= ' checked="checked"';
			}
			# Teilnehmerbegrenzung ?
			if($events_row->quota) {
				try {
				$event_quota_SQL	= "SELECT COUNT(anmeldungid) AS ctr FROM event_anmeldung_event WHERE eventid='".$events_row->eventid."'";
				$event_quota_query	= my_query($event_quota_SQL);
				$event_member		= mysql_num_rows($event_quota_query);
				# noch Plätze frei ?
				if(($events_row->quota-$event_member) > 0) {
					$quota_ret = 'noch '.($events_row->quota-$event_member).' Pl&auml;tze frei';
				# Keine Plätze mehr frei !
				}else{
					$ret .= ' disabled="disabled"';
					$quota_ret = 'leider schon ausgebucht';
				}
			}
			# Name
			$ret .= ' /> '.$events_row->name.' ';

			if($events_row->charge || $quota_ret) { $ret .= '('; }
			# Kosten ?
			if($events_row->charge) {
				$ret .= number_format($events_row->charge,2,",",".").'&euro;';
				# Wenn Kosten und Quota gesetzt sind wird ein "und" dazwischengesetzt
				if($quota_ret != '') {
					$ret .= ' und ';
				}
			}
			# wie viele Plätze sind noch frei ?
			$ret .= $quota_ret;
			if($events_row->charge || $quota_ret) { $ret .= ')'; }

			$ret .= '</dd>';
		}
*/
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

				<dt><label for-id="anmeldung_form_ankunft">voraussichtlicher Ankunftstag und -zeit (nur angeben, wenn du abgeholt werden willst)</label></dt>
				<dd>
					<input id="anmeldung_form_ankunft" type="text" name="anmeldung_form_ankunft" value="'.$this->in['ankunft'].'" /> 
					falls du keinen genauen Zeitpunkt festlegen kannst, oder der Zug mal wieder Versp&auml;tung hat, 
					informiere uns einfach rechtzeitig &uuml;ber das Orga-Telefon (0151-21020311).
		';
		if(isset($this->err['ankunft'])) {
			$ret .= '<p class="error">'.$this->err['ankunft'].'</p>';
		}
		$ret .= '
				</dd>

				<dt><label for-id="anmeldung_form_abfahrt">voraussichtlicher Abfahrtstag und -zeit (nur angeben, wenn du gefahren werden willst)</label></dt>
				<dd>
					<input id="anmeldung_form_abfahrt" type="text" name="anmeldung_form_abfahrt" value="'.$this->in['abfahrt'].'" /> 
					falls du keinen genauen Zeitpunkt festlegen kannst, informiere einfach einen von uns rechtzeitig auf dem Camp 
					oder &uuml;ber das Orga-Telefon (0151-21020311).
		';
		if(isset($this->err['abfahrt'])) {
			$ret .= '<p class="error">'.$this->err['abfahrt'].'</p>';
		}
		$ret .= '
				</dd>';

		$ret .= '
			</dl>
			<h3>Bemerkung</h3>
			<p>Hast Du noch eine Bemerkung zur Anmeldung? Kein Problem...</p>
			<p><textarea name="anmeldung_form_bemerkung" rows="5" cols="70">'.$this->in['bemerkung'].'</textarea>
			</p>
			<p><input type="submit" name="anmeldung_submit" value="Ich bin dabei!" /></p>
			</form>
			</div>
			</p>
			<p>* Pflichtfelder</p>
		';
		return $ret;
	}

	protected function anmeldung_schritt2() {
		$ret = '';
		my_connect();
		# Ueberpruefung und DB eintrag

		//$this->_readInput();

		$kosten			= 45;


		if(($this->in['geb_y'] == 1990 && $this->in['geb_m'] >= 4 && $this->in['geb_d'] >= 1) || ($this->in['geb_y'] > 1990)) {
			$ret .= '<p>Du bist zum LugCamp 2008 noch nicht vollj&auml;hrig! Du brauchst diese unterschriebene <a href="?p=mycamp_einverstaendniserklaerung">Einverst&auml;ndniserkl&auml;rung</a> um am Camp teilzunehmen!</p>';
		}
		
		$geb = date("Y-m-d H:i:s",mktime(0,0,0,$this->in['geb_m'],$this->in['geb_d'],$this->in['geb_y']));

/*
		$account_sql	= "INSERT INTO account (username,passwd,email,crdate,lugid) VALUES ";
		$account_sql	.= "('".my_escape_string($this->in['nickname'])."',MD5('".my_escape_string($this->in['passwort'])."'),'".my_escape_string($this->in['email']);
		$account_sql	.= "',NOW(),".$this->in['lugid'].");";
		$account_query	= my_query($account_sql);
		$account_id	= my_insert_id();
*/
		// TODO account_id aus Session auslesen

		// Liste der Daten zusammenstellen
		$pairs = Array();
		if(is_numeric($account_id))
			array_push($pairs,'accountid='.$account_id);
		array_push($pairs,'lugid='.$this->in['lugid']);
		array_push($pairs,"vorname='".my_escape_string($this->in['vorname'])."'");
		array_push($pairs,"nachname='".my_escape_string($this->in['nachname'])."'");
		array_push($pairs,"strasse='".my_escape_string($this->in['strasse'])."'");
		array_push($pairs,"hausnr='".my_escape_string($this->in['haus'])."'");
		array_push($pairs,"plz='".my_escape_string($this->in['plz'])."'");
		array_push($pairs,"ort='".my_escape_string($this->in['ort'])."'");
		array_push($pairs,'landid='.$this->in['landid']);
		array_push($pairs,"email='".my_escape_string($this->in['email'])."'");
		array_push($pairs,"gebdat='".$geb."'");
		array_push($pairs,'vegetarier='.$this->in['vegetarier']);
		array_push($pairs,'arrival='.$this->in['anreise']);
		array_push($pairs,"ankunft='".my_escape_string($this->in['ankunft'])."'");
		array_push($pairs,"abfahrt='".my_escape_string($this->in['abfahrt'])."'");
		if(strlen($this->in['bemerkung'])!=0)
			array_push($pairs,"bemerkung='".$this->in['bemerkung']."'");

		$anmeldung_sql = "INSERT INTO event_anmeldung SET ".join(",",$pairs);
		$anmeldung_query = my_query($anmeldung_sql);
		$anmeldung_id = my_insert_id();

		if($this->in['events'] != NULL) {
			foreach($this->in['events'] as $eventid) {
				$event_anmeldung_sql = "INSERT INTO event_anmeldung_event (anmeldungid,eventid) VALUES ";
				$event_anmeldung_sql .= "('".$anmeldung_id."','".$eventid."')";
				$event_anmeldung_query = my_query($event_anmeldung_sql);
				$event_event_sql = "SELECT charge FROM event_event WHERE eventid='".$eventid."'";
				$event_event_query = my_query($event_event_sql);
				$event_event_array = mysql_fetch_array($event_event_query);
				$kosten += $event_event_array['charge'];
			}
		}

		if(mysql_errno() == 0) {

			if(strlen($this->in['bemerkung'])!=0) {
				// Da hat einer was Bemerkt
				$infotxt  = "Moin moin,\n\n";
				$infotxt .= $this->in['vorname']." ".$this->in['nachname'] . " (".$this->in['nickname'].") hat da was bemerkt...\n\n";
				$infotxt .= strip_tags($this->in['bemerkung']);
				my_mailer('anmeldung@lug-camp-2008.de','anmeldung@lug-camp-2008.de','lc2008-Anmeldung: Bemerkungsinfo',$infotxt);
				
			}
			$this->smarty_assign['events_block'] = 'events_registration_successfull';
		}
		return $ret;
	}

	protected function anmeldung_activate() {
		$ret = '';
		my_connect();
		$crypt = http_get_var("code");
		$id = intval(substr($crypt,-3));
		$code = substr($crypt,0,-3);
		$account_query = my_query("SELECT username,active,acl FROM account WHERE accountid='".$id."'");
		$account_array = mysql_fetch_array($account_query);
		$code_real = md5($account_array[0]);
		# Passt der Code zur ID / richtiger Code ?
		if($code == $code_real)
		{
			if($account_array[1] == 1) {
				$ret .= '<p>Dein Account ist bereits aktiviert!</p>';
			}else{
				$activation_query = my_query("UPDATE account SET active='1' WHERE accountid='".$id."'");
				if($activation_query)
				{
					$ret .= '<p>Dein Account ist nun aktiviert!</p>';
				}
			}
		}
		else
		{
			$ret .= '<p>Dein Aktivierungscode ist falsch!</p>';
		}
		return $ret;
	}


	public function getOutputMethod() {
		return Plugin::OUTPUT_METHOD_SMARTY;
	}

	/**
	 * @return Filename of Smarty-Template.
	 */
	public function getSmartyTemplate() {
		return 'page.events.html';
	}

	/**
	 * @return Data for Smarty::assign()
	 */
	public function getSmartyVariables() {
		return $this->smarty_assign;
	}

	public function getOutput() {

		// Checken, ob die Seite wegen Wartungsarbeiten ausgeschaltet werden soll.
		// Funktion checkMaintenance() kommt aus class.HtmlPage.php
		$ret = $this->checkMaintenance();
		if($ret == TRUE)
			return 'Diese Seite ist z.Zt. im Wartungsmodus. Versuche es bitte sp&auml;ter noch einmal.';

		# Damit die Anmeldung erst ab dem 01.01.2008 0:00:01 Uhr funktioniert
		$ret .= '<h1>Anmeldung</h1>';
		#if(time() <= mktime(0,0,1,1,1,2008)) # richtiger Timestamp!
		//if(time() <= mktime(21,31,0,12,31,2007))
		if(time() <= mktime(00,00,0,01,01,2008))
    		{
			$ret .= '
				<p>	
				Hier kannst Du Dich ab dem 01.01.2008 0:00:01 Uhr f&uuml;r das Camp anmelden.
				</p>
			';
		}
		else
		{
			if($this->in['mode'] == 'save') {
				my_connect();
				$this->validateInput();
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


	private function hasErrors() {
		$ctr = sizeof($this->err);
		if( $ctr > 0 )
			return TRUE;
		else
			return FALSE;
	}


}


?>
