<?php

$PAGE['demo2']['name'] = "Demo 2";
$PAGE['demo2']['navilevel'] = 1;
$PAGE['demo2']['login_required'] = 1;
$PAGE['demo2']['phpclass'] = 'HtmlPage_demo2';
$PAGE['demo2']['parent'] = 'root';

class HtmlPage_demo2 extends HtmlPage {

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
