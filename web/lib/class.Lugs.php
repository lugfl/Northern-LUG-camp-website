<?php

/**
 * Access-Class for Lugs
 */
class Lugs {

	private $pdo = null;

	private $lugs = array();

	public function __construct($pdo) {
		$this->pdo = $pdo;
		$this->loadLugs();
	}

	public function getLugs() {
		return $this->lugs;
	}

	public function loadLugs() {
		// clear current LUG-List
		$this->lugs = array();

		// load lugs from database
		try {
			$SQL = 'SELECT lugid,name,abk FROM event_lug ORDER BY name';
			$st = $this->pdo->prepare($SQL);
			$st->execute();
			while($row = $st->fetch(PDO::FETCH_ASSOC)) {
				$this->lugs[] = $row;
			}
			$st->closeCursor();
		} catch( PDOException $e) {
			print $e;
		}
	}

	/**
	 * Search a LUG or create a new DB-Entry
	 */
	public function searchOrAdd($name) {
		$ret = null;
		foreach( $this->lugs as $lug ) {
			if( $lug['abk'] == $name or $lug['name'] == $name ) {
				$ret = $lug;
			}
		}
		if( $ret == null ) {
			// no Entry found
			try {
				$SQL = 'INSERT INTO event_lug (name,crdate) VALUES (?,NOW())';
				$st = $this->pdo->prepare($SQL);
				$st->execute( array($name) );
				$st->closeCursor();
				$this->loadLugs(); // List Modified; reload it
				$ret = $this->SearchOrAdd($name);
			} catch (PDOException $p) {
				print $e;
			}
		}
		return $ret;
	}
}

?>
