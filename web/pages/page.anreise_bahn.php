<?php

$PAGE['anreise_bahn']['name'] = "Bahn";
$PAGE['anreise_bahn']['navilevel'] = 2;
$PAGE['anreise_bahn']['login_required'] = 0;
$PAGE['anreise_bahn']['phpclass'] = 'HtmlPage_anreise_bahn';
$PAGE['anreise_bahn']['parent'] = 'anreise';

class HtmlPage_anreise_bahn extends HtmlPage {

	function getContent() {
    		$ret = '

<h1>Anreise mit der Bahn</h1>
<p><img src="./images/mike/anreise-bahn-200px.jpg" title="Anreise mit der Bahn" align="left" style="margin-right:10px;"/>
Wenn ihr mit der Bahn anreist, dann fahrt ihr bis zum Bahnhof Flensburg. Teilt uns dann bitte auf jeden Fall mit, wann euer Zug in Flensburg eintreffen soll, damit wir euch mit dem von <a href="http://www.nehrkorn-autohaeuser.de" target="_blank">Nehrkorn Automobile</a> zur Verf&uuml;gung gestellten Bus abholen und zum Camp bringen k&ouml;nnen!</p>

<!-- TODO ###Nummer f&uuml; die Shuttle bestellung### -->
		';
		return $ret;
	}

}


?>
