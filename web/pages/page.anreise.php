<?php

$PAGE['anreise']['name'] = "Anreise";
$PAGE['anreise']['navilevel'] = 1;
$PAGE['anreise']['login_required'] = 0;
$PAGE['anreise']['phpclass'] = 'HtmlPage_anreise';
$PAGE['anreise']['parent'] = 'root';

class HtmlPage_anreise extends HtmlPage {

	function getContent() {
    		$ret = '
<h1>Die Anreise</h1>
Wer bislang noch nicht in Flensburg war oder sich keinen eigenen Chauffeur leisten kann, der weiss wo das Camp genau stattfindet, der sollte sich die folgenden Seiten genau durchlesen.<br />
<!-- TODO ### bilder von Chefeur, Karte von FLensburg oder aenlicehes ### -->

		';
		return $ret;
	}

}


?>
