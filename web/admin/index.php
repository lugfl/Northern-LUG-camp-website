<?php
  require_once('lib/class.AuthUser.php');
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
    <div id="banner">Administrationsbereich - LC-2008</div>
    
    <div id="haupt-navi">
<?php
	if(auth_ok() && is_object($navi)) {
		print $navi->getNaviHtml('root',1);
	}
?>
    </div>
<?php
/*
	$rootPath = $navi->getRootPath();
	if(isset($rootPath[1])) {
		print '<div id="sub-navi">';
		print $navi->getNaviHtml( $rootPath[1]['_self'] );
		print '</div>';
	}
*/
?>
    <div id="content">
    <?php
    	$display_content = 0;
    	if(true) {
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
