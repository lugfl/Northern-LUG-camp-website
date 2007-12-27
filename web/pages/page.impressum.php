<?php

$PAGE['impressum']['name'] = "Impressum";
$PAGE['impressum']['navilevel'] = 1;
$PAGE['impressum']['login_required'] = 0;
$PAGE['impressum']['phpclass'] = 'HtmlPage_impressum';
$PAGE['impressum']['parent'] = 'root';

class HtmlPage_impressum extends HtmlPage {

	function getContent() {
    		$ret = '
<h1>Impressum</h1>
<p>
Das LUG-Camp 2008 wird von der Linux User Group Flensburg e.V. organisiert.
</p>

<h2>Vereinsanschrift</h2>

<address>
LUG Flensburg e.V.<br/>
Bl&uuml;cherstra&szlig;e 9<br/>
24944 Flensburg<br/>
<br/>
Telefon: 0461-3153081<br/>
Telefax: 0461-3107564<br/>
<br/>
Email : info (at) lugfl.de<br/>
</address>

<h2>Vertretungsberechtigter Vorstand</h2>

<ul>
	<li>Michael Schulte (1. Vorsitzender)</li>
	<li>Frank Agerholm (2. Vorsitzender)</li>
</ul>
<p>
Registergericht: Amtsgericht Flensburg<br/>
Registernummer : VR 2 VR 1724
</p>

<h2>Inhaltlich Verantwortlicher gem&auml;&szlig; &sect;10 Absatz 3 MDSt</h2>

<address>
Frank Agerholm<br/>
Gl&uuml;cksburger Str. 93<br/>
24943 Flensburg<br/>
<br/>
EMail : frank (at) lugfl.de<br/>

<h2>Haftungshinweis</h2>

<p>
Trotz sorgf&auml;ltiger inhaltlicher Kontrolle &uuml;bernehmen wir keine Haftung f&uuml;r die Inhalte externer Links. &uuml;r den Inhalt der verlinkten Seiten sind ausschliesslich deren Betreiber verantwortlich. 
</p>

<h2>Datenschutz</h2>
<p>
Im Rahmen der Veranstaltungsanmeldung, die hier am 01.01.2008 freigeschaltet wird, werden personenbezogene Daten von den Teilnehmern erhoben. 
Diese Daten werden ausschliesslich zu Abrechnungszwecken verwendet und nicht an Dritte weitergegeben.
</p>
';
		return $ret;
	}

}


?>
