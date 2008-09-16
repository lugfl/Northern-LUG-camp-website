<?php
/**
 * PEAR-Requirements:
 * - Mail_Mime
 * - Net_SMTP
 */
require_once('Mail/mime.php');
require_once('Mail.php'); 
 
$empfaenger = 'rx@example.com'; 
$absender = 'Absender <tx@example.com>';


$options = array ( 
		'localhost' => 'servername.example.com',
               'host'      => 'smtp.example.net', 	// Mailausgangsserver
               'auth'      => false, 
               'username'  => '', 
               'password'  => '' 
           ); 
$mailer = Mail::factory('smtp',$options); 
if (true === PEAR::isError($mailer)) 
{ 
   die ($mailer->getMessage()); 
} 

// Neues Mail_mime-Objekt mit Zeilenumbruch definieren 
$mail = new Mail_mime("\r\n");

// Absender festlegen 
$mail->setFrom ($absender); 
 
// Betreff der Mail festlegen 
$mail->setSubject('Test-Mail mit PEAR als MIME'); 
 
// Den Inhalt der Mail definieren 
// Bitte beachten Sie das img-Tag 

$mail->setTXTBody('Moin moin');


// Koerper der Mail auslesen 
$body = $mail->get(); 
 
// Zusaetzliche Header definieren 
$extra_header=array (
 'Content-Type'=> 'text/plain; charset=iso-8859-1', 
                     'To' => 
                     $empfaenger); 
// Header ergaenzen und auslesen 
$headers = $mail->headers($extra_header); 
var_dump($headers); 
// Mail verschicken 
$res=$mailer->send($empfaenger,$headers,$body); 
if (true === PEAR::isError($res)) 
{ 
   die ($res->getMessage()); 
}

?>
