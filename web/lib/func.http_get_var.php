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

?>
