<?php
/**
 * Access-Class for Artikel
 */

class Artikel {


	private $pdo = null;
	private $domainid = null;

	public function __construct($pdo,$domainid) {
		$this->pdo = $pdo;
		$this->domainid = $domainid;
	}

	public function getArtikels() {
		$ret = array();
		try {
			$SQL  = 'SELECT a.* FROM event_artikel a ';
			$SQL .= ' LEFT JOIN domain_event de ON a.eventid=de.eventid ';
			$SQL .= ' WHERE de.domainid=? ';
			$SQL .= ' AND a.kaufab <= NOW() AND a.kaufbis >= NOW() ';
			$SQL .= ' ORDER BY a.name';
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($this->domainid));
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
	 * Order Article for User
	 *
	 * 
	 * @return insert-ID, if succesfully ardered
	 */
	public function orderArtikel($_artikelid,$_anzahl,$_groesse,$_accountid) {

		$ret = 0;
		try {
			$p = Array();
			$p['artikelid'] = $_artikelid;
			$p['anzahl'] = $_anzahl;
			$p['groesse'] = $_groesse;
			$p['accountid'] = $_accountid;
			$SQL = 'INSERT INTO event_account_artikel (artikelid,anzahl,groesse,accountid,crdate) ';
			$SQL .= ' VALUES (:artikelid,:anzahl,:groesse,:accountid,NOW())';
			$st = $this->pdo->prepare($SQL);
			$st->execute($p);
			$ret = $this->pdo->lastInsertId();
			$st->closeCursor();
		} catch (PDOException $e) {
			print $e;
		}
		return $ret;
	}
}

?>
