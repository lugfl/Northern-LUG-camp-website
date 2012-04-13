<?php

require_once('lib/func.http_get_var.php');
require_once('lib/class.Plugin.php');
require_once('lib/class.Artikel.php');

class Plugin_Artikels extends Plugin {

	private $artikel = null;
	private $site = null;

	private $smarty_assign = array();
	private $artikelliste = array();
	private $basket = array();

	private $in_artikelid = 0;
	private $in_groesse = 0;
	private $mode = "";

	function __construct($pdo,$page,$site) {
		parent::__construct($pdo,$page);
		$this->smarty_assign['PAGEID'] = $this->page['pageid'];
		$this->artikel = new Artikel($pdo,$page['domainid']);
		$this->site = $site;

		// create shoplist
		$tmp = $this->artikel->getArtikels();
		for( $i=0; $i < count($tmp); $i++) {
			if( isset($tmp[$i]['groessen']) ) {
				$arr = array();
				$arr = explode(',',$tmp[$i]['groessen']);
				$tmp[$i]['groessen'] = array();
				foreach( $arr as $a) {
					$tmp[$i]['groessen'][] = trim($a);
				}
			}
		}
		$this->artikelliste = $tmp;
		if( isset($_SESSION['basket']) ) {
			$this->basket = $_SESSION['basket'];
		}
	}

	public function readInput() {
		$tmp = http_get_var('artikelid');
		if( is_numeric($tmp) ) {
			$this->in_artikelid = $tmp;
		}
		$this->in_groesse = http_get_var('groesse');
		
		$this->mode = http_get_var('m');
		
	}

	public function processInput() {
		$this->smarty_assign['ARTIKELLISTE'] = $this->artikelliste;

		switch($this->mode) {
			case "kaufe":
				// Artikel in den Warenkorb
				if( $this->in_artikelid != 0) {
					// Artikel suchen
					$art = $this->getCachedArtikel($this->in_artikelid);
					if( $art != null && is_array($art['groessen']) ) {
						
						// Groesse checken
						if( in_array($this->in_groesse,$art['groessen']) ) {
							// Warenkorb aktualieren
							if( $this->getRequestMethod() == Plugin::METHOD_GET || $this->getRequestMethod() == Plugin::METHOD_POST) {
								$this->addToBasket($art,$this->in_groesse,1);
							}
							
						}
					} else {
						trigger_error('Ups');
					}
				}
				break;
			case "del":
				// Artikel aus Warenkorb loeschen
				if( $this->in_artikelid != 0) {
					// Artikel suchen
					$art = $this->getCachedArtikel($this->in_artikelid);
					if( $art != null && is_array($art['groessen']) ) {
						
						// Groesse checken
						if( in_array($this->in_groesse,$art['groessen'],TRUE) ) {
							// Warenkorb aktualieren
							if( $this->getRequestMethod() == Plugin::METHOD_GET || $this->getRequestMethod() == Plugin::METHOD_POST) {
								$this->delFromBasket($art,$this->in_groesse,1);
							}
						} else {
						}
					} else {
						trigger_error('Ups');
					}
				}
				break;
			case "commit":
				// Bestellung abschicken
				break;
		}
		$this->recalcBasket();
		$_SESSION['basket'] = $this->basket;
		if( count($this->basket) > 0 ) {
			$this->smarty_assign['BASKET'] = $this->basket;
			$this->smarty_assign['BASKET_preis'] = $_SESSION['basket_preis'];
		}
	}

	private function addToBasket($art,$groesse,$anz) {
		$this->addDebug('addToBasket('.$art['artikelid'].','.$groesse.','.$anz.')');

		$item = array( 'artikel' => $art, 'groesse' => $groesse, 'anzahl' => $anz );
		$item['preis'] = $art['preis'] * $anz;
		$replpos = -1;
		for( $i=0; $i < count($this->basket); $i++) {
			if( $this->basket[$i]['artikel']['artikelid'] == $art['artikelid'] ) {
				// Artikel gefunden
				if( $this->basket[$i]['groesse'] == $groesse ) {
					//Groesse gefunden
					$replpos = $i;
					$item['anzahl'] = $this->basket[$i]['anzahl'] + $anz;
					$this->basket[$i] = $item;
				}
			}
		}
		if( $replpos < 0 ) {
			// neu hinzufuegen
			$this->basket[] = $item;
		}
	}

	private function delFromBasket($art,$groesse,$anz) {
		$this->addDebug('delFromBasket('.$art['artikelid'].','.$groesse.','.$anz.')');
		// Artikel aus Warenkorb loeschen
		$retry = 0;
		foreach($this->basket as $k => $v) {
			if( isset($v['artikel']) ) {
				if( $v['artikel']['artikelid'] == $art['artikelid'] ) {
					// Artikel gefunden
					if( $v['groesse'] == $groesse ) {
						//Groesse gefunden
						$a = $v['anzahl'] - $anz;
						if( $a <= 0 ) {
							$this->addDebug('Removing ' . $k . ' from basket');
							$this->basket[$k] = null;
							unset( $this->basket[$k]);
							return 0;
						} else {
							$this->basket[$k]['anzahl'] = $a;
						}
					}
				}
			} else {
			}
		} // foreach

	}

	private function recalcBasket() {
		if( ! isset($_SESSION['basket']) )
			return;

		$gespreis = 0.00;
		for( $i=0; $i < count($this->basket); $i++) {
			//Preis pro Item
			if( isset($this->basket[$i]['artikel']) ) {
				$p = $this->basket[$i]['artikel']['preis'] * $this->basket[$i]['anzahl'];
				$this->basket[$i]['preis'] = $p;
				$gespreis += $p;
			}
		}
		$_SESSION['basket_preis'] = $gespreis;

	}

	private function getCachedArtikel($artikelid) {
		$ret = null;
		for( $i=0; $i < count($this->artikelliste); $i++) {
			if( $this->artikelliste[$i]['artikelid'] == $artikelid ) {
				$ret = $this->artikelliste[$i];
			}
		}
		return $ret;
	}

	public function getOutputMethod()
	{
		return Plugin::OUTPUT_METHOD_SMARTY;
	}

	/**
	 * @return Filename of Smarty-Template.
	*/
	public function getSmartyTemplate()
	{
		return 'page.artikel.html';
	}

	public function getSmartyVariables()
	{
		return $this->smarty_assign;
	}

	public function getAdminNavigation() {
		$ret = parent::getAdminNavigation();
		return $ret;
	}
}
?>
