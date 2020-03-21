<?php

$PAGE['start']['name'] = "Startseite";
$PAGE['start']['navilevel'] = 1;
$PAGE['start']['login_required'] = 1;
$PAGE['start']['phpclass'] = 'HtmlPage_start';
$PAGE['start']['parent'] = 'root';

class HtmlPage_start extends HtmlPage {

	var $name = "Startseite";
	var $navilevel = 1;
	var $login_required = 1;

	function __construct() {
	}
	
	function getContent() {
    		$ret = '

		';
		return $ret;
	}

}


?>
