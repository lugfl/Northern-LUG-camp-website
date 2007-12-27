<?php

$PAGE['camp_np']['name'] = "Netzwerk/Strom";
$PAGE['camp_np']['navilevel'] = 2;
$PAGE['camp_np']['login_required'] = 0;
$PAGE['camp_np']['phpclass'] = 'HtmlPage_camp_np';
$PAGE['camp_np']['parent'] = 'camp';

class HtmlPage_camp_np extends HtmlPage {

	function getContent() {
    		$ret = '
<h1>Netzwerk / Strom</h1>
<ul>

<li>Wir stellen euch alles Notwendige zum Anschluss eurer PC\'s an der Netzwerk des LUG Camp zur Verf&uuml;gung. Es wird dabei sowohl ein Kabel- als auch ein Funk-Netzwerk geben. Auch Zugang zum Internet wird vorhanden sein, jedoch bitten wir schon jetzt darum, zum Sparen von Bandbreite f&uuml;r Installationen etc. die internen Mirrors, auf denen die Repositories aller wichtigen Distributionen vorhanden sein werden, zu nutzen.<br />

<p>
<b> Die IP-Adressen werden &uuml;brigens fest bei der Anmeldung vergeben! </b>
</p>

</li>
<li>Die Stromverteilung erfolgt durch uns bis zu den Tischen. Um eure eigenen Ger&auml;te dann anzuschliessen, bringt bitte ausreichend Mehrfachsteckdosen etc. mit. Unsere Schweizer Freunde denken dabei bitte daran, dass sie einen Adapter ben&ouml;tigen, um sich an unser Stromnetz anschliessen zu k&ouml;nnen!</li>

</ul>

		';
		return $ret;
	}

}


?>
