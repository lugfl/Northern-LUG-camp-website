<?php

$PAGE['anreise_taxi']['name'] = "Taxi";
$PAGE['anreise_taxi']['navilevel'] = 2;
$PAGE['anreise_taxi']['login_required'] = 0;
$PAGE['anreise_taxi']['phpclass'] = 'HtmlPage_anreise_taxi';
$PAGE['anreise_taxi']['parent'] = 'anreise';

class HtmlPage_anreise_taxi extends HtmlPage {

	function getContent() {
    		$ret = '
<h1>Anreise mit dem Taxi</h1>
Ruft eurer Taxi oder Funkmietwagen des Vertrauens an. Sagt ihm das Reiseziel, je nach Startort, solltet ihr warten bis der Fahrer aufgeh&ouml;rt hat zu lachen, und geniesst die Fahrt.<br>
<br>
<b>Wir m&ouml;chten aber darauf hinweisen, dass euch je nach Startort eine Rechnung von mehreren Hundert Euro erwarten kann und dass wir <u><b>NICHT</b></u> die Rechnung bezahlen werden. Also nicht vergessen Geld holen, sehr viel Geld! ;).<br></b>

		';
		return $ret;
	}

}


?>
