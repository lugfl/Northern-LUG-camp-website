<?php

$PAGE['lugcamp']['name'] = "LUG-Camp";
$PAGE['lugcamp']['navilevel'] = 1;
$PAGE['lugcamp']['login_required'] = 0;
$PAGE['lugcamp']['phpclass'] = 'HtmlPage_lugcamp';
$PAGE['lugcamp']['parent'] = 'root';

class HtmlPage_lugcamp extends HtmlPage {

	function getContent() {
    		$ret = '
<h1>Das LUG Camp</h1>

<p>
Das LUG Camp ist ein Treffen von Linux User Groups und Linux-Freunden aus dem gesamten deutschsprachigen Raum. Die &quot;Erfinder&quot; sind die <a href="http://www.lugal.de" target="_blank">LUG Allg&auml;u</a> und die <a href="http://www.luga.de" target="_blank">LUG Augsburg</a>, die seither nat&uuml;rlich auch bei jedem LUG Camp dabei waren.
</p>

<p>
Genaueres erfahrt ihr - war ja klar - in der <a href="http://de.wikipedia.org/wiki/LUG-Camp" target="blank">Wikipedia</a> und im <a href="http://www.lug-camp-howto.de" target="blank">LUG Camp-Howto</a>, einer Zusammenstellung vieler Hinweise, Tips und Erfahrungen der Ausrichter und der Teilnehmer der bisherigen LUG Camps.
</p>

<p>Wenn ihr immer auf dem Laufenden bleiben wollt, was das aktuelle Camp und auch die Folgenden angeht, meldet euch doch einfach an der <a href="http://lists.lugcamp.org/cgi-bin/mailman/listinfo/teilnehmer">Camp Mailingliste</a> an. Neu ist ab diesem Jahr, das diese Liste nicht nur f&uuml;r 2008 gilt, sondern f&uuml;r die Folgejahre aktiv sein wird.
</p>

<p>
Auf jeden Fall ist das Camp ein Ereignis, dass Du auf keinen Fall verpassen solltest!
</p>


		';
		return $ret;
	}

}


?>
