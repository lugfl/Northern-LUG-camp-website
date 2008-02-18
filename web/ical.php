<?php

require_once('lib/func.parse_ical.php');

function parse_ical_utf8($name) {
	$filename = $name . ".ics";

	if($fp = fopen($filename,"r")) {
		$in = fread($fp,filesize($filename));
		fclose($fp);

		$str = iconv('utf-8','ISO-8859-1',$in);
		if($fp = fopen('/tmp/'.$name.'-ISO.ics',"w")) {
			fputs($fp,$str);
			fclose($fp);

			print_r(parse_ical('/tmp/lc2008-all-ISO'));
		}
	}
}

parse_ical_utf8('lc2008-all');

/*
print '<pre>';
print_r(parse_ical('lc2008-all-ISO'));
print '</pre>';
*/

?>
