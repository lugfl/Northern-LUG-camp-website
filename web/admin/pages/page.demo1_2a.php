<?php

$PAGE['demo1_2a']['name'] = "Demo 1 a";
$PAGE['demo1_2a']['navilevel'] = 2;
$PAGE['demo1_2a']['login_required'] = 1;
$PAGE['demo1_2a']['phpclass'] = 'HtmlPage_demo1_2a';
$PAGE['demo1_2a']['parent'] = 'demo1_1';

class HtmlPage_demo1_2a extends HtmlPage {

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
