<?php

$PAGE['anreise_auto']['name'] = "PKW";
$PAGE['anreise_auto']['navilevel'] = 2;
$PAGE['anreise_auto']['login_required'] = 0;
$PAGE['anreise_auto']['phpclass'] = 'HtmlPage_anreise_auto';
$PAGE['anreise_auto']['parent'] = 'anreise';

class HtmlPage_anreise_auto extends HtmlPage {

	function getContent() {
    		$ret = '
<h1>Anreise mit dem Auto</h1>
<p>Wenn ihr euch auf die lange Reise mit dem Auto macht, dann findet ihr uns auch ganz einfach:</p>

<p>&Uuml;ber die Autobahn A7 fahrt ihr bis zur Abfahrt &quot;Harrislee&quot;, das ist die letzte Abfahrt vor der Grenze nach D&auml;nemark. Wenn ihr diese Abfahrt verpa&szlig;t habt, dann fahrt ihr weiter nach D&auml;nemark, futtert an einer der vielen Buden die ihr einfach nicht &uuml;besehen k&ouml;nnt eine echten d&auml;nischen HotDog. Anschlie&szlig;end dreht ihr um, fahrt nach S&uuml;den bis zur Abfahrt &quot;Harrislee&quot; zur&uuml;ck und es geht weiter:</p>

<p>An der Ampel bei der Abfahrt dann links abbiegen und der Stra&szlig;e (B199) bis zur n&auml;chsten Ampel an einer gro&szlig;en Kreuzung folgen. Rechts voraus seht ihr dann die Media-Markt-Reklame. Da sollt ihr aber nicht hin! Naja, jedenfalls noch nicht... Ihr biegt an dieser Kreuzung links ab und befindet euch dann auf der Westerallee. Nach ca. 400 Metern rechts ab in die Raiffeisenstra&szlig;e und habt es fast geschafft. Nach ca. 100 Metern links ab und dann nur noch ca. 200 Meter und ihr seid angekommen!</p>

<p>Bitte gebt bei der Anmeldung an, ob ihr mit \'nem normalen Pkw oder mit Wohnmobil,  Bus oder mit Wohnanh&auml;nger kommt. Uns stehen diesmal mehrere Parkpl&auml;tze zur Verf&uuml;gung und den Platz direkt bei der Halle m&ouml;chten wir vorrangig f&uuml;r die Wohnmobil-Nutzer reservieren, damit auch die &quot;dicht beim Geschehen&quot; sind.</p>


<!-- TODO ### Strassenkarte ### -->
<br/>
<br/>
<!-- Falk Homepagetools -->
<iframe src="http://www.falk.de/homepagetools/do/widgetHtml?code=5%2F1Ls7WztsGRPqQda9bmDo7u9WkcywyAYQk0H5HDm%2FTzjSx8inMNpPLeN0FdBUtyHgiHLKQk9Pqx%0AU9KsIIHA9n%2Bd7E4wCfWw" width="400px" height="194px" scrolling="no" frameborder="0"></iframe>
<!-- Mehr Infos und Nutzungsbedingungen unter http://www.falk.de/homepagetools -->
		';
		return $ret;
	}

}


?>
