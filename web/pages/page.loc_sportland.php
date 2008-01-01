<?php

$PAGE['loc_sportland']['name'] = "Sportland";
$PAGE['loc_sportland']['navilevel'] = 2;
$PAGE['loc_sportland']['login_required'] = 0;
$PAGE['loc_sportland']['phpclass'] = 'HtmlPage_loc_sportland';
$PAGE['loc_sportland']['parent'] = 'loc';

class HtmlPage_loc_sportland extends HtmlPage {

	function getContent() {
    		$ret = '

<h1>Das Sportland:</h1>

<p><img src="images/loc_sportland01_200x160.jpg" align="left" style="markgin-right:10px"/>Das LUG Camp 2008 findet an bew&auml;hrter Stelle im <a href="http://www.sportland-flensburg.de" target="blank">Sportland-Flensburg</a> statt. Die von der LUG Flensburg organisierte ehemalige Tennishalle bietet ausreichend Platz f&uuml;r bis zu 250 Teilnehmern. 

<p>Schlafen k&ouml;nnt ihr also sowohl direkt bei euren PC\'s als auch wie gewohnt im abgetrennten, hinteren Teil der Halle, alternativ dazu nun auch in euren mitgebrachten Zelten (allerdings wieder nur in begrenzter St&uuml;ckzahl).
 Und wer meint, er m&ouml;chte aus guter alter Flensburger Tradition heraus sein Zelt in der Halle aufschlagen, auch der wird das wohl wieder durchf&uuml;hren k&ouml;nnen, denn Platz sollte ausreichend vorhanden sein.</p>

<p>Uns stehen dar&uuml;ber hinaus wie gewohnt die sanit&auml;ren Anlagen des Sportlandes zur Verf&uuml;gung, saubere, &quot;richtige&quot; Toiletten und vern&uuml;nftige Duschen sind also in ausreichender Anzahl verf&uuml;gbar! Ausserdem wurde mit den Betreibern des Sportlandes vereinbart, dass LUG Camp-Teilnehmer, wenn sie denn m&ouml;chten, gegen eine Pauschale von 6,- &euro; pro Mann/Frau und Tag die dort vorhandene Sauna und das Schwimmbad mitbenutzen d&uuml;rfen.</p>
		';
		return $ret;
	}

}


?>
