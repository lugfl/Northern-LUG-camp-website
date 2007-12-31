<?php

require_once('Mail/RFC822.php');

$email = "test@example.com";

if(Mail_RFC822::isValidInetAddress($email) == FALSE)
	print "$email ist Falsche\n";

$email = "test @ example.com";
if(Mail_RFC822::isValidInetAddress($email) == FALSE)
	print "$email ist Falsche\n";


?>
