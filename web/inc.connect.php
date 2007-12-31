<?php

include_once('global.php');

	$conn = @mysql_connect($DB_host,$DB_user,$DB_pass);
	if(mysql_errno() != 0) {
		echo "Momentan steht das System leider nicht zur verf&uuml;gung.";
		exit();
	}
	mysql_select_db($DB_name,$conn);
	if(mysql_errno($conn) != 0) {
		echo "Momentan steht das System leider nicht zur verf&uuml;gung.";
		exit();
	}
?>
