<?php

require_once('lib/func.http_get_var.php');
require_once('lib/class.Plugin.php');

class Plugin_MyCamp_Rechnung extends Plugin {

	private $pdo = null;
	private $page = null;
	private $events = null;
	private $enable_edit = false;

	private $domain = null;

	private $in = null;
	private $smarty_assign = null;

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
		$this->in['anmeldungid'] = http_get_var('anmeldung');
		/* TODO implement edit-mode
		$this->in['editor'] = http_get_var('editor');

		// get the edited content from the browser
		if($this->in['editor'] == 1)
			$this->in['codeeditor'] = http_get_var('codeeditor');
		*/
	}

	public function processInput() {

		$this->smarty_assign['PAGEID'] = $this->page['pageid'];
		if($this->in['anmeldungid'] > 0) {
			// display single registration
			$this->smarty_assign = $this->events->getEventRegistration($this->in['anmeldungid']);
			foreach(Plugin_Events::$ANREISE as $anreiseart) {
				if($anreiseart['anreiseid'] == $this->smarty_assign['ANMELDUNG']['arrival']) {
					$this->smarty_assign['ANMELDUNG']['anreise'] = $anreiseart['name'];
					break;
				}
			}
			$this->smarty_assign['rechnung_block'] = 'anmeldung';
		}else{
			// create list of registrations
			$this->smarty_assign['PERSONEN'] = $this->events->getEventRegistrationsForAccount(
				$_SESSION['_accountid'], 
				$this->domain['domainid']);
			$this->smarty_assign['ARTIKEL'] = $this->events->getBoughtArtikelForAccount(
				$_SESSION['_accountid'],
				$this->domain['domainid']);
			$this->smarty_assign['rechnung_block'] = 'overview';
		}

/* TODO implement edit-mode for events...
		if( $this->enable_edit )
		{
			$this->smarty_assign['ENABLE_EDITOR'] = true;
			$this->smarty_assign['XINHA_DIR'] = XINHA_WEBROOT;
		}

		// only save if content has been altered..

		if($this->in['codeeditor'] != $this->page['content'])
		{
			$SQL = "UPDATE `content_page` SET `content`=? WHERE `pageid`=?";
			$st = $this->pdo->prepare($SQL);
			$res = $st->execute( ARRAY($this->in['codeeditor'], $this->page['pageid']) );
			if(!$res)
				throw new Exception("Could not update pagecontent..");

			// update content we are going to display..
			$this->page['content'] = $this->in['codeeditor'];
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

	public function getSmartyVariables() {
		return $this->smarty_assign;
	}

	public function getAdminNavigation() {
		return array();
	}
}

?>
