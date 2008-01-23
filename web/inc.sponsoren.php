<?php

$SPONSOREN[0]['name'] = "Flensburger Brauerei";
$SPONSOREN[0]['url'] = "http://www.flens.de";
$SPONSOREN[0]['img'] = "./images/logo_flens.png";

$SPONSOREN[1]['name'] = "Sportland";
$SPONSOREN[1]['url'] = "http://www.sportland-flensburg.de";
$SPONSOREN[1]['img'] = "./images/logo-sportland.png";

$SPONSOREN[2]['name'] = "Fleischerei Clausen";
$SPONSOREN[2]['url'] = "http://www.fleischerei-clausen.de/";
$SPONSOREN[2]['img'] = "./images/logo_clausen.png";

$SPONSOREN[3]['name'] = "Ford Nehrkorn";
$SPONSOREN[3]['url'] = "http://www.fordnehrkorn.de/";
$SPONSOREN[3]['img'] = "./images/logo_nehrkorn.png";

$SPONSOREN[4]['name'] = "Servage Webhosting";
$SPONSOREN[4]['url'] = "http://www.servage.net/";
$SPONSOREN[4]['img'] = "./images/servage.png";

$SPONSOREN[5]['name'] = "SecAdm - Secure for sure";
$SPONSOREN[5]['url'] = "http://www.secadm.de/";
$SPONSOREN[5]['img'] = "./images/logo_secadm.png";

function get_sponsoren_image() {
	global $SPONSOREN;
	$ret = '';
	
	if(!isset($_SESSION['sponsoren_id'])) {
		$_SESSION['sponsoren_id'] = 0;
	}

	$display_id = $_SESSION['sponsoren_id'];


	$ret .= '<label for-id="sponsor'.$display_id.'">sponsored by:</label><br/>';
	$ret .= '<a href="'.$SPONSOREN[$display_id]['url'].'" target="_blank">';
	$ret .= '<img id="sponsor'.$display_id.'" src="'.$SPONSOREN[$display_id]['img'].'" title="'.$SPONSOREN[$display_id]['name'].'" border="0">';
	$ret .= '</a>';
	$display_id++;
	if( isset($SPONSOREN[$display_id]) ) {
		$_SESSION['sponsoren_id'] = $display_id; 
	}else{
		$_SESSION['sponsoren_id'] = 0; 
	}
	return $ret;
}

?>
