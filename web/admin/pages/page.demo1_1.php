<?php

$PAGE['demo1_1']['name'] = "Demo 1";
$PAGE['demo1_1']['navilevel'] = 1;
$PAGE['demo1_1']['login_required'] = 1;
$PAGE['demo1_1']['phpclass'] = 'HtmlPage_demo1_1';
$PAGE['demo1_1']['parent'] = 'root';

class HtmlPage_demo1_1 extends HtmlPage {

	var $name = "Startseite";
	var $navilevel = 1;
	var $login_required = 1;

	function HtmlPage_demo1_1() {
	}
	
	function getContent() {
    		$ret = '

		';
		return $ret;
	}

}


?>
