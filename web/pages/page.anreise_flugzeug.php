<?php

$PAGE['anreise_flugzeug']['name'] = "Flugzeug";
$PAGE['anreise_flugzeug']['navilevel'] = 2;
$PAGE['anreise_flugzeug']['login_required'] = 0;
$PAGE['anreise_flugzeug']['phpclass'] = 'HtmlPage_anreise_flugzeug';
$PAGE['anreise_flugzeug']['parent'] = 'anreise';

class HtmlPage_anreise_flugzeug extends HtmlPage {

	function getContent() {
    		$ret = '
<h1>Anreise mit dem Flugzeug</h1>

Wer in der gl&uuml;cklichen Lage ist, mit seinen ph&auml;nomenalen Linux-Kenntnissen schon so viel verdient zu haben, dass er ein eigenes Flugzeug besitzt, der kann auch einfach bis zum Flugplatz &quot;Flensburg-Sch&auml;ferhaus&quot; fliegen. Der Fluplatz ist ganz in der N&auml;he des LUG Camps und wenn ihr uns anruft, dass ihr gelandet seid, dann dauert es nur noch wenige Minuten, bis wir euch abgeholt haben.

		';
		return $ret;
	}

}


?>
