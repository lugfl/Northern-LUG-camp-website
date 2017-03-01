<?php

require_once('lib/func.pear_mail.php');
require_once('Mail/RFC822.php');
require_once('lib/func.http_get_var.php');
require_once('lib/class.Plugin.php');

/**
 * Plugin for Login
 *
 * Template-Blocks:
 *
 * - newpwform (new password request form)
 * - newpw (new password request)
 * - newform (account registration form)
 * - confirmmailsend (account created, registration mail send)
 * - confirmed (registration confirmed via mail-link)
 * - chpwform (change-password form)
 * - chpwconfirm (change-password confirm)
 */
class Plugin_Login extends Plugin {

	private $domaininfo = null;
	private $input_logout = FALSE;
	private $input_auth_user = '';
	private $input_auth_pass = '';
	private $input_auth_newpass = ''; // used for password changes
	private $input_auth_pass2 = '';
	private $input_auth_email = '';
	private $input_h = '';
	private $smarty_assign = array();
	private $auth_ok = FALSE;
	private $error = '';
	private $loginpageid = 0;
	private $input_m = 0;

	function __construct($pdo,&$page,$domaininfo) {
		parent::__construct($pdo, $page);
		$this->domaininfo = $domaininfo;

		// Default value for template view
		$this->smarty_assign['login_block'] = 'login';
		
	}

	public function readInput() {
		$tmp = http_get_var('logout','f');
		if( $tmp != 'f' ) {
			$this->input_logout = TRUE;
		}
		$this->input_auth_user = http_get_var('auth_user');
		if( $this->input_auth_user == '' ) {
			$this->input_auth_user = http_get_var('au');
		}
		if( $this->input_auth_user == '' && isset($_SESSION['_username']) ) {
			$this->input_auth_user = $_SESSION['_username']; // required for change-password-requests
		}
		$this->input_auth_pass = http_get_var('auth_pass');
		$this->input_auth_newpass = http_get_var('auth_newpass');
		$this->input_auth_pass2 = http_get_var('auth_pass2');
		$this->input_auth_email = http_get_var('auth_email');
		$this->input_m = http_get_var('m',0);
		$this->input_h = http_get_var('h');

		if(isset($_SESSION['_login_ok']) && $_SESSION['_login_ok'] == 1) {
			$this->auth_ok = TRUE;
		}

	}

