<?php

$PAGE['loc_flensburg']['name'] = "Flensburg";
$PAGE['loc_flensburg']['navilevel'] = 2;
$PAGE['loc_flensburg']['login_required'] = 0;
$PAGE['loc_flensburg']['phpclass'] = 'HtmlPage_loc_flensburg';
$PAGE['loc_flensburg']['parent'] = 'loc';

class HtmlPage_loc_flensburg extends HtmlPage {

	function getContent() {
    		$ret = '

<h1>Ums Sportland herum</h1>


<h2>Die Stadt Flensburg:</h2>
<p>Nein, Flensburg liegt immer noch nicht an der Nordsee, auch wenn unsere &quot;Schweizer Helden&quot; das im Jahr 2002 meinten herausgefunden zu haben. Flensburg liegt tats&auml;chlich und immer noch an der Ostsee, genauer gesagt im Bereich der sogenannten &quot;D&auml;nischen S&uuml;dsee&quot;, am Ende der Flensburger F&ouml;rde! Zur Nordsee kommt man, wenn man vom Sportland aus ca. 40 Kilometer Richtung Westen f&auml;hrt, aber keinesfalls, wenn man in den Aktiv-Bus einsteigt, der direkt vor dem Sportland abf&auml;hrt ;)</p>


<h2>Was ist los in Flensburg?</h2>
<p>Nun, es gibt tats&auml;chlich ein maritimes Gro&szlig;ereignis, welches in Flensburg jedes Jahr zum Camp-Termin mit dem LUG Camp konkurriert: Die <a href="http://www.rumregatta.de/" target="_blank">Flensburger Rum-Regatta!</a></p>

<p><img src="images/mike/flensburg-innenhafen-2-200px.jpg" align="right" style="margin-left:10px;"/>Bei der <a href="http://www.rumregatta.de/" target="_blank">Rum-Regatta</a> k&ouml;nnt ihr die ganzen historischen Segler, die im <a href="http://www.museumshafen-flensburg.de/" target="_blank">Flensburger Museumshafen</a> gehegt und gepflegt werden, mal &quot;live in action&quot; erleben, da sind die n&auml;lich alle segelnd unterwegs. Ein umfangreiches Rahmenprogramm direkt am Innenhafen versucht ebenfalls, euch vom Camp weg in die Stadt hineinzulocken. Ob das gelingen wird? Wenn ja, dann zumindest nicht f&uuml;r lange, denn auch das LUG Camp 2008 hat f&uuml;r Dich wieder eine Menge zu '.htmlpage_link('prog','bieten').'!</p>


<h2>Die Riesen-Baustelle</h2>
<p>
Leider ist die gesamte Flensburger Innenstadt zur Zeit (oder besser seit l&auml;ngerem)  eine einzige Gro&szlig;baustelle <FOTO>. Da wird gebuddelt und gebaggert und es macht - zumindest uns Flensburgern - mittlerweile wirklich keinen Spass mehr, einen Innenstadtbummel zu unternehmen. Aber als Alternative bietet sich euch wirklich der Bereich um den Innenhafen an: Dort k&ouml;nnt ihr,  sofern ihr einmal den Weg aus dem Sportland hinaus findet, sicherlich eine ganze Menge entdecken und erleben!</p>


<h2>Wie kommt ihr in die Stadt?</h2>
<p>
Ganz einfach! Dank unseres Sponsors Nehrkorn Automobile #www.nehrkorn-autohaeuser.de# steht uns f&uuml;r das gesamte LUG Camp ein 9-Sitzer-Bus zur Verf&uuml;gung, mit dem wir - sofern ihr mit der Bahn anreist - euch nicht nur vom Bahnhof abholen werden sondern dar&uuml;ber hinaus f&uuml;r alle Camp-Teilnehmer einen Shuttle-Service vom Camp zum Hafen / zur Innenstadt realisieren m&ouml;chten und werden. 
D.h. zu festen Zeiten werden wir vom LUG Camp aus Richtung Innenstadt fahren und zu festen Zeiten werden wir euch an bestimmten Punkten in der Stadt wieder abholen! Das erspart denjenigen von euch, die sich auch auf die touristischen Aspekte eines LUG Camp\'s einlassen, im Zweifelsfall eine Menge Taxikosten und Fragen nach dem richtigen Weg zur&uuml;ck ;)
</p>
		';
		return $ret;
	}

}


?>
