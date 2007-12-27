<?php

$PAGE['loc']['name'] = "Location";
$PAGE['loc']['navilevel'] = 1;
$PAGE['loc']['login_required'] = 0;
$PAGE['loc']['phpclass'] = 'HtmlPage_loc';
$PAGE['loc']['parent'] = 'root';

class HtmlPage_loc extends HtmlPage {

	function getContent() {
    		$ret = '
<h1>Location</h1>
<p><img src="images/mike/flensburg-innenhafen-200px.jpg" align="left" style="margin-right:10px;"/>Ohne Location geht es nicht.<br/><br/>
Das LUG-Camp 2008 findet dieses Jahr in Flensburg an der Ostsee im sch&ouml;nsten Bundesland der Welt statt, n&auml;mlich <a href="http://www.schleswig-holstein.de" target="_blank">Schleswig-Holstein</a>.<br>
</p>

<p>Ihr wollt mehr Informationen. dann klickt euch mal <a href="http://www.flensburg.de" target="_blank">durch</a>. </p>

		';
		return $ret;
	}

}


?>
