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
}

?>
