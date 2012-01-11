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
 * mode: lar  (List All Registrations)
 *
 * Template-Blocks ($this->smarty_assign['events_block']:
 *  - events_registration_successfull
 *  - events_login_required
 */
class Plugin_Events extends Plugin {

	public static $ANREISE = array(
		array('anreiseid' => 2, 'name' => 'Ich komme mit der Bahn und m&ouml;chte von euch abgeholt werden'),
		array('anreiseid' => 3, 'name' => 'Ich komme mit dem PKW und brauche einen Parkplatz'),
		array('anreiseid' => 4, 'name' => 'Ich komme mit dem Wohnwagen/Wohnmobil und brauche einen Stellplatz'),
		array('anreiseid' => 5, 'name' => 'Ich komme mit dem Schiff und m&ouml;chte von euch abgeholt werden'),
		array('anreiseid' => 6, 'name' => 'Ich komme mit dem Flugzeug und m&ouml;chte von euch abgeholt werden'),
		array('anreiseid' => 1, 'name' => 'Ich komme irgendwie anders zum Camp'),
	);
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
		'events',
		'bemerkung'
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
						if( $registration_id != null ) {
							$this->smarty_assign['events_block'] = 'events_registration_successfull';
							// TODO sendConfirmMail()
						}
					}
					break;
				case "lar":
					// list all registrations
					if( isset($this->in['eventid']) && is_numeric($this->in['eventid']) ) {
						// event selected, show registrations for this event
						$reg = $this->events->getEventRegistrations($this->in['eventid']);
						$this->smarty_assign['events_block'] = 'events_admin_list_registrations';
						$this->smarty_assign['events_registrations'] = $reg;
						$this->smarty_assign['events_data'] = $this->events->getEventById($this->in['eventid']);
					} else {
						// no event selected, show event-selector
						$this->smarty_assign['events_block'] = 'events_admin_selector';
						$eventlist = $this->events->getEvents($this->domain['domainid'],Events::ALL_EVENTS);
						$this->smarty_assign['events_list'] = $eventlist;
						$this->smarty_assign['p'] = $this->p;
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
		$this->smarty_assign['anreise_list'] = self::$ANREISE;
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

		$this->in['eventid'] = http_get_var('eventid'); // used for m=lar

		$aid = $this->site->getMyAccountID();
		if($aid != null) {
			$this->in['accountid'] = $aid;
		}

	}

	protected function validateInput() {
	
		if(!preg_match('/^[[:alpha:] -]{3,40}$/iu',$this->in['vorname'])) {
			// Fehler im Vornamen
			$this->err['vorname'] = 'Dieser Vorname enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (3-40 Zeichen)';
		}

		if(!preg_match('/^[[:alpha:] -]{3,40}$/iu',$this->in['nachname'])) {
			// Fehler im Nachnamen
			$this->err['nachname'] = 'Dieser Nachname enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (3-40 Zeichen)';
		}

		if(!preg_match('/^[[:alpha:]. -]{3,40}$/iu',$this->in['strasse'])) {
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

		if(!preg_match('/^[[:alpha:]0-9 -]{3,40}$/iu',$this->in['ort'])) {
			// Fehler im Ort
			$this->err['ort'] = 'Dieser Ort enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (3-40 Zeichen)';
		}

		if($this->in['landid'] == 0)
		{
			if(!preg_match('/^[[:alpha:].:,; -]{3,50}$/iu',$this->in['landnew'])) {
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

	private function hasErrors() {
		$ctr = sizeof($this->err);
		if( $ctr > 0 )
			return TRUE;
		else
			return FALSE;
	}

	public function getAdminNavigation() {
		$ret = array();
		$ret[] = array(
			'pageid' => $this->page['pageid'],
			'title' => 'List all Registrations',
			'url' => '?p=' . $this->page['pageid'] . '&mode=lar'
		);
		return $ret;
	}

}


?>
