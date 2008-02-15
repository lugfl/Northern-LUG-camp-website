<?php

$PAGE['kasse']['name'] = "Kasse";
$PAGE['kasse']['navilevel'] = 1;
$PAGE['kasse']['login_required'] = 1;
$PAGE['kasse']['phpclass'] = 'HtmlPage_kasse';
$PAGE['kasse']['parent'] = 'root';

class HtmlPage_kasse extends HtmlPage {

	var $name = "Kasse";
	var $navilevel = 1;
	var $login_required = 1;

	function HtmlPage_kasse() {
	}
	
	function getContent() {
		$nickname = http_get_var('nickname');
		if($nickname) {
			$SQL = "SELECT * FROM account WHERE username = '$nickname'";
			$search_query = my_query($SQL);
			if(mysql_num_rows($search_query) >0) {
				$ret = '<p>Gefundene Nicknames:</p>';
				while($row = mysql_fetch_assoc($search_query)) {
					$ret .= '<a href="?p=account&accountid='.$row["accountid"].'">'.$row["username"].'</a>';
				}
			}
		} else {
			$ret = '
			<h1>Kasse</h1>
			<p>Hier kann Jan eintragen wer schon wof&uuml;r bezahlt hat.</p>
			<form action="?p=kasse" method="post">
			Nickname: <input type="text" name="nickname" />
			<p><input type="submit" value=" Suchen " /></p>
			</form>
			';
		}
		return $ret;
	}

}


?>
