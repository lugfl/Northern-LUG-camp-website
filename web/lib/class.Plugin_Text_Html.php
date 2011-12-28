<?php

require_once('lib/func.http_get_var.php');
require_once('lib/class.Plugin.php');

class Plugin_Text_Html extends Plugin {

	private $pdo = null;
	private $page = null;

	function __construct($pdo,$page) {
		$this->pdo = $pdo;
		$this->page = $page;
	}

	public function readInput() {
		// static content. No Input needed
		return TRUE;
	}

	public function processInput() {
		// static content. No processing needed.
		return TRUE;
	}

	public function getOutputMethod() {
		return Plugin::OUTPUT_METHOD_BUILDIN;
	}

	public function getOutput() {
		$ret = '';
		if( isset($this->page['content']) ) {
			$ret = $this->page['content'];
		}
		return $ret;
	}
}

?>
