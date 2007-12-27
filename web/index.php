<?php
  session_start();
  require_once('global.php');


  require_once('pages/index.php');

// Navigation
$navi = new HtmlPageNavi();

// Content
$p = http_get_var('p');
if(!isset($PAGE[$p])) {
	$p = "start";
}
$pclass = $PAGE[$p]['phpclass'];
$page = new $pclass;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/Strict.dtd">
<html>
  <head>
    <title> LUG-CAMP 2008 Flensburg - Germany</title>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
    <meta http-equiv="Content-Style-Type" content="text/css">
    <link rel="stylesheet" type="text/css" href="style.css">
    
    <link rev="copyright" title="Impressum" href="./index.php?p=impressum">
    <link rev="start" title="Startseite" href="./index.php?p=start">
    <link rev="bookmark" title="www.lugfl.de" href="http://www.lugfl.de">
  </head>
  <body>
  <div id="page">
    <div id="banner"><img src="bilder/banner.png"/></div>
    
    <div id="haupt-navi">
<?php
	if(is_object($navi)) {
		print $navi->getNaviHtml();
	}
?>
    </div>
    <div id="sub-navi">
<?php

	$rootPath = $navi->getRootPath();
	if(isset($rootPath[1])) {
		print $navi->getNaviHtml( $rootPath[1]['_self'] );
	}

?>
    </div>
    <div id="content">
    <div id="sponsoren"><?php print get_sponsoren_image(); ?></div>
    <?php
    	$display_content = 0;
    	if(htmlpage_login_required($p)) {
		$content = auth_form($p);
		if($content != '') {
			print $content;
		}else{
			$display_content = 1;
		}
	}else{
		$display_content = 1;
	}
	if($display_content && is_object($page)) {
		print $page->getContent();
	}
	
    ?>
    </div>
  </div>
<?php
	if(DEBUG == 1) {
		print '<pre>';
		var_dump($_SESSION);
		print '</pre>';
	}
?>
  </body>
</html>
