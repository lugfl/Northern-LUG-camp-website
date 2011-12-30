<?php

require_once('lib/func.http_get_var.php');
require_once('lib/class.Plugin.php');

class Plugin_MyCamp_Rechnung extends Plugin {

	private $pdo = null;
	private $page = null;
	private $events = null;
	private $enable_edit = false;
	private $edited_content = null;

	private $domain = null;

	function __construct($pdo,$page,$domain) {
		$this->pdo = $pdo;
		$this->page = $page;
		$this->domain = $domain;
		$this->events = new Events($pdo);
	}

	public function enableEditing()
	{
		$this->enable_edit = true;
	}

	public function readInput() {
		// get the edited content from the browser
		if(http_get_var('editor') == 1)
			$this->edited_content = http_get_var('codeeditor');
	}

	public function processInput() {
		// do nothing if we are not in edit mode..
		if(!$this->enable_edit || !isset($this->edited_content))
			return;

		// only save if content has been altered..
/* TODO
		if($this->edited_content != $this->page['content'])
		{
			$SQL = "UPDATE `content_page` SET `content`=? WHERE `pageid`=?";
			$st = $this->pdo->prepare($SQL);
			$res = $st->execute( ARRAY($this->edited_content, $this->page['pageid']) );
			if(!$res)
				throw new Exception("Could not update pagecontent..");

			// update content we are going to display..
			$this->page['content'] = $this->edited_content;
		}
*/
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
		return 'page.mycamp_rechnung.html';
	}

	public function getSmartyVariables()
	{
		$ret = array();
		$ret['PAGEID'] = $this->page['pageid'];
		if(http_get_var('anmeldung') != null) {
			$ret = $this->events->getEventRegistration($anmeldungid);
			$ret['rechnung_block'] = 'anmeldung';
		}else{
			$ret['EVENTS'] = $this->events->getEventRegistrationsForAccount(
				$this->domain['domainid'], 
				$_SESSION['_accountid']);
			$ret['ARTIKEL'] = $this->events->getBoughtArtikelForAccount(
				$_SESSION['_accountid']);
			$ret['rechnung_block'] = 'overview';
		}

		if( $this->enable_edit )
		{
			$ret['ENABLE_EDITOR'] = true;
			$ret['XINHA_DIR'] = XINHA_WEBROOT;
		}
		return $ret;
	}
}

?>
