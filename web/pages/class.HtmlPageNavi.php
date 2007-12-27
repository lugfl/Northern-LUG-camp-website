<?php

require_once('lib/func.http_get_var.php');

function htmlpage_link($pagename,$content='') {
	global $PAGE;
	$ret = '';
	
	if(isset($PAGE[$pagename]) ) {
		if($content == '') {
			$content = $PAGE[$pagename]['name'];
		}
		$ret = '<a href="./index.php?p='.$pagename.'">'.$content.'</a>';
	}
	return $ret;
}

function htmlpage_is_hidden($pagename) {
	global $PAGE;
	$ret = 1;
	if(!isset($PAGE[$pagename]['hidden']) || $PAGE[$pagename]['hidden'] == 0) {
		$ret = 0;
	}
	return $ret;

}

function htmlpage_login_required($pagename) {
	global $PAGE;
	$ret = 0;
	if(isset($PAGE[$pagename]['login_required']) && $PAGE[$pagename]['login_required'] == 1) {
		$ret = 1;
	}
	return $ret;

}


class HtmlPageNavi {

	var $current_page = "";
	var $has_subnavi = 0;
	var $default_page = 'start';
	
function HtmlPageNavi() {
	global $PAGE;
	
	$this->current_page = http_get_var('p');
	
	
	foreach($PAGE as $pname=>$pdata) {
		if(! htmlpage_is_hidden($pname) ) {
			if(isset($pdata['parent']) && $pdata['parent'] == $this->current_page) {
				$this->has_subnavi = 1;
			}
		}
	}
}

function getNaviHtml($parent='root') {
	global $PAGE;
	
	$pages = array();
	foreach($PAGE as $pname=>$pdata) {
		if($pdata['parent'] == $parent) {
			$pages[$pname] = $pdata;
		}
	}
	$items = array();
	if(count($pages) > 0) {
		foreach($pages as $pname=>$pdata) {
			if(! htmlpage_is_hidden($pname) ) {
				$l = '<a href="./index.php?p='.$pname.'">'.$pdata['name'].'</a>';
				array_push($items,$l);
			}
		}
	}
	if($parent == 'root' && auth_ok()) {
		$l = '<a href="./index.php?p=start&logout=1">Abmelden</a>';
		array_push($items,$l);
		
	}
	return implode('|',$items);
}

function getCurrentPage() {
	return $this->current_page;
}

function getRootPath() {
	global $PAGE;
	$ret = array();

	$startpage = $this->getCurrentPage();
	if( ! isset($PAGE[$startpage]) ) {
		// Startseite existiert nicht
		return array();
	}
	$level = $PAGE[$this->current_page]['navilevel'];
	$parent = $PAGE[$this->current_page]['parent'];
	$ret[$level] = $PAGE[$this->current_page];
	$ret[$level]['_self'] = $this->current_page;
	$level--;
	while($level>=0) {
		if( isset($PAGE[$parent]) ) {
			$ret[$level] = $PAGE[$parent];
			$ret[$level]['_self'] = $parent;
			$parent = $ret[$level]['parent'];
		}else{
			print "$parent existiert nicht";
		}
		$level--;
	}

	return $ret;
	
	
}

function hasSubnavi() {
	return $this->has_subnavi;
}

}

?>
