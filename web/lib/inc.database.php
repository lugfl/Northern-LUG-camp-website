<?php

//require_once('.htconfig.php');

function my_connect($db='DEFAULT') {
	global $DB;

  if( !isset($DB[$db]['conn']) or !mysqli_ping($DB[$db]['conn']) ) {
		$DB[$db]['conn'] = @mysqli_connect($DB[$db]['host'],$DB[$db]['user'],$DB[$db]['pass']);
		if(mysqli_errno() != 0) {
			trigger_error("Database Problem",E_USER_ERROR);
			return false;
		}

		mysqli_select_db($DB[$db]['name'],$DB[$db]['conn']);
		if(mysqli_errno() != 0) {
			trigger_error("Database Problem",E_USER_ERROR);
			return false;
		}
		if(defined('WEB_DATEFORMAT2')) {
			print "SESSION";
			// date_format		%Y-%m-%d
			// datetime_format	%Y-%m-%d %H:%i:%s
			$SQL = "SET SESSION date_format='%d.%m.%Y'";
			my_query($SQL,$db);

			$SQL = "SET SESSION datetime_format='%d.%m.%Y %H:%i'";
			my_query($SQL,$db);
		}
	}
	return true;
}

function my_query($SQL,$db='DEFAULT') {
	global $DB;
	$ret = false;
	if(is_resource($DB[$db]['conn'])) {
		$ret = @mysqli_query($SQL,$DB[$db]['conn']);
		if(mysqli_errno() != 0) {
			trigger_error('Database Problem',E_USER_ERROR);
			trigger_error(mysqli_errno() . ' ' . mysqli_error(),E_USER_NOTICE);
			$ret = false;
		}
	}
	return $ret;
}

/**
 *
 */
function my_affected_rows($db='DEFAULT') {
	global $DB;
	global $DEBUG;

	$ret = false;
	if(is_resource($DB[$db]['conn'])) {
		$ret = @mysqli_affected_rows($DB[$db]['conn']);
		if(mysqli_errno() != 0) {
			if( isset($DEBUG) && $DEBUG==1 ) {
				trigger_error('Database Error: '.mysqli_error().' ('.$SQL.')',E_USER_ERROR);
			}else{
				trigger_error('Database Problem',E_USER_ERROR);
			}
			$ret = false;
		}
	}
	return $ret;
}

function my_insert_id($db='DEFAULT') {
	global $DB;
	global $DEBUG;

	if(is_resource($DB[$db]['conn'])) {
		$ret = @mysqli_insert_id($DB[$db]['conn']);
		if(mysqli_errno() != 0) {
			if( isset($DEBUG) && $DEBUG==1 ) {
				trigger_error('Database Error: '.mysqli_error().' ('.$SQL.')',E_USER_ERROR);
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
	return mysqli_real_escape_string($str,$DB[$db]['conn']);
}

?>
