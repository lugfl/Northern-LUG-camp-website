<?php

require_once('lib/func.pear_mail.php');
require_once('Mail/RFC822.php');

$PAGE['newpw']['name'] = "Neues Passwort generieren";
$PAGE['newpw']['navilevel'] = 0;
$PAGE['newpw']['login_required'] = 0;
$PAGE['newpw']['phpclass'] = 'HtmlPage_newpw';
$PAGE['newpw']['parent'] = 'none';

class HtmlPage_newpw extends HtmlPage {

	function new_password($user, $email)
	{
		my_connect();
		$content = "qwertzupasdfghkyxcvbnm";
		$content .= "123456789";
		srand((double)microtime()*1000000);
		$rand_password = '';
		for($i = 0; $i < 8; $i++)
		{
			$rand_password .= substr($content,(rand()%(strlen ($content))), 1);
		}
		$SQL1 = "UPDATE account SET passwd = MD5('".$rand_password."') WHERE username = '".$user."'";
		$add = my_query($SQL1);
		return $rand_password;
	}

	function getContent() {
    		$ret = '<h1>Passwort vergessen</h1>';
		
		my_connect();
		
		$done = http_get_var('done');
		$user = http_get_var('user');
		$email = http_get_var('email');
		
		$error = 'Accountname oder E-Mailadresse sind falsch!';
		
		if ($done) {
			$SQL2 = "SELECT username,email FROM account WHERE username='".$user."'";
			$query = my_query($SQL2);
			if ($query) {
				$array = mysql_fetch_array($query);
				if ($array['email'] == $email && $array['username'] == $user) {
					$newpw = $this->new_password($user,$email);
					$message = "Hallo ".$user."!\n\nDu hast auf www.lug-camp-2008.de ein neues Passwort angefordert! Dieses lautet wie folgt.\n\n\tPasswort: $newpw\n\ndie Mitglieder der LUG Flensburg";
					my_mailer('anmeldung@lug-camp-2008.de',my_escape_string($email),'Lugcamp 2008 - Neues Passwort für MyCamp',$message);
					$ret .= "<p>Dein neues Passwort wurde an die angegebene E-Mailadresse geschickt!</p>";
				} else {
					$ret .= $error;
				}
			} else { $ret .= $error; }
		} else {
			$ret .= '
			<div id="authbox">
			<form method="POST" action="?p=newpw">
			<input type="hidden" name="done" value="yes" />
			<dl>
			    <dt>Benutzername:</dt>
			    <dd><input type="text" name="user" value="" /></dd>
			    <dt>E-Mail Adresse:</dt>
			    <dd><input type="text" name="email" value="" /></dd>
			</dl>
			<input type="submit" value="OK" />
			</form>
			</div>
			';
		}	
		
		return $ret;
	}

}


?>
