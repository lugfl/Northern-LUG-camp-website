<?php

$PAGE['anreise_schiff']['name'] = "Schiff";
$PAGE['anreise_schiff']['navilevel'] = 2;
$PAGE['anreise_schiff']['login_required'] = 0;
$PAGE['anreise_schiff']['phpclass'] = 'HtmlPage_anreise_schiff';
$PAGE['anreise_schiff']['parent'] = 'anreise';

class HtmlPage_anreise_schiff extends HtmlPage {

	function getContent() {
    		$ret = '
<h1>Schiff</h1>

<p>Wenn ihr mit dem Schiff anreist, dann m&uuml;&szlig;t ihr euch erstmal durch die dichte Phalanx der Traditionsschiffe durchw&uuml;hlen, die im Rahmen der <a href="http://www.rumregatta.de" target="_blank">Rum-Regatta</a> die Flensburger F&ouml;rde unsicher machen. Wenn ihr das geschafft habt, dann sucht ihr euch im Innenhafen einen sch&ouml;nen Liegeplatz und ruft uns &uuml;ber das Orga-Telefon (0171 - 233 85 96) an, damit wir euch dort abholen und zum LUG Camp bringen k&ouml;nnen.

		';
		return $ret;
	}

}


?>
