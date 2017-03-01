<?php

/**
 * Access-Class for Events
 */
class Events  {

	const ALL_EVENTS = 1;
	const FILTER_EVENTS = 2;

	private $pdo = null;

	public function __construct($pdo) {
		$this->pdo = $pdo;
	}

	/**
	 * Get list of Events for specified domain without child-events
	 */
	public function getRootEvents($domainid) {
		$ret = array();
		try {
			$SQL = 'SELECT e.*,de.domainid FROM event_event e 
			LEFT JOIN domain_event de ON e.eventid=de.eventid
			WHERE e.hidden=0 AND e.parent IS NULL AND de.domainid=? ORDER BY e.sort';
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($domainid));
			while( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
				$ret[] = $row;
			}
			$st->closeCursor();
		} catch (PDOException $e) {
			print $e;
		}
		print_r($ret);
		return $ret;
	}

	/**
	 * Get list of all Events for specified domain
	 */
	public function getEvents($domainid,$filter = Events::FILTER_EVENTS) {
		$ret = array();
		try {
			$SQL  = 'SELECT e.*,de.domainid,COUNT(eae.anmeldungid) AS curregistrations ';
			$SQL .= ' FROM event_event e ';
			$SQL .= ' LEFT JOIN domain_event de ON e.eventid=de.eventid';
			$SQL .= ' LEFT JOIN event_anmeldung_event eae ON e.eventid=eae.eventid ';
			$SQL .= ' WHERE e.hidden=0 AND de.domainid=? ';
			if( $filter == Events::FILTER_EVENTS ) {
				$SQL .= ' AND buchanfang <= NOW() AND buchende >= NOW() ';
			}
			$SQL .= ' GROUP BY e.eventid ';
			$SQL .= ' ORDER BY e.sort';
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($domainid));
			while( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
				$ret[] = $row;
			}
			$st->closeCursor();
		} catch (PDOException $e) {
			print $e;
		}
		return $ret;
	}

	/**
	 * Get a Event by ID
	 */
	public function getEventById($eventid) {
		$ret = null;
		try {
			$SQL = 'SELECT e.*,de.domainid FROM event_event e 
			LEFT JOIN domain_event de ON e.eventid=de.eventid
			WHERE e.hidden=0 AND e.eventid=? ORDER BY e.sort';
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($eventid));
			while( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
				$ret = $row;
			}
			$st->closeCursor();
		} catch (PDOException $e) {
			print $e;
		}
		return $ret;
	}

	/**
	 * Insert new Registration in Tabele event_anmeldung and event_anmeldung_event
	 */
	public function addEventRegistration($data) {

		if( ! isset($data['bemerkung']) ) {
			$data['bemerkung'] = '';
		}
		$insert_id = null;

		// Prepare named parameters for INSERT
		$pairs = Array();
		$pairs[':accountid'] = $data['accountid'];
		$pairs[':lugid'] = $data['lugid'];
		$pairs[':vorname'] = $data['vorname'];
		$pairs[':nachname'] = $data['nachname'];
		$pairs[':strasse'] = $data['strasse'];
		$pairs[':hausnr'] = $data['haus'];
		$pairs[':plz'] = $data['plz'];
		$pairs[':ort'] = $data['ort'];
		$pairs[':landid'] = $data['landid'];
		$pairs[':email'] = $data['email'];
		$pairs[':gebdat'] = $data['geb_y'] . '-' . $data['geb_m'] . '-' . $data['geb_d'];
		$pairs[':vegetarier'] = $data['vegetarier'];
		$pairs[':arrival'] = $data['anreise'];
		$pairs[':ankunft'] = $data['ankunft'];
		$pairs[':abfahrt'] = $data['abfahrt'];
		$pairs[':bemerkung'] = $data['bemerkung'];

		try {
			// Create databasenetry with person-related registration-Data
			$SQL = 'INSERT INTO event_anmeldung (accountid,lugid,vorname,nachname,strasse,hausnr,plz,ort,landid,email,gebdat,vegetarier,arrival,ankunft,abfahrt,bemerkung,crdate) ';
			$SQL .= 'VALUES (:accountid,:lugid,:vorname,:nachname,:strasse,:hausnr,:plz,:ort,:landid,:email,DATE(:gebdat),:vegetarier,:arrival,:ankunft,:abfahrt,:bemerkung,NOW()) ';
			$st = $this->pdo->prepare($SQL);
			$st->execute($pairs);
			$insert_id = $this->pdo->lastInsertId();
			$st->closeCursor();
		} catch (PDOException $e) {
			print $e;
		}

		if( $insert_id != null && isset($data['events']) && is_array($data['events']) ) {
			// register the new person with each selected event...
			try {
				$SQL = 'INSERT INTO event_anmeldung_event (anmeldungid,eventid,accountid) VALUES (?,?,?)';
				$st = $this->pdo->prepare($SQL);
				foreach( $data['events'] as $eventid ) {
					$st->execute( array($insert_id,$eventid,$data['accountid']) );
				}
				$st->closeCursor();
			} catch ( PDOException $e ) {
				print $e;
			}
		}
		return $insert_id;
	}

	public function getEventRegistrationsForAccount($accountid, $domainid) {
		$ret = array();

		try{
			// first fetch all registered persons for the account
			$SQL = 'SELECT ea.anmeldungid, ea.vorname, ea.nachname '
				.'FROM event_anmeldung ea '
				.'LEFT JOIN event_anmeldung_event eae ON ea.anmeldungid=eae.anmeldungid '
				.'LEFT JOIN domain_event de ON eae.eventid=de.eventid '
				.'WHERE ea.accountid = ? AND de.domainid=?';
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($accountid,$domainid));
			while( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
				$ret[] = $row;
			}
			$st->closeCursor();

			foreach($ret as &$person) {
				$SQL = 'SELECT eae.bezahlt, ee.name as eventname, ee.charge as eventkosten '
					.'FROM event_anmeldung_event eae '
					.'LEFT JOIN event_event ee ON ee.eventid = eae.eventid '
					.'LEFT JOIN domain_event de ON de.eventid = eae.eventid '
					.'WHERE de.domainid = ? AND eae.anmeldungid = ?';
				$st = $this->pdo->prepare($SQL);
				$st->execute(array($domainid,$person['anmeldungid']));
				while( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
					$person['EVENTS'][] = $row;
				}
				$st->closeCursor();
			}
		} catch (PDOException $e) {
			print $e;
		}

		return $ret;
	}

	public function getEventRegistration($anmeldungid, $account_id=null) {
		$ret = array();

		try{
			// get the registration data
			$SQL = 'SELECT ea.vorname, ea.nachname, ea.strasse, ea.hausnr, ea.plz, ea.ort, ea.land, '
				.'a.email, DATE_FORMAT(ea.gebdat,"%e.%c.%Y") as gebdat, ea.vegetarier, el.name as lugname, '
				.'ea.arrival,ea.bemerkung '
				.'FROM event_anmeldung ea '
				.'LEFT JOIN event_lug el ON el.lugid = ea.lugid '
				.'LEFT JOIN account a ON ea.accountid=a.accountid '
				.'WHERE ea.anmeldungid = ? ';
			if($account_id)
				$SQL .= 'AND ea.accountid = ? ';
			$st = $this->pdo->prepare($SQL);
			if($account_id)
				$st->execute(array($anmeldungid, $account_id));
			else
				$st->execute(array($anmeldungid));
			$ret['ANMELDUNG'] = $st->fetch(PDO::FETCH_ASSOC);
			$st->closeCursor();

			// get the events for which the user is registered
			$SQL = 'SELECT ee.name, ee.charge '
				.'FROM event_anmeldung_event eae '
				.'LEFT JOIN event_event ee ON ee.eventid = eae.eventid '
				.'WHERE eae.anmeldungid = ? ';
			if($account_id)
				$SQL .= 'AND ea.accountid = ? ';
			$st = $this->pdo->prepare($SQL);
			if($account_id)
				$st->execute(array($anmeldungid, $account_id));
			else
				$st->execute(array($anmeldungid));
			while($row = $st->fetch(PDO::FETCH_ASSOC)) {
				$ret['EVENTS'][] = $row;
			}
			$st->closeCursor();
		

		} catch (PDOException $e) {
			print $e;
		}

		return $ret;
	}

	public function getBoughtArtikelForAccount($accountid, $domainid = null) {
		$ret = array();

		try{
			$SQL = 'SELECT ea.name, eaa.groesse, eaa.anzahl, (eaa.anzahl*ea.preis) as kosten, eaa.bezahlt '
				.'FROM event_account_artikel eaa '
				.'LEFT JOIN event_artikel ea ON ea.artikelid = eaa.artikelid '
				.'LEFT JOIN domain_artikel da ON da.artikelid = eaa.artikelid '
				.'WHERE eaa.accountid = ? ';

			$ORDERBY = ' ORDER BY ea.name,eaa.groesse ';
			if($domainid != null) {
				$SQL .= 'AND da.domainid = ? ' . $ORDERBY;
				$st = $this->pdo->prepare($SQL);
				$st->execute(array($accountid,$domainid));
			}else{
				$SQL .= $ORDERBY;
				$st = $this->pdo->prepare($SQL);
				$st->execute(array($accountid));
			}
			while( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
				$ret[] = $row;
			}
			$st->closeCursor();
		} catch (PDOException $e) {
			print $e;
		}

		return $ret;
	}

	public function getEventRegistrations($eventid) {
		$ret = array();
		try {
			$SQL = 'SELECT ea.vorname, ea.nachname, ea.strasse, ea.hausnr, ea.plz, ea.ort, ea.land, '
				.'a.email, DATE_FORMAT(ea.gebdat,"%e.%c.%Y") as gebdat, ea.vegetarier, el.name as lugname, '
				.'ea.arrival,ea.bemerkung,a.username,a.accountid '
				.'FROM event_anmeldung ea '
				.'LEFT JOIN event_lug el ON el.lugid = ea.lugid '
				.'LEFT JOIN event_anmeldung_event eae ON eae.anmeldungid=ea.anmeldungid '
				.'LEFT JOIN account a ON eae.accountid=a.accountid '
				.'WHERE eae.eventid = ? '
				.'ORDER BY ea.vorname, ea.nachname ';
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($eventid));
			while( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
				$ret[] = $row;
			}
			$st->closeCursor();
		} catch( PDOException $e) {
			print $e;
		}
		return $ret;
	}

	public function getEventRegistrationComments($eventid) {
		$ret = array();
		try {
			$SQL = 'SELECT ea.vorname, ea.nachname, '
				.'DATE_FORMAT(ea.crdate,"%e.%c.%Y") as crdate, el.name as lugname, '
				.'ea.bemerkung,a.username,a.accountid '
				.'FROM event_anmeldung ea '
				.'LEFT JOIN event_lug el ON el.lugid = ea.lugid '
				.'LEFT JOIN event_anmeldung_event eae ON eae.anmeldungid=ea.anmeldungid '
				.'LEFT JOIN account a ON eae.accountid=a.accountid '
				.'WHERE eae.eventid = ? AND ea.bemerkung IS NOT NULL '
				.'ORDER BY ea.crdate ';
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($eventid));
			while( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
				$ret[] = $row;
			}
			$st->closeCursor();
		} catch( PDOException $e) {
			print $e;
		}
		return $ret;
	}
}

?>
