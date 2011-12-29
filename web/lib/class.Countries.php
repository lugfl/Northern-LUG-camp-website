<?php

/**
 * Access class for Countries.
 */
class Countries {

	private $pdo = null;

	private $countries = array();

	public function __construct($pdo) {
		$this->pdo = $pdo;
		$this->loadCountries();
	}

	/**
	 * Get List of countries
	 */
	public function getCountries() {
		return $this->countries;
	}

	/**
	 * Load Countrylist from database.
	 *
	 * current cached list ist cleared before.
	 */
	public function loadCountries() {
		// clear current Country-List
		$this->countries = array();

		// load countries from database
		try {
			$SQL = 'SELECT landid,name FROM event_land ORDER BY name';
			$st = $this->pdo->prepare($SQL);
			$st->execute();
			while($row = $st->fetch(PDO::FETCH_ASSOC)) {
				$this->countries[] = $row;
			}
			$st->closeCursor();
		} catch( PDOException $e) {
			print $e;
		}
	}

	/**
	 * Search a Country or create a new DB-Entry, if not exists.
	 */
	public function searchOrAdd($name) {
		$ret = null;
		foreach( $this->countries as $c ) {
			if( $c['name'] == $name ) {
				$ret = $c;
			}
		}
		if( $ret == null ) {
			// no Entry found
			try {
				$SQL = 'INSERT INTO event_land (name,crdate) VALUES (?,NOW())';
				$st = $this->pdo->prepare($SQL);
				$st->execute( array($name) );
				$st->closeCursor();
				$this->loadCountries(); // List Modified; reload it
				$ret = $this->SearchOrAdd($name);
			} catch (PDOException $p) {
				print $e;
			}
		}
		return $ret;
	}
}

?>
