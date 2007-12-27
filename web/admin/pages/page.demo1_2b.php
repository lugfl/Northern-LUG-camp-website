<?php

$PAGE['demo1_2b']['name'] = "Demo 1 b";
$PAGE['demo1_2b']['navilevel'] = 2;
$PAGE['demo1_2b']['login_required'] = 1;
$PAGE['demo1_2b']['phpclass'] = 'HtmlPage_demo1_2b';
$PAGE['demo1_2b']['parent'] = 'demo1_1';

class HtmlPage_demo1_2b extends HtmlPage {

	var $name = "Startseite";
	var $navilevel = 1;
	var $login_required = 1;

	function HtmlPage_demo1_2b() {
	}
	
	function getContent() {
    		$ret = '

		';
		return $ret;
	}

}


?>
