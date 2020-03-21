<?php

class AuthUser {

	var $vorname;
	var $nachname;
	var $username;
	var $email;

	function __construct() {
		$this->vorname = '';
		$this->nachname = '';
		$this->username = '';
		$this->email = '';
	}

	function getVorname() {
		return $this->vorname;
	}

	function getNachname() {
		return $this->nachname;
	}

	function getUsername() {
		return $this->username;
	}

	function getEmail() {
		return $this->email;
	}
}


?>
