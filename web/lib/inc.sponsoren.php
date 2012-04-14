<?php

function get_sponsoren_image($pdo) {

	$ret = '';
	
	if(!isset($_SESSION['sponsoren_id'])) {
		$_SESSION['sponsoren_id'] = 1;
	}

	$display_id = $_SESSION['sponsoren_id'];

	try {
		$SQL = 'SELECT sponsorenid,name,url,img FROM sponsoren WHERE sponsorenid>=? LIMIT 1';
		$st = $pdo->prepare($SQL);
		$st->execute( array($display_id) );
		if ( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
			$ret .= '<label for-id="sponsor'.$display_id.'">sponsored by:</label><br/>';
			$ret .= '<a href="'.$row['url'].'" target="_blank">';
	    // Bilder in der DB stehen immer mit /images/... in der URL
			$ret .= '<img id="sponsor'.$display_id.'" src=".'.$row['img'].'" alt="'.$row['name'].'" title="'.$row['name'].'" border="0">';
			$ret .= '</a>';
			$display_id++;
		} else {
			$display_id = 1;
		}
		$st->closeCursor();
		$_SESSION['sponsoren_id'] = $display_id; 
	} catch (PDOException $e) {
		print $e;
	}
	return $ret;
}

?>
