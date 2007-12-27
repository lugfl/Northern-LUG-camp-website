<?php

$PAGE['anreise_fuss']['name'] = "zu Fuss";
$PAGE['anreise_fuss']['navilevel'] = 2;
$PAGE['anreise_fuss']['login_required'] = 0;
$PAGE['anreise_fuss']['phpclass'] = 'HtmlPage_anreise_fuss';
$PAGE['anreise_fuss']['parent'] = 'anreise';

class HtmlPage_anreise_fuss extends HtmlPage {

	function getContent() {
    		$ret = '
<h1>zu Fuss / per Fahrrad</h1>

Daran glauben wir nicht wirklich, daher ersparen wir uns hier die konkreten Hinweise

		';
		return $ret;
	}

}


?>
