<?php

require_once('lib/func.http_get_var.php');
require_once('lib/class.Plugin.php');

class Plugin_Text_Html extends Plugin {

	private $pdo = null;
	private $page = null;
	private $enable_edit = false;

	function __construct($pdo,$page) {
		$this->pdo = $pdo;
		$this->page = $page;
	}

	public function enableEditing()
	{
		$this->enable_edit = true;
	}

	public function readInput() {
		// static content. No Input needed
		return TRUE;
	}

	public function processInput() {
		// static content. No processing needed.
		return TRUE;
	}

	public function getOutputMethod()
	{
		return Plugin::OUTPUT_METHOD_SMARTY;
	}

	/**
	 * @return Filename of Smarty-Template.
	*/
	public function getSmartyTemplate()
	{
		return 'page.default.html';
	}

	public function getSmartyVariables()
	{
		$ret = ARRAY();
		if( isset($this->page['content']) )
			$ret['CONTENT'] = $this->page['content'];
		if( $this->enable_edit )
		{
			$ret['ENABLE_EDITOR'] = true;
			$ret['XINHA_DIR'] = XINHA_WEBROOT;
		}
		return $ret;
	}
}

?>
