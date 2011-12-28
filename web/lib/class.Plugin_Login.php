<?php

require_once('lib/func.http_get_var.php');

class Plugin_Login {

	private $pdo = null;

	function __construct($pdo) {
		$this->pdo = $pdo;

		// AutoLogout
		if(isset($_SESSION['_login_ok']) && $_SESSION['_login_ok'] == 1) {
			$logout = http_get_var('logout');
			if($logout) {
				unset($_SESSION['_login_ok']);
				unset($_SESSION['_acl']);
				unset($_SESSION['_accountid']);
				unset($_SESSION['_username']);
			}
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

	public function checkAuth() {
		global $_SESSION;
		$ret = FALSE;
		$auth_user = http_get_var('auth_user');
		$auth_pass = http_get_var('auth_pass');
		if($auth_user != '' && $auth_pass != '') {
			// Formular abgeschickt
			try {
				$SQL = "SELECT accountid,username,acl FROM account WHERE username=? AND passwd=MD5(?) AND active=1";
				$st = $this->pdo->prepare($SQL);
				$st->execute(array($auth_user,$auth_pass));
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
		$auth_user = '';
		$auth_pass = '';
		return $ret;
	}

	public function checkNewPw() {
		$newpw = http_get_var('newpw',0);
		$user = http_get_var('user');
		$email = http_get_var('email');
		
		if( $newpw == 1 ) {
			// show form
			// TODO
		}
	}

	public function getContent() {
		
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
