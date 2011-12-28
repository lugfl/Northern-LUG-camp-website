<?php
  session_start();
  require_once('global.php');
	require_once('lib/func.http_get_var.php');
	require_once(WEB_ROOT.'/lib/class.Site.php');
	require_once(WEB_ROOT.'/lib/class.Plugin_Login.php');
	require_once(WEB_ROOT.'/lib/class.Plugin_Text_Html.php');
  require_once(WEB_ROOT.'/lib/smarty/libs/Smarty.class.php');

// connect to Database
$pdo = null;
try {
	$dsn = "mysql:host={$DB['DEFAULT']['host']};dbname={$DB['DEFAULT']['name']}";
	$pdo = new PDO($dsn,$DB['DEFAULT']['user'],$DB['DEFAULT']['pass']);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	$pdo->exec('SET names utf8');
	$pdo->exec('SET character set utf8');
} catch (PDOException $e) {
	print 'Error! :' . $e->getMessage();
	exit();
}

$site = new Site($pdo);

$p = http_get_var('p');
$domaininfo = $site->getDomain();

$page = $site->getPage($p);

if( ! is_array($page) || sizeof($page) == 0) {
	print '404er';
	exit();
}


// Every Template setup
$tmpl = new smarty();
$tmpl->template_dir = TEMPLATE_DIR;

// regular pages with database content
$content = '';
$pagetype = $site->getPageType($p);
$template = TEMPLATE_STYLE . '/page.default.html';

$tmpl->assign('TEMPLATE_STYLE',TEMPLATE_STYLE);

$plugin = null;

// 1.) load Plugin for Page
switch( $pagetype ) {
	case Site::PAGETYPE_PLUGIN_LOGIN:
		$plugin = new Plugin_Login($pdo,$page);
		$plugin->setLoginPage($p);
		break;
	case Site::PAGETYPE_TEXT_HTML:
		$plugin = new Plugin_Text_Html($pdo,$page);
		break;
	case Site::PAGETYPE_TEXT_WIKI:
		break;
	default:
		warn('Unknown Pagetype');
		break;
}

// 2.) read Input an process it
if( $plugin != null ) {
	$plugin->readInput();
	$plugin->processInput();
	switch( $plugin->getOutputMethod() ) {
		case Plugin::OUTPUT_METHOD_SMARTY:
			$template = TEMPLATE_STYLE . '/' . $plugin->getSmartyTemplate();
			$tmpl->assign( $plugin->getSmartyVariables() );
			break;
		case Plugin::OUTPUT_METHOD_BUILDIN:
			$content = $plugin->getOutput();
			$tmpl->assign('CONTENT',$content);
			break;
	}
}

$rootpath = $site->getRootPath($p);
// Create Naviline 1
$navi1 = '';
$navi1arr = array();
$n1 = $site->getNavigation(); // Hauptnavi
foreach( $n1 as $nav1) {
	if( $site->isInRole($nav1['acl']) ) {
		$l = '<a href="./index.php?p='.$nav1['pageid'].'"';
		if($p == $nav1['pageid'] || in_array($nav1['pageid'],$rootpath) ) {
			$l .= ' class="akktiv"';
		}
		$l .= '>'.$nav1['title'].'</a>';
		$navi1arr[]=$l;
	}
}
$navi1 = '' . implode('|',$navi1arr) . '';

$tmpl->assign('NAVI',$navi1);

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
		if( $site->isInRole($nav2['acl']) ) {
			$l = '<a href="./index.php?p='.$nav2['pageid'].'"';
			if($p == $nav2['pageid']) {
				$l .= ' class="akktiv"';
			}
			$l .= '>'.$nav2['title'].'</a>';
			$navi2arr[]=$l;
		}
	}
	$navi2 = '' . implode('|',$navi2arr) . '';
}
$tmpl->assign('SUBNAVI',$navi2);

$tmpl->assign('TITLE',$page['title']);
$tmpl->assign('SPONSOREN',get_sponsoren_image());
$tmpl->assign('DEBUG',print_r($_SESSION,TRUE));
$tmpl->display($template) ;


?>
