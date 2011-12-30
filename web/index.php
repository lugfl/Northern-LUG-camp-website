<?php
//require_once('lib/inc.debug.php');
session_start();
require_once('global.php');
require_once('lib/func.http_get_var.php');
require_once(WEB_ROOT.'/lib/class.Site.php');
require_once(WEB_ROOT.'/lib/class.Plugin_Login.php');
require_once(WEB_ROOT.'/lib/class.Plugin_Events.php');
require_once(WEB_ROOT.'/lib/class.Plugin_Text_Html.php');
require_once(WEB_ROOT.'/lib/class.Plugin_Text_Wiki.php');
require_once(WEB_ROOT.'/lib/class.Plugin_News.php');
require_once(WEB_ROOT.'/lib/smarty/libs/Smarty.class.php');

// connect to Database
$pdo = null;
try {
	$dsn = "mysql:host={$DB['DEFAULT']['host']};dbname={$DB['DEFAULT']['name']};charset=utf8";
	$pdo = new PDO($dsn,$DB['DEFAULT']['user'],$DB['DEFAULT']['pass']);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

	// required for PHP < 5.3.6 as workaround for charset
	// see http://www.php.net/manual/en/ref.pdo-mysql.connection.php
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

// no editor per default, but define var to prevent notices
$tmpl->assign('ENABLE_EDITOR', false);

$plugin = null;

// 1.) load Plugin for Page
switch( $pagetype ) {
	case Site::PAGETYPE_PLUGIN_LOGIN:
		$plugin = new Plugin_Login($pdo,$page);
		$plugin->setLoginPage($p);
		break;
	case Site::PAGETYPE_TEXT_HTML:
		$plugin = new Plugin_Text_Html($pdo,$page);
		if( $site->isInRole('admin') ) {
			$plugin->enableEditing();
		}
		break;
	case Site::PAGETYPE_TEXT_WIKI:
		$plugin = new Plugin_Text_Wiki($pdo,$page);
		if( $site->isInRole('admin') ) {
			$plugin->enableEditing();
		}
		break;
	case Site::PAGETYPE_PLUGIN_EVENTS:
		$plugin = new Plugin_Events($pdo,$page,$domaininfo,$site);
		break;
	case Site::PAGETYPE_PLUGIN_NEWS:
		$newsEintragId = http_get_var('news');
		$plugin = new Plugin_News($pdo,$page,$newsEintragId,$domaininfo['domainid']);
		if($site->isInRole('admin')) {
			$plugin->enableEditing();
		}
		break;
 	default:
		exit('Unknown Pagetype');
		break;
}

// 2.) read Input and process it
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
// Create Naviline
$naviarr = array();
foreach( $site->getNavigation() as $nav1) // Level 1
{
	$item = ARRAY();
	if( $site->isInRole($nav1['acl']) )
	{
		$item['pageid'] = $nav1['pageid'];
		$item['url'] = './index.php?p='.$nav1['pageid'];
		$item['title'] = $nav1['title'];
		$item['active'] = ($p == $nav1['pageid'] || in_array($nav1['pageid'],$rootpath));
		$item['subitems'] = ARRAY();
		// search for subitems
		$subItems = $site->getNavigation($nav1['pageid']);
		if(isset($subItems))
		{
			foreach( $site->getNavigation($nav1['pageid']) as $nav2) // Level 2
			{
				$subItem = ARRAY();
				if( $site->isInRole($nav2['acl']) )
				{
					$subItem['pageid'] = $nav2['pageid'];
					$subItem['url'] = './index.php?p='.$nav2['pageid'];
					$subItem['title'] = $nav2['title'];
					$subItem['active'] = ($p == $nav2['pageid'] || in_array($nav2['pageid'],$rootpath));
					$item['subitems'][] = $subItem;
				}
			}
		}
		$naviarr[] = $item;
	}
}
// assign navigation structure to Smarty..
$tmpl->assign('NAVI',$naviarr);

// Set some variables for rolebased functions in templates
$tmpl->assign('auth_ok', $site->auth_ok());
$tmpl->assign('role_user', $site->isInRole('user'));
$tmpl->assign('role_admin', $site->isInRole('admin'));

// Set variable to allow pagetyperelated templates
// (only numeric representation available)
$tmpl->assign('pagetypeid',$page['pagetypeid']);

$tmpl->assign('TITLE',$page['title']);
$tmpl->assign('SPONSOREN',get_sponsoren_image($pdo));
//$tmpl->assign('DEBUG',print_r($_SESSION,TRUE));
$tmpl->display($template) ;


?>
