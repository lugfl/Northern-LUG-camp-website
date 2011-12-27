<?php
  session_start();
  require_once('global.php');
	require_once('lib/func.http_get_var.php');
	require_once(WEB_ROOT.'/lib/class.Site.php');
  require_once(WEB_ROOT.'/lib/smarty/libs/Smarty.class.php');

// connect to Database
$pdo = null;
try {
	$dsn = "mysql:host={$DB['DEFAULT']['host']};dbname={$DB['DEFAULT']['name']}";
	$pdo = new PDO($dsn,$DB['DEFAULT']['user'],$DB['DEFAULT']['pass']);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
} catch (PDOException $e) {
	print 'Error! :' . $e->getMessage();
	exit();
}

$site = new Site($pdo);

$p = http_get_var('p');
$domaininfo = $site->getDomain();
$page = $site->getPage($p);

$content = $site->getPageContent($p);

$rootpath = $site->getRootPath($p);

// Create Naviline 1
$navi1 = '';
$navi1arr = array();
$n1 = $site->getNavigation(); // Hauptnavi
foreach( $n1 as $nav1) {
	$l = '<a href="./index.php?p='.$nav1['pageid'].'"';
	if($p == $nav1['pageid'] || in_array($nav1['pageid'],$rootpath) ) {
		$l .= ' class="akktiv"';
	}
	$l .= '>'.$nav1['title'].'</a>';
	$navi1arr[]=$l;
}
$navi1 = '' . implode('|',$navi1arr) . '';


// Create Naviline 2
$navi2 = null;
$navi2arr = array();
$searchNaviRoot = $p;
if( is_numeric($page['parentpageid']) ) {
	$searchNaviRoot = $page['parentpageid'];
}
$n2 = $site->getNavigation($searchNaviRoot);
if( is_array($n2) ) {
	foreach( $n2 as $nav2) {
		$l = '<a href="./index.php?p='.$nav2['pageid'].'"';
		if($p == $nav2['pageid']) {
			$l .= ' class="akktiv"';
		}
		$l .= '>'.$nav2['title'].'</a>';
		$navi2arr[]=$l;
	}
	$navi2 = '' . implode('|',$navi2arr) . '';
}

$tmpl = new smarty();
$tmpl->template_dir = TEMPLATE_DIR;
$tmpl->assign('TITLE',$page['title']);
$tmpl->assign('NAVI',$navi1);
$tmpl->assign('SUBNAVI',$navi2);
$tmpl->assign('CONTENT',$content);
$tmpl->assign('SPONSOREN',get_sponsoren_image());
$tmpl->display(TEMPLATE_STYLE . '/page.default.html') ;

?>
