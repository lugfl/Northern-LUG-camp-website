<?php

if(!defined('WEB_INSIDE'))
	die("geh wech");

require_once('global.php');
require_once('lib/inc.database.php');


// Damit das Logout immer klappt, wird das grundsaetzlich abgefangen
if(isset($_SESSION['_login_ok']) && $_SESSION['_login_ok'] == 1) {
	$logout = http_get_var('logout');
	if($logout) {
		unset($_SESSION['_login_ok']);
	}
}

// Login durchfuehren. Wenns Login nicht klappt, muss das Formular 
// aus der Funktion auth_form() verwendet werden.
if(! isset($_SESSION['auth_form']) ) {
	$_SESSION['auth_form'] = time();
}
$auth_user = http_get_var('auth_user'.$_SESSION['auth_form']);
$auth_pass = http_get_var('auth_pass'.$_SESSION['auth_form']);
if($auth_user != '' && $auth_pass != '') {
	// Formular abgeschickt
	if($auth_user == 'frank' && $auth_pass == 'frank') {
		$_SESSION['_login_ok'] = 1;
	}
}
$auth_user = '';
$auth_pass = '';

/**
 * Pruefen, ob der aktuelle User eingelogt ist oder nicht
 */
function auth_ok() {
	global $_SESSION;
	$ret = false;
	if(isset($_SESSION['_login_ok']) && $_SESSION['_login_ok'] == 1) {
		$ret = true;
	}
	return $ret;
}

/**
 * Login-Form erzeugen, wenn notwendig
 *
 * @param string Seitenname
 * @return string HTML-Formular, falls Login notwendig. Sonst ''
 */
function auth_form($pn = 'start') {
	global $PAGE;
	global $_SESSION;
	$ret = '';

	if(isset($PAGE[$pn]) && isset($PAGE[$pn]['login_required']) &&  $PAGE[$pn]['login_required']==1) {
		// Login erforderlich
		if( ! isset($_SESSION['_login_ok']) || $_SESSION['_login_ok'] == 0) {
			// Login notwendig
			if(! isset($_SESSION['auth_form']) ) {
				$_SESSION['auth_form'] = time();
			}
			// Formular anzeigen
			$ret .= '
			<div id="authbox">
				<form action="./index.php" method="post">
					<input type="hidden" name="p" value="'.$pn.'"/>
					<dl>
						<dt><label for-id="auth_form_user">Benutzername:</label></dt>
						<dd><input id="auth_form_user" type="text" name="auth_user'.$_SESSION['auth_form'].'"/></dd>
						
						<dt><label for-id="auth_form_pass">Passwort:</label></dt>
						<dd><input id="auth_form_pass" type="text" name="auth_pass'.$_SESSION['auth_form'].'"/></dd>
					</dl>
					<input type="submit" value="Login"/>
				</form>
			</div>
			';
		}
	} // if login_required
	return $ret;
}

?>
