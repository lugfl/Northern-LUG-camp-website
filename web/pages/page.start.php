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
<img src="bilder/camp-cup.jpg" alt="LUG-CAMP Cup" style="margin-left:300px">

		';
		return $ret;
	}

}


?>
