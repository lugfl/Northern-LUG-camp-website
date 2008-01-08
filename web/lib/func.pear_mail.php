<?php
require_once('Mail/mime.php');
require_once('Mail.php'); 

function my_mailer($from,$to,$subject,$msg) {
	global $MAILER;

	// Defaultwerte
	$options = array ( 
			'localhost' => 'www.lug-camp-2008.de',
			'host'      => 'localhost', 
			'auth'      => false, 
			'username'  => '', 
			'password'  => '' 
		   ); 

	if(isset($MAILER)) {
		if(isset($MAILERR['localhost']))
			$options['localhost'] = $MAILER['localhost'];
		if(isset($MAILERR['host']))
			$options['host'] = $MAILER['host'];
		if(isset($MAILER['auth']))
			$options['auth'] = $MAILER['auth'];
		if(isset($MAILER['username']))
			$options['username'] = $MAILER['username'];
		if(isset($MAILER['password']))
			$options['password'] = $MAILER['password'];

	}
	$mailer = Mail::factory('smtp',$options); 
	if (true === PEAR::isError($mailer)) 
	{ 
		return $mailer->getMessage(); 
	} 

	// Neues Mail_mime-Objekt mit Zeilenumbruch definieren 
	$mail = new Mail_mime("\r\n");

	// Absender festlegen 
	$mail->setFrom ($from); 
	 
	// Betreff der Mail festlegen 
	$mail->setSubject($subject); 
	 
	// Den Inhalt der Mail definieren 
	// Bitte beachten Sie das img-Tag 

	$mail->setTXTBody($msg);

	// Koerper der Mail auslesen 
	$body = $mail->get(); 
	 
	// Zusaetzliche Header definieren 
	$extra_header=array (
			'Content-Type'=> 'text/plain; charset=iso-8859-1', 
			'To' => $to
		); 

	// Header ergaenzen und auslesen 
	$headers = $mail->headers($extra_header); 

	// Mail verschicken 
	$res=$mailer->send($to,$headers,$body); 
	if (true === PEAR::isError($res)) 
	{ 
		return $res->getMessage(); 
	}
	else { return 0; }
}

?>