	protected  function loglogin($accountid) {
		$SQL = "INSERT INTO logins (accountid,logintime) VALUES (".$accountid.",NOW())";
		try {
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($accountid));
		} catch (PDOException $e) {
			print $e;
		}
	}

	/**
	 * Validate Input
	 */
	private function validateAccountData() {
		$ret = TRUE;
		if(!preg_match('/^[a-z0-9-_.@!?:;,]{3,30}$/i',$this->input_auth_user)) {
			// Fehler im Nickname
			$this->smarty_assign['err_auth_user'] = 'Dieser Benutzername enth&auml;lt ung&uuml;ltige Zeichen, ist zu kurz oder zu lang! (3-30 Zeichen)';
			$ret = FALSE;
		}
		$username_is_free = TRUE;
		try {
			$SQL = 'SELECT COUNT(accountid) AS ctr,username FROM account WHERE username=? GROUP BY username';
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($this->input_auth_user));
			if( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
				if( $row['ctr'] != 0 ) {
					$username_is_free = FALSE;
				}
			}
			$st->closeCursor();
		} catch (PDOException $e) {
			print $e;
		} catch (Exception $e) {
			print $e;
		}
		if( ! $username_is_free ) {
			$this->smarty_assign['err_auth_user'] = 'Dieser Benutzername ist bereits vergeben!';
			$ret = FALSE;
		}
		
		if($this->input_auth_pass == '') {
			$this->smarty_assign['err_auth_pass'] = 'Du hast kein Passwort angegeben!';
			$ret = FALSE;
		}elseif($this->input_auth_pass2 != $this->input_auth_pass) {
			$this->smarty_assign['err_auth_pass'] = 'Deine Passw&ouml;rter stimmen nicht &uuml;berein!';
			$ret = FALSE;
		}

		$mrfc822 = new Mail_RFC822();
		if($mrfc822->isValidInetAddress($this->input_auth_email) == FALSE) {
			$this->smarty_assign['err_auth_email'] = 'Diese Adresse ist ung&uuml;ltig!';
			$ret = FALSE;
		}
		return $ret;
	}

	/**
	 * Validate Input for change-password-request
	 */
	private function validateChPasswordData() {
		$ret = TRUE;
		$auth = $this->processLogin(FALSE);
		
		if( FALSE == $auth ) {
			$this->smarty_assign['err_auth'] = TRUE;
			$ret = FALSE;
		}
		
		if($this->input_auth_newpass == '') {
			$this->smarty_assign['err_auth_pass'] = 'Du hast kein neues Passwort angegeben!';
			$ret = FALSE;
		}elseif($this->input_auth_pass2 != $this->input_auth_newpass) {
			$this->smarty_assign['err_auth_pass'] = 'Deine neuen Passw&ouml;rter stimmen nicht &uuml;berein!';
			$ret = FALSE;
		}

		return $ret;
	}

	public function processInput() {
		// AutoLogout
		if( $this->auth_ok ) {
			if($this->input_logout == TRUE) {
				unset($_SESSION['_login_ok']);
				unset($_SESSION['_acl']);
				unset($_SESSION['_accountid']);
				unset($_SESSION['_username']);
				$this->auth_ok = FALSE;
				$this->smarty_assign['login_block'] = 'loginform';
			}
		}

		switch($this->input_m) {
			case "login":
				// process login request
				$this->auth_ok = $this->processLogin();
				break;
			case "newform":
				// show form for new Registration
				$this->smarty_assign['login_block'] = 'newform';
				$this->smarty_assign['auth_user'] = $this->input_auth_user;
				$this->smarty_assign['auth_email'] = $this->input_auth_email;
				break;
			case "new":
				// save new Account or redisplay form
				$this->smarty_assign['login_block'] = 'newform';
				if( $this->validateAccountData() ) {
					// save new Account
					$newacc = $this->createAccount($this->input_auth_user,$this->input_auth_pass,$this->input_auth_email);
					if( $newacc != null ) {
						$rc = $this->sendConfirmMail($newacc);
						$this->smarty_assign['login_block'] = 'confirmmailsend';
					}
				} else {
					// display user and email in Form again
					$this->smarty_assign['auth_user'] = $this->input_auth_user;
					$this->smarty_assign['auth_email'] = $this->input_auth_email;
				}
				// save new account or
				// display form for new Account
				break;
			case "c":
				// check account confirmation hash an display confirmation page
				if( $this->input_auth_user != '' && $this->input_h != '' ) {
					$u = $this->getAccount($this->input_auth_user);
					if( $u != null ) {
						if($u['h'] == $this->input_h) {
							// hash for this user confirmed
							try {
								$SQL = 'UPDATE account SET active=1 WHERE accountid=?';
								$st = $this->pdo->prepare($SQL);
								$st->execute(array($u['accountid']));
								$this->smarty_assign['login_block'] = 'confirmed';
							} catch ( PDOException $e ) {
								print $e;
							}
						}
					}
				}
				break;
				case "newpwform":
					// display form for new password requests
					$this->smarty_assign['login_block'] = 'newpwform';
					break;
				case "newpw":
					// generate a new Password an send it per mail
					$new_pw = $this->new_password($this->input_auth_user,$this->input_auth_email);
					if( $new_pw != FALSE ) {
						$u = $this->getAccount($this->input_auth_user);
						$u['newpw'] = $new_pw;
						$rc = $this->sendNewPasswordMail($u);
						$this->smarty_assign['login_block'] = 'newpwconfirm';
					} else {
						// user not found
						$this->smarty_assign['err_user_not_found'] = TRUE;
						$this->smarty_assign['login_block'] = 'newpwform';
					}
					break;
				case "chpwform":
					// display change-password-form
					$this->smarty_assign['login_block'] = 'chpwform';
					break;
				case "chpw":
					// change password or redisplay form
					if( FALSE == $this->validateChPasswordData() ) {
						$this->smarty_assign['login_block'] = 'chpwform';
					} else {
						if( TRUE == $this->updatePassword($this->input_auth_user,$this->input_auth_newpass) ) {
							$this->smarty_assign['login_block'] = 'chpwconfirm';
						} else {
							$this->smarty_assign['login_block'] = 'chpwform';
						}
					}
					break;
		}
		$this->smarty_assign['auth_ok'] = $this->auth_ok;
		$this->smarty_assign['error'] = $this->error;
		$this->smarty_assign['loginpage'] = $this->loginpageid;
		if( isset($_SESSION['_username']) ) {
			$this->smarty_assign['username'] = $_SESSION['_username'];
		}
		
	}

	/**
	 * Send confirmationmail.
	 */
	private function sendConfirmMail($user) {
		
		$msg = "Hallo ".$user['username'].",\n\n"."Damit deine Registrierung erfolgreich abgeschlossen werden kann,";
		$msg .= " klicke bitte auf folgenden Link:\n\n";
		if($_SERVER['HTTPS'] or $_SERVER['SERVER_PORT'] == 443)
			$msg .= 'https://'.$_SERVER['SERVER_NAME'];
		else
			$msg .= 'http://'.$_SERVER['SERVER_NAME'];
		if( !in_array($_SERVER['SERVER_PORT'], ARRAY(80,443)) )
			$msg .= ':' . $_SERVER['SERVER_PORT'];
		$msg .= '/'.get_script_name().'?p='.$this->loginpageid.'&m=c&h='.$user['h'].'&au='.$user['username'];
		$msg .= "\n";
		$msg .= "\nNeuigkeiten zur Webseite werden auf der Mailingliste bekanntgegeben.";
		$msg .= "\nDie Anmeldeseite der Mailingliste findest Du unter http://lists.lugcamp.org/cgi-bin/mailman/listinfo/teilnehmer";
		$msg .= "\n\nWir freuen uns auf Dich\n\ndie Mitglieder der LUG Flensburg";

		$send_mail	= my_mailer($this->domaininfo['email'],$user['email'],'Registrierung auf ' . $_SERVER['SERVER_NAME'],$msg);
		return $send_mail;
	}

	/**
	 * Send new password mail.
	 */
	private function sendNewPasswordMail($user) {
		
		$msg = "Moin moin,\n\n";
		$msg .= "in Deinem Namen wurde auf der Webseite " . $_SERVER['SERVER_NAME'] . " ein neues Passwort angefordert.\n\n";
		$msg .= "Sollte die Passwortanforderung nicht von Dir selbst angestossen worden sein, informiere uns bitte.\n\n";
		$msg .= "Das neue Passwort lautet:\n\n";
		$msg .= $user['newpw'];
		$msg .= "\n\n";
		$msg .= "Mit dem neuen Passwort kannst Du Dich auf der Webseite jetzt anmelden.\n\n";
		
		$send_mail	= my_mailer($this->domaininfo['email'],$user['email'],'Passwortanforderung auf ' . $_SERVER['SERVER_NAME'],$msg);
		return $send_mail;
	}

	/**
	 * Check Username and Password and set _SESSION Variables after successfull login.
	 */
	private function processLogin($sessionmodify = true) {
		$ret = FALSE;
		if($this->input_auth_user != '' && $this->input_auth_pass != '') {
			// Formular abgeschickt
			try {
				$SQL = "SELECT accountid,username,acl FROM account WHERE username=? AND passwd=MD5(?) AND active=1";
				$st = $this->pdo->prepare($SQL);
				$st->execute(array($this->input_auth_user,$this->input_auth_pass));
				if($row = $st->fetch(PDO::FETCH_ASSOC)) {
						if($row) {
							if( $sessionmodify ) {
								$_SESSION['_login_ok'] = 1;
								$_SESSION['_accountid'] = $row['accountid'];
								$_SESSION['_username'] = $row['username'];
								$arry = explode(",",$row['acl']);
								$arry[]= 'user'; // every authentificated User is in Role "user"
								$_SESSION['_acl'] = join(',',$arry);
								$this->loglogin($row['accountid']);
							}
							$ret = TRUE;
						}
				}
				$st->closeCursor();
			}catch (PDOException $e) {
				print $e;
			}
		}
		return $ret;
	}

	public function getOutputMethod() {
		return Plugin::OUTPUT_METHOD_SMARTY;
	}

	/**
	 * @return Filename of Smarty-Template.
	 */
	public function getSmartyTemplate() {
		return 'page.login.html';
	}

	/**
	 * @return Data for Smarty::assign()
	 */
	public function getSmartyVariables() {
		return $this->smarty_assign;
	}

	public function setLoginPage($pageid) {
		if( is_numeric($pageid) ) {
			$this->loginpageid = $pageid;
		}
	}

	/**
	 * Create a new Account.
	 */
	protected function createAccount($user,$pass,$email) {
		$ret = null;
		try {
			$SQL = 'INSERT INTO account (username,passwd,email,crdate,active) VALUES 
			(?,MD5(?),?,NOW(),0)
			';
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($user,$pass,$email));
			$ret = $this->getAccount($user);
		} catch (PDOException $e) {
			print $e;
		}
		return $ret;
	}

	/**
	 * Get Account-Data by Username.
	 */
	protected function getAccount($user) {
		try {
			// create per-user-hash for confirmation-email
			$SQL = 'SELECT a.*,SUBSTR(MD5(CONCAT(a.username,a.email,a.accountid)),1,6) AS h FROM account a WHERE a.username=?';
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($user));
			if( $row = $st->fetch( PDO::FETCH_ASSOC ) ) {
				$ret = $row;
			}
			$st->closeCursor();
		} catch (PDOException $e) {
			print $e;
		}
		return $ret;
	}

	/**
	 * Create a new random passworda
	 *
	 * @return new password or FALSE if user not found
	 */
	protected function new_password($user, $email)
	{
		$content = "qwertzupasdfghkyxcvbnm";
		$content .= "123456789";
		srand((double)microtime()*1000000);
		$rand_password = '';
		for($i = 0; $i < 8; $i++)
		{
			$rand_password .= substr($content,(rand()%(strlen ($content))), 1);
		}
		try {
			$SQL = "UPDATE account SET passwd = MD5(?) WHERE username = ? AND email = ?";
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($rand_password,$user,$email));
			if($st->rowCount() != 1) {
				$rand_password = FALSE;
			}
		} catch ( PDOException $e) {
			print $e;
		}
		return $rand_password;
	}

	/**
	 * Update Password in Database without random generation and mail.
	 */
	protected function updatePassword($user,$newpasswd) {
		$ret = FALSE;
		try {
			$SQL = "UPDATE account SET passwd = MD5(?) WHERE username = ?";
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($newpasswd,$user));
			if($st->rowCount() == 1) {
				$ret = TRUE;
			}
		} catch ( PDOException $e) {
			print $e;
		}
		return $ret;
	}

	public function getAdminNavigation() {
		return array();
	}

}

?>
