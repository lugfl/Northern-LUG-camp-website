<?php

$PAGE['mycamp']['name'] = "MyCamp";
$PAGE['mycamp']['navilevel'] = 1;
$PAGE['mycamp']['login_required'] = 1;
$PAGE['mycamp']['phpclass'] = 'HtmlPage_mycamp';
$PAGE['mycamp']['parent'] = 'root';
$PAGE['mycamp']['hidden'] = 0;

class HtmlPage_mycamp extends HtmlPage {

	function getContent() {

		// Checken, ob die Seite wegen Wartungsarbeiten ausgeschaltet werden soll.
		// Funktion checkMaintenance() kommt aus class.HtmlPage.php
		$ret = $this->checkMaintenance();
		if($ret!='')
			return $ret;

    		$ret = '
			<h1>MyCamp</h1>
			<p>
			In diesem Bereich kannst Du Deine Anmeldedaten &uuml;berarbeiten, also T-Shirt-Bestellung, Anmeldung f&uuml;r Besichtigungen, f&uuml;r\'s Schwimmen, und so weiter.  
			</p>
		';
		return $ret;
	}

}


?>
