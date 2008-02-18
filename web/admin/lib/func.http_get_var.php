<?php

function http_get_var($name,$default='') {
	$ret = "";
	
	if(isset($_POST[$name]))
		$ret = $_POST[$name];
	else if(isset($_GET[$name]))
		$ret = $_GET[$name];
	else
		$ret = $default;
	return $ret;
}

/**
 * Abfrage des Scriptnamens
 *
 * Bei mod_php und PHP-CGI muessen unterschiedliche Variablen
 * fuer das Auslesen des Scriptnamens abgefragt werden.
 * 
 * @return string Pfadname relativ zum Documentroot
 */
function get_script_name() {
  global $_SERVER;
  $ret = 'unknown';
  if(isset($_SERVER['PATH_INFO'])) // PHP-CGI
    $ret = $_SERVER['PATH_INFO'];
  else if(isset($_SERVER['SCRIPT_NAME'])) // mod_php
    $ret = $_SERVER['SCRIPT_NAME'];
  return $ret;
}
?>
