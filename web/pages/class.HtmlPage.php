<?php

$PAGE['root']['name'] = "Home";
$PAGE['root']['navilevel'] = 0;
$PAGE['root']['login_required'] = 0;
$PAGE['root']['phpclass'] = 'HtmlPage';
$PAGE['root']['parent'] = '_';

class HtmlPage {

	/**
	 * Meta-Header fuer diese Seite
	 */
	function getMeta() {
		return "";
	}
	
	/**
	 * hauptcontent fuer diese Seite
	 */
	function getContent() {
		return "";
	}

	/**
	 * Checken, ob die Seite im Wartungsmodus ist und den Defaulttext fuer die Seite generieren
	 *
	 * @param string Seitentitel, der als H1 angezeigt wird
	 * @return mixed HTML-Code der Wartungsinfo, ansonsten ""
	 */
	function checkMaintenance($pagetitle = 'Wartungsarbeiten') {
		global $MAINTENANCE_MODE;
		$ret = '';
		if(isset($MAINTENANCE_MODE) && $MAINTENANCE_MODE) {
			$ret = '
				<h1>'.$pagetitle.'</h1>
				<p>
					Die Seite ist momentan wegen Wartungsarbeiten leider nicht verf&uuml;gbar. Versuch es doch bitte sp&auml;ter noch mal.
				</p>
			';
		}
		return $ret;
	}
}

?>
