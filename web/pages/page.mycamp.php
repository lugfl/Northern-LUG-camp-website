<?php

$PAGE['mycamp']['name'] = "MyCamp";
$PAGE['mycamp']['navilevel'] = 1;
$PAGE['mycamp']['login_required'] = 1;
$PAGE['mycamp']['phpclass'] = 'HtmlPage_mycamp';
$PAGE['mycamp']['parent'] = 'root';
$PAGE['mycamp']['hidden'] = 1;

class HtmlPage_mycamp extends HtmlPage {

	function getContent() {
    		$ret = '
			<h1>MyCamp</h1>
			<p>
			Hier geht zu Deinen pers&ouml;nlichen Campdaten. Nat&uuml;rlich nur, wenn Du dich vorher angemeldet hast und das geht bekanntlich erst ab dem 01.01.2008.
			</p>
		';
		return $ret;
	}

}


?>
