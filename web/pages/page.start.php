<?php

$PAGE['start']['name'] = "Startseite";
$PAGE['start']['navilevel'] = 1;
$PAGE['start']['login_required'] = 0;
$PAGE['start']['phpclass'] = 'HtmlPage_start';
$PAGE['start']['parent'] = 'root';

class HtmlPage_start extends HtmlPage {

	function getContent() {
    		$ret = '
Herzlich willkommen zum

<h1>LUG Camp 2008</h1>

Das mittlerweile 9. LUG Camp wird 2008 bereits zum dritten Mal durch die Linux User Group Flensburg e.V. ausgerichtet!<br />

Und wenn wir etwas <b>aus</b>richten, dann richtet ihr es doch einfach <b>ein</b>, dass ihr vom<br />

<h2>1. bis 4. Mai 2008</h2>

im <a href="http://www.sportland-flensburg.de">Sportland Flensburg</a> an diesem Event teilnehmen werdet und dass ihr der &Uuml;bergabe des "LUG Camp-Cup" beiwohnt!<br/><br/>
<img src="bilder/camp-cup.jpg" alt="LUG-CAMP Cup" style="margin-left:0px" align="left" hspace="10" vspace="10">

<h1>NEWS: Termin und Lokation LUG Camp 2009</h1>

Die Veranstalter des LUG-Camps 2009 haben die Bombe platzen lassen. Das Camp findet vom 21.-24.05.2009 im <a href="http://www.kleiter.de/Seiten/Unsere_Haeuser/Gschwender-Hof.php" target="_blank">Gschwender Hof</a> statt. Weitere Details findet Ihr in ein paar Tagen auf der Webseite <a href="http://www.lug-camp.de">lug-camp.de</a> sobald die Webseite online gegangen ist.
		';
		return $ret;
	}

}


?>
