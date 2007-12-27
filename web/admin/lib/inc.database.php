<?php

require_once('.htconfig.php');

function my_connect($db='DFAULT') {
	global $DB;

	$DB[$db]['conn'] = @mysql_connect($DB[$db]['host'],$DB[$db]['user'],$DB[$db]['pass']);
	if(mysql_errno() != 0) {
		trigger_error("Database Problem",E_USER_ERROR);
		return false;
	}

	mysql_select_db($DB[$db]['name'],$DB[$db]['conn']);
	if(mysql_errno() != 0) {
		trigger_error("Database Problem",E_USER_ERROR);
		return false;
	}

/*
	if(defined('WEB_DATEFORMAT')) {
		print "SESSION";
		// date_format		%Y-%m-%d
		// datetime_format	%Y-%m-%d %H:%i:%s
		$SQL = "SET SESSION date_format='%d.%m.%Y'";
		my_query($SQL,$db);

		$SQL = "SET SESSION datetime_format='%d.%m.%Y %H:%i'";
		my_query($SQL,$db);
	}
*/
	return true;
}

function my_query($SQL,$db='DEFAULT') {
	global $DB;
	$ret = false;
	if(is_resource($DB[$db]['conn'])) {
		$ret = @mysql_query($SQL,$DB[$db]['conn']);
		if(mysql_errno() != 0) {
			if(defined('DEBUG')) {
				trigger_error('Database Problem: '.mysql_error(),E_USER_ERROR);
			}else{
				trigger_error('Database Problem',E_USER_ERROR);
			}
			$ret = false;
		}
	}
	return $ret;
}

function my_escape_string($str,$db='DEFAULT') {
	global $DB;
	return mysql_real_escape_string($str,$DB[$db]['conn']);
}

?>
