<?php

$PAGE['camp']['name'] = "Auf dem Camp";
$PAGE['camp']['navilevel'] = 1;
$PAGE['camp']['login_required'] = 0;
$PAGE['camp']['phpclass'] = 'HtmlPage_camp';
$PAGE['camp']['parent'] = 'root';
$PAGE['camp']['hidden'] = 0;

class HtmlPage_camp extends HtmlPage {

	function getContent() {
    		$ret = '
<h1>Das Camp</h1>

Woher bekomm ich mein Strom, was gibt es zu essen und gibt es dieses Jahr einen Grill, diese und weitere Fragen werden wir auf den folgenden Seiten
beantworten<br>

<!-- TODO ### FOTOS  muessen noch gmeacht werden ### -->

		';
		return $ret;
	}

}


?>
