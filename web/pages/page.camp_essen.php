<?php

$PAGE['camp_essen']['name'] = "Verpflegung";
$PAGE['camp_essen']['navilevel'] = 2;
$PAGE['camp_essen']['login_required'] = 0;
$PAGE['camp_essen']['phpclass'] = 'HtmlPage_camp_essen';
$PAGE['camp_essen']['parent'] = 'camp';

class HtmlPage_camp_essen extends HtmlPage {

	function getContent() {
    		$ret = '
<h1>Die Verpflegung</h1>

<p>Tja, 2007 in Interlaken haben wir sie kennengelernt: Die Grillkohl-Flatrate. Dat ist aber nix f&uuml;r echte K&uuml;stenbewohner, da muss schon was anderes her, also wurde beschlossen:<br><br>

<center><b>2008 in Flensburg wird es eine echte &quot;Grill-Flatrate&copy;&quot; geben!</b> </center>
</p>

<p><img src="images/mike/stromi-beim-grillen_300.jpg" align="left" style="margin-right:10px;" title="Stromi beim Grillen in Flensburg"/>2004 hatten wir das ja schon beinahe geschafft, auch wenn es da eher ein Problem falscher Einkaufsplanung unsererseits war. Beim LUG Camp 2008 wird es aber ganz bewusst eine &quot;Grill-Flatrate&quot; geben, d. h., wer immer Lust hast, sich ein St&uuml;ck Fleisch oder eine Wurst auf einen der Grills zu werfen, kann dies - egal zu welcher Tages- oder Nachzeit - tun! Wir werden daf&uuml;r sorgen, dass ausreichend Grillfl&auml;che und ausreichend Grillfleisch bzw. W&uuml;rstchen zur Verf&uuml;gung stehen!</p>

<center><b>Darauf habt ihr unser Wort!</b></center>

<p><img src="images/mike/kaese_300.jpg" align="right" style="margin-left:10px;margin-bottom:10px;" title="HowTo build Kaesspazten"/>Die Kaffee- und Tee-Flatrate, die sind ja mittlerweile bei jedem Camp eigentlich schon selbstverst&auml;ndlich und auch auf die werdet ihr nat&uuml;rlich nicht verzichten m&uuml;ssen. Zus&auml;tzlich zum jederzeit m&ouml;glichen Grillen werden wir uns nat&uuml;rlich noch weitere warme Mahlzeiten einfallen lassen, eine davon steht bereits fest:</p>

<p>Eine kleine Schar von Linux-Fans aus dem &auml;ussersten S&uuml;den Deutschlands m&ouml;chte es sich nach dem grandiosen Erfolg in Interlaken nicht nehmen lassen, auch 2008 in Flensburg die <a href="http://www.allgaeuer-rezepte.de/kaespatzen.htm" target="_blank">&quot;Allg&auml;uer K&auml;sspatzen&quot;</a> nach Original-Rezept herzustellen und uns damit zu verk&ouml;stigen! </p>


		';
		return $ret;
	}

}


?>
