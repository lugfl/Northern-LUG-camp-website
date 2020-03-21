<?php

require_once('lib/inc.database.php');

$PAGE['bemerkung']['name'] = "Bemerkung &auml;ndern";
$PAGE['bemerkung']['navilevel'] = 2;
$PAGE['bemerkung']['login_required'] = 1;
$PAGE['bemerkung']['phpclass'] = 'HtmlPage_bemerkung';
$PAGE['bemerkung']['parent'] = 'mycamp';

class HtmlPage_bemerkung extends HtmlPage {

	var $data = Array();
	var $errors = Array();

	function _readInput() {
		$this->data['a'] = http_get_var('a');
		$this->data['bemerkung'] = strip_tags(http_get_var('frm_bemerkung'));
	}

	function _verifyInput() {
		global $_SESSION;
		$username = $_SESSION['_username'] ? $_SESSION['_username'] : '';
		$accountid = $_SESSION['_accountid'] ? $_SESSION['_accountid'] : 0;
	}

	function getBemerkungForm() {
		$username = $_SESSION['_username'] ? $_SESSION['_username'] : '';
		$accountid = $_SESSION['_accountid'] ? $_SESSION['_accountid'] : 0;
		
		$SQL = "SELECT bemerkung FROM event_anmeldung WHERE accountid = '".$accountid."'";
		$res = my_query($SQL);
		$row = mysqli_fetch_assoc($res);
		
		$ret = '
		<p>Hier kannst Du die Bemerkung, die du uns bei der Anmeldung mitgeteilt hast &auml;ndern. Die &Auml;nderung wird sofort wirksam.</p>
		<form action="?" method="post">
			<input type="hidden" name="p" value="bemerkung"/>
			<input type="hidden" name="a" value="1"/>
			<p>
			<label for-id="frm_bemerkung">Bermerkung:</label><br/>
			<textarea name="frm_bemerkung" id="frm_passwd_alt" cols="70" rows="5">'.$row['bemerkung'].'</textarea>
			</p>

			<p>
			<input type="submit" value="&Uuml;bernehmen"/>
			</p>
		</form>
		';
		return $ret;
	}

	function updateBemerkung() {
		global $_SESSION;

		$ret = '';

		$username = $_SESSION['_username'] ? $_SESSION['_username'] : '';
		$accountid = $_SESSION['_accountid'] ? $_SESSION['_accountid'] : 0;

		$SQL1 = "UPDATE event_anmeldung SET bemerkung='".my_escape_string($this->data['bemerkung'])."' WHERE accountid='".$accountid."'";
		$res1 = my_query($SQL1);
		$ctr = my_affected_rows();
		if($ctr==1) {
			$ret .= '
			<p>Die Bemerkung wurde erfolgreich ge&auml;ndert.</p>
			';
		}else{
			$ret .= '
			<!-- Das klappte nich-->
			';
		}
		return $ret;
	}

	function getContent() {

		// Checken, ob die Seite wegen Wartungsarbeiten ausgeschaltet werden soll.
		// Funktion checkMaintenance() kommt aus class.HtmlPage.php
		$ret = $this->checkMaintenance();
		if($ret!='')
			return $ret;

		$this->_readInput();

		$ret .= '
		<h1>Bemerkung &auml;ndern</h1>
		';
		$a = http_get_var('a');
		switch($a) {
			case '1':
				$this->_verifyInput();
				if(count($this->errors)>0) {
					$ret .= $this->getBemerkungForm();
					$ret .= '<ul>';
					foreach($this->errors as $k=>$v) {
						$ret .= '<li>'.$v.'</li>';
					}
					$ret .= '</ul>';
				}else{
					$ret .= $this->updateBemerkung();
				}
			break;
			default:
				$ret .= $this->getBemerkungForm();
			break;
		}
		return $ret;
	}

}


?>
