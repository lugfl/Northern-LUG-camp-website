<?php

$PAGE['prog_lpi']['name'] = "LPI-Pr&uuml;fung";
$PAGE['prog_lpi']['navilevel'] = 2;
$PAGE['prog_lpi']['login_required'] = 0;
$PAGE['prog_lpi']['phpclass'] = 'HtmlPage_prog_lpi';
$PAGE['prog_lpi']['parent'] = 'prog';

class HtmlPage_prog_lpi extends HtmlPage {

	function getContent() {
    		$ret = '
		<h1>LPI-Pr&uuml;fungen</h1>
		<p>TODO</p>
		';
		return $ret;
	}

}


?>
