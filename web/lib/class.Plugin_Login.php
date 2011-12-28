<?php

require_once('lib/func.http_get_var.php');
require_once('lib/class.Plugin.php');

class Plugin_Login extends Plugin {

	private $pdo = null;
	private $page = null;
	private $input_logout = FALSE;
	private $input_auth_user = '';
	private $input_auth_pass = '';
	private $input_email = '';
	private $input_newpw = 0;
	private $smarty_assign = array();
	private $auth_ok = FALSE;
	private $error = '';
	private $loginpageid = 0;

	function __construct($pdo,&$page) {
		$this->pdo = $pdo;
		$this->page = $page;
	}

	public function readInput() {
		$tmp = http_get_var('logout','f');
		if( $tmp != 'f' ) {
			$this->input_logout = TRUE;
		}
		$this->input_auth_user = http_get_var('auth_user');
		$this->input_auth_pass = http_get_var('auth_pass');
		$this->input_newpw = http_get_var('newpw',0);
		$this->input_email = http_get_var('email');

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

	public function processInput() {
		// AutoLogout
		if( $this->auth_ok ) {
			if($this->input_logout == TRUE) {
				unset($_SESSION['_login_ok']);
				unset($_SESSION['_acl']);
				unset($_SESSION['_accountid']);
				unset($_SESSION['_username']);
				$this->auth_ok = FALSE;
			}
		}

		$ret = FALSE;
		if($this->input_auth_user != '' && $this->input_auth_pass != '') {
			// Formular abgeschickt
			try {
				$SQL = "SELECT accountid,username,acl FROM account WHERE username=? AND passwd=MD5(?) AND active=1";
				$st = $this->pdo->prepare($SQL);
				$st->execute(array($this->input_auth_user,$this->input_auth_pass));
				if($row = $st->fetch(PDO::FETCH_ASSOC)) {
						if($row) {
							$_SESSION['_login_ok'] = 1;
							$_SESSION['_accountid'] = $row['accountid'];
							$_SESSION['_username'] = $row['username'];
							$arry = explode(",",$row['acl']);
							$arry[]= 'user'; // every authentificated User is in Role "user"
							$_SESSION['_acl'] = join(',',$arry);
							$this->loglogin($row['accountid']);
							$ret = TRUE;
						}
				}
				$st->closeCursor();
			}catch (PDOException $e) {
				print $e;
			}
		}
		$this->smarty_assign['newpw'] = $this->input_newpw;
		$this->smarty_assign['auth_ok'] = $this->auth_ok;
		$this->smarty_assign['error'] = $this->error;
		$this->smarty_assign['loginpage'] = $this->loginpageid;

		return $ret;
	}

	public function checkNewPw() {
		if( $this->input_newpw == 1 ) {
			// show form
			// TODO
		}
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
			$SQL1 = "UPDATE account SET passwd = MD5(?) WHERE username = ? AND email = ?";
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
}

?>
