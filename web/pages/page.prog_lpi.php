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
		<p>Die folgenden Pr&uuml;fungen werden jeweils um 09:00 Uhr und um 11:00 Uhr am Sa. 03.05. angeboten:</p>
		<img src="./images/lpi-tux-3-lowres.jpg" align="right"  style="margin-right:10px;"/>
		<ul>
			<li>LPI-101 (deutsch oder englisch), 70 &euro;</li>
			<li>LPI-102 (deutsch oder englisch), 70 &euro;</li>
			<li>LPI-201 (deutsch oder englisch), 70 &euro;</li>
			<li>LPI-202 (deutsch oder englisch), 70 &euro;</li>
			<li>LPI-301 (nur englisch), 90 &euro;</li>
			<li>LPI-302 (nur englisch), 70 &euro;</li>
			<li>Ubuntu-LPI-199 (nur englisch), 85 &euro; (f&uuml;r UBUNTU Member 75 &euro;)</li>
		</ul>

		<p>
			F&uuml;r die beiden Pr&uuml;fungstermine gelten die Pr&uuml;fungsbedingungen des LPI. Jeder Teilnehmer muss sich vor dem Event unter <a href="http://lpievent.lpi-german.de/" target="_blank">lpievent.lpi-german.de</a> anmelden. 
		</p>

		';
		return $ret;
	}

}


?>
