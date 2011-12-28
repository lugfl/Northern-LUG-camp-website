<?php
  session_start();
  require_once('global.php');
	require_once('lib/func.http_get_var.php');
	require_once(WEB_ROOT.'/lib/class.Site.php');
	require_once(WEB_ROOT.'/lib/class.Plugin_Login.php');
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
}else {
	$searchNaviRoot = $page['pageid'];
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

// Every Template setup
$tmpl = new smarty();
$tmpl->template_dir = TEMPLATE_DIR;
$tmpl->assign('TITLE',$page['title']);
$tmpl->assign('NAVI',$navi1);
$tmpl->assign('SUBNAVI',$navi2);
$tmpl->assign('SPONSOREN',get_sponsoren_image());

// regular pages with database content
$content = '';
$pagetype = $site->getPageType($p);
$template = TEMPLATE_STYLE . '/page.default.html';
$tmpl->assign('TEMPLATE_STYLE',TEMPLATE_STYLE);
switch( $pagetype ) {
	case Site::PAGETYPE_PLUGIN_LOGIN:
		$login = new Plugin_Login($pdo);
		$rc = $site->auth_ok();
		if( ! $rc ) {
			$rc = $login->checkAuth();
		}
		$newpw = http_get_var('newpw');
		$tmpl->assign('newpw',$newpw);
		$tmpl->assign('auth_ok',$rc);
		$error = '';
		$tmpl->assign('error',$error);
		$tmpl->assign('loginpage',$p);
		$template = TEMPLATE_STYLE . '/page.login.html';
		break;
	case Site::PAGETYPE_TEXT_HTML:
		$content = $site->getPageContent($p);
		break;
	case Site::PAGETYPE_TEXT_WIKI:
		break;
	default:
		break;
}
$tmpl->assign('CONTENT',$content);
$tmpl->assign('DEBUG',print_r($_SESSION,TRUE));
$tmpl->display($template) ;


?>
