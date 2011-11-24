<?php

function get_sponsoren_image() {
  my_connect();

	$ret = '';
	
	if(!isset($_SESSION['sponsoren_id'])) {
		$_SESSION['sponsoren_id'] = 0;
	}

	$display_id = $_SESSION['sponsoren_id'];

	$res1 = my_query('SELECT sponsorenid,name,url,img FROM sponsoren WHERE sponsorenid>=' . $display_id . ' LIMIT 1');
	if(isset($res1)) {
		if (mysql_num_rows($res1)>0) {
			$row = mysql_fetch_assoc($res1);
			$ret .= '<label for-id="sponsor'.$display_id.'">sponsored by:</label><br/>';
			$ret .= '<a href="'.$row['url'].'" target="_blank">';
	    // Bilder in der DB stehen immer mit /images/... in der URL
			$ret .= '<img id="sponsor'.$display_id.'" src=".'.$row['img'].'" alt="'.$row['name'].'" title="'.$row['name'].'" border="0">';
			$ret .= '</a>';
			$display_id++;
		} else {
			$display_id = 0;
		}
		$_SESSION['sponsoren_id'] = $display_id; 
	}
	mysql_free_result($res1);
	return $ret;
}

?>
