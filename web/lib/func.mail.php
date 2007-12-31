<?php
/**
 * Mail Subsystem
 *
 * @package Mail
 * @author Frank Agerholm <frank@fagerholm.de>
 * @version: $Id$
 * @see MAILER
 */

require_once('Mail/mime.php');
require_once('Mail.php');
require_once('lib/inc.database.php');


/**
 * Verschicken einer EMail ueber einen Konfigurierten Mailer
 *
 * Die Konfiguration unterschiedlicher Mailer wird in der
 * Konfigdatei .htconfig.php vorgenommen.
 * 
 * <code>
 * $MAILER['DEFAULT']['type'] = 'sendmail';
 *
 * $MAILER['DEMO01']['type'] = 'smtp';
 * $MAILER['DEMO01']['host'] = 'smtp.example.com';
 * $MAILER['DEMO01']['auth'] = 1;
 * $MAILER['DEMO01']['username'] = 'demouser@example.com';
 * $MAILER['DEMO01']['password'] = 'helloWorldPasswd';
 * $MAILER['DEMO01']['subject'] = 'Testmail';
 * $MAILER['DEMO01']['subjectprefix'] = 'Demo:';
 * $MAILER['DEMO01']['alwaysbcc'] = 'copy@example.com';
 * </code>
 *
 * Wird ein Subject angegeben, wird immer dieses verwendet.
 *
 * Das Subjectprefix wird dem normalen Subject vorangestellt
 * und durch ein Freizeichen getrennt.
 *
 * Mit alwaysbcc kann eine EMailadresse angegeben werden,
 * die immer eine Copy erhaelt.
 *
 * @param string Konfigurationsname des Mailers
 * @param string EMailadresse des Absenders
 * @param string EMailadresse des Empfaenger
 * @param string plain/text Body der Mail
 * @param string Betreffzeile
 * @return bool Mail erfolgreich verschickt
 */

function send_txt_mail($mailername,$from,$to,$body,$subject='') {
	global $MAILER;
	global $_SERVER;
	$ret = false;
	$options_ok = true;

	if( ! isset($MAILER[$mailername]) ) {
		trigger_error('Unknown Mailer in send_txt_mail(). Using DEFAULT.',E_USER_NOTICE);
		$mailername = 'DEFAULT';
	}

	$m = $MAILER[$mailername];

	// Konfigoptionen vorbereiten
	$options = array ();
	if( isset($m['type']) ) {
		if($m['type'] == 'smtp') {
			// Hostname for EHLO
			if(isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] != '') {
				$options['localhost'] = $_SERVER['SERVER_NAME'];
			}
			if(isset($m['host']) ) {
				// SMTP-Server
				$options['host'] = $m['host'];
			}else{
				trigger_error('SMTP-Host missing.',E_USER_ERROR);
				$options_ok = false;
			} // if host

			if(isset($m['auth']) && $m['auth'] ) {
				// SMTP-AUTH Aktiv
				if(isset($m['username']) && $m['username'] != '' &&
					isset($m['password']) && $m['password'] != ''
				) {
					$options['auth'] = true;
					$options['username'] = $m['username'];
					$options['password'] = $m['password'];
				}else{
					trigger_error('No SMTP-Auth Credentials, but Auth wanted',E_USER_NOTICE);
				}
			} // if auth
		}else if($m['type'] == 'sendmail') { // if type==amtp
			
		}else{ // if type==sendmail
			trigger_error('Unknown Mailer-Backend specified.',E_USER_ERROR);
			$options_ok = false;
		} // if type supported

	}else{ // if type
		trigger_error('No Mailer-Konfiguration available.',E_USER_ERROR);
		$options_ok = false;
	}

	if(isset($m['subject']) && $m['subject'] != '') {
		// Subject-Override active
		$subject = $m['subject'];
	}

	if(isset($m['subjectprefix']) && $m['subjectprefix'] != '') {
		$subject = $m['subjectprefix'].' '.$subject;
	}

	if($options_ok) {
		// mail only, if required optins ok.

		// create Mailer-Object
		$mailer = Mail::factory($m['type'],$options);
		if (true === PEAR::isError($mailer)) { 
			die ($mailer->getMessage()); 
		}

		// Create MIME-Message
		$mail = new Mail_mime("\r\n");
		$mail->setFrom($from);
		$mail->setSubject($subject);

		if(isset($m['alwaysbcc']) && $m['alwaysbcc'] != '') {
			$mail->addBcc($m['alwaysbcc']);
		}

		$mail->setTXTBody($body);

		$mbody = $mail->get();
		$extra_header = Array(
			'Content-Type' => 'text/plain; charset=iso-8859-1',
			'To' => $to
		);
		$headers = $mail->headers($extra_header);
		$res = $mailer->send($to,$headers,$mbody);
		if(true === PEAR::isError($res)) {
			$msg = $res->getMessage();
			trigger_error('SMTP: '.$msg,E_USER_NOTICE);
			$ret = false;
		}else{
			$ret = true;
		}

	} // if options_ok
	return $ret;
} // function send_txt_mail

?>
