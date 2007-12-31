<?php

$PAGE['prog']['name'] = "Programm";
$PAGE['prog']['navilevel'] = 1;
$PAGE['prog']['login_required'] = 0;
$PAGE['prog']['phpclass'] = 'HtmlPage_prog';
$PAGE['prog']['parent'] = 'root';

class HtmlPage_prog extends HtmlPage {

	function getContent() {
    		$ret = '
<h1>Programm</h1>

<h2>Rahmenprogramm</h2>

Mittlerweile hat es sich ja eingespielt, da&szlig; die jeweils ausrichtende LUG ihren G&auml;sten &uuml;ber das eigentliche Vortragsprogramm hinaus zus&auml;tzliche Programmpunkte anbietet. Dieser Tradition kommen wir nat&uuml;rlich auch 2008 nach:<br/><br/>

<b>Am Freitag geht es bereits los:</b><br /> 

<p>Alle Linux-Fans, die etwas auf sich halten, m&uuml;ssen unbedingt das Abzeichen &quot;Tux&quot tragen! Was ist das? Ganz einfach: Ein offizielles Schwimmabzeichen, vergleichbar dem deutschen &quot;Seepferdchen&quot;, dass zwar eigentlich "Pinguin" heisst, welches wir aber nat&uuml;rlich "Tux" nennen. Und wenn ein Abzeichen schon &quot;Tux&quot; hei&szlig;t und wir die M&ouml;glichkeit haben, diese Schwimmpr&uuml;fung ganz offiziell abzulegen, dann m&uuml;&szlig;en wir das doch einfach ausnutzen! Wir haben zu diesem Zweck extra eine Schwimmhalle angemietet, die am Freitag Nachmittag dann au&szlig;chlie&szlig;lich f&uuml;r uns zur Verf&uuml;gung steht.</p> 

<p>Daher hoffen wir (und gehen auch davon aus!), dass m&ouml;glichst viele von euch in ihrem Reisegep&auml;ck noch etwas Platz f&uuml;r ihre Badesachen finden! Jeder, der daran teilnehmen m&ouml;chte, der gibt das bitte bei der Anmeldung mit an.</p>

<p>Wer beim Schwimmen von au&szlig;en nass geworden ist, den wird es sicherlich erfreuen, wenn er anschlie&szlig;end auch von innen &quot;etwa befeuchtet&quot; wird ;)</p> 

<p>Zus&auml;tzlich zur "Tux"-Aktion wird es daher nat&uuml;rlich wieder die traditionelle Besichtigung der <a href="http://www.flens.de" target="_blank">Flensburger Brauerei</a> geben! In den 4 Jahren seit eurer letzten Besichtigung hat sich einiges getan, zumindest gibt es mittlerweile einige neue Varianten des <a href="http://www.flens.de" target="_blank">Flens</a>, die ihr unbedingt probieren solltet!</p>

<p>Und auch den 2002 zum ersten Mal durchgef&uuml;hrten &quot;Tag der offenen T&uuml;r&quot; im Rahmen eines LUG Camps wird es in Flensburg wieder geben! Durch die N&auml;he zur Stadt hoffen wir, dass viele Flensburger - und nat&uuml;rlich Besucher aus dem Umland - die Gelegenheit nutzen werden, sich &quot;bei den Cracks&quot; &uuml;ber die M&ouml;glichkeiten und F&auml;higkeiten von Linux zu informieren. Diese Aktion hat sich auf jedem Camp, auf dem sie angeboten wurde, bew&auml;hrt und daher werden wir das auf jeden Fall wieder anbieten. Es w&auml;re toll, wenn ihr uns helft, noch mehr Flensburger zu &uuml;berzeugen, sich endlich ein richtiges Betriebssystem zu installieren.</p>


<h2>Vortragsprogramm</h2>

<p><img src="images/mike/debian-vortrag-200px.jpg" align="right" style="margin-left:10px;"/>Das Vortragsprogramm lebt nat&uuml;rlich ganz stark von den Ideen der Camp-Teilnehmer, auf die wir auch diesmal wieder hoffen. Egal ob es um Hard- oder Software geht, ob ihr ein bestehendes oder ein v&ouml;llig neues Projekt vorstellen m&ouml;chtet, ihr werdet auf jeden Fall ein interessiertes Publikum finden. Und bestimmt wird es auch wieder die ber&uuml;hmt-ber&uuml;chtigten Impro-Vortr&auml;ge geben.
</p>
<p>Wenn ihr selber Vortr&auml;ge halten wollt, mailt bitte eine kurze Zusammenfassung an <b>call4paper (at) lug-camp-2008.de</b>, damit wir den Vortrag ins Programm einbauen k&ouml;nnen.</p>
<p>
Trotzdem haben auch wir als Veranstalter nat&uuml;rlich schon das ein oder andere fest eingeplant und vorbereitet.</p>

<p>So freuen wir uns, gemeinsam mit <a href="http://www.lpi-german.de" target="_blank">LPI German e.V.</a> verg&uuml;nstigte LPI-Pr&uuml;fungen (LPIC-1, LPIC-2 und LPIC-302 jeweils 70,- Euro, LPIC-301 90,- Euro) anbieten zu k&ouml;nnen! Aber nicht nur LPI sondern auch &quot;Ubuntu Professional Pr&uuml;fungen&quot; k&ouml;nnen im Rahmen des LUG Camp 2008 zu g&uuml;nstigen Konditionen abgelegt werden (UBUNTU-Member: 75,- Euro, andere Pr&uuml;flinge 85,- Euro) . Wer sich daf&uuml;r interessiert, der mu&szlig; sich daf&uuml;r nicht nur bei der Anmeldung f&uuml;r\'s LUG Camp outen, sondern speziell f&uuml;r die LPI-Pr&uuml;fung mu&szlig; man sich zus&auml;tzlich auch unter <a href="http://lpievent.lpi-german.de" target="_blank">http://lpicevent.lpi-german.de</a> direkt bei LPI anmelden. Die jeweilige Pr&uuml;fungsgeb&uuml;hr werden wir mit euer Anmeldung gleich einziehen, dann braucht ihr euch darum nicht mehr zu k&uuml;mmern!</p>

		';
		return $ret;
	}

}


?>
