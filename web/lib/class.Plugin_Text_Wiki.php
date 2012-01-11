<?php

require_once('lib/func.http_get_var.php');
require_once('lib/class.Plugin.php');

class Plugin_Text_Wiki extends Plugin {

	private $pdo = null;
	private $page = null;
	private $enable_edit = false;
	private $edited_content = null;

	function __construct($pdo,$page) {
		$this->pdo = $pdo;
		$this->page = $page;
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
		return 'page.wiki.html';
	}

	public function getSmartyVariables()
	{
		$ret = ARRAY();
		if( isset($this->page['content']) )
			// Neues Objekt instanziieren
			$wiki = new Text_Wiki();
			// Text nach XHTML formatieren
			$content = $wiki->transform($this->page['content'], 'Xhtml');
			$ret['CONTENT_RAW'] = $this->page['content'];
			$ret['CONTENT_RENDERED'] = $content;
		if( $this->enable_edit )
		{
			$ret['ENABLE_EDITOR'] = true;
			$ret['XINHA_DIR'] = XINHA_WEBROOT;
		}
		return $ret;
	}

	public function getAdminNavigation() {
		return array();
	}

}

?>
