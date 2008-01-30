<?php

if(!defined('WEB_INSIDE'))
	die("geh wech");

require_once('pages/class.HtmlPage.php');
require_once('pages/class.HtmlPageNavi.php');

require_once('pages/page.start.php');
require_once('pages/page.news.php');

require_once('pages/page.lugcamp.php');
require_once('pages/page.lugcamp_stats.php');

require_once('pages/page.loc.php');
require_once('pages/page.loc_flensburg.php');
require_once('pages/page.loc_sportland.php');

require_once('pages/page.anreise.php');
require_once('pages/page.anreise_bahn.php');
require_once('pages/page.anreise_flugzeug.php');
require_once('pages/page.anreise_fuss.php');
require_once('pages/page.anreise_auto.php');
require_once('pages/page.anreise_schiff.php');
require_once('pages/page.anreise_taxi.php');

require_once('pages/page.camp.php');
require_once('pages/page.camp_essen.php');
require_once('pages/page.camp_np.php');

require_once('pages/page.prog.php');
require_once('pages/page.anmeldung.php');
require_once('pages/page.mycamp.php');
require_once('pages/page.mycamp_rechnung.php');
require_once('pages/page.mycamp_shop.php');
require_once('pages/page.mycamp_event.php');
require_once('pages/page.mycamp_passwd.php');
//require_once('pages/page.mycamp_einverstaendniserklaerung.php');

require_once('pages/page.newpw.php');

require_once('pages/page.impressum.php');

// Authentifizierungscheck
require_once('pages/inc.auth.php');
?>
