<?php

$PAGE['root']['name'] = "Home";
$PAGE['root']['navilevel'] = 0;
$PAGE['root']['login_required'] = 0;
$PAGE['root']['phpclass'] = 'HtmlPage';
$PAGE['root']['parent'] = '_';

class HtmlPage {

	var $name;
	var $navilevel;
	var $login_required;

	/**
	 * Meta-Header fuer diese Seite
	 */
	function getMeta() {
		return "";
	}
	
	/**
	 * hauptcontent fuer diese Seite
	 */
	function getContent() {
		return "";
	}

	/**
	 *
	 */
	function getName() {
		return $this->name;
	}

	/**
	 *
	 */
	function getNavilevel() {
		return $this->navilevel;
	}

	/**
	 *
	 */
	function is_login_required() {
		return $this->login_required;
	}
}

?>
