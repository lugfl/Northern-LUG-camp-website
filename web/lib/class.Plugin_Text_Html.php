<?php

require_once('lib/func.http_get_var.php');
require_once('lib/class.Plugin.php');

class Plugin_Text_Html extends Plugin {

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
		// nothing to be read for "static" content
	}

	public function processInput() {
		// nothing to process for "static" content
	}


	public function processAdminInput() {
		// call parent so its AdminInput handler is processed first
		parent::processAdminInput();

		// do nothing if we are not in edit mode..
		if(!$this->enable_edit)
			return;

		// get the edited content from the browser
		if(http_get_var('editor') == 1)
			$this->edited_content = http_get_var('codeeditor');

		// only save if content has been altered and entered..
		if($this->edited_content != $this->page['content'] && isset($this->edited_content))
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

	public function getAdminNavigation()
	{
		$ret = parent::getAdminNavigation();
		$ret[] =  ARRAY(
				'pageid' => null,
				'title' => 'Seite bearbeiten',
				'url' => 'javascript:editor_show();',
		);
		return $ret;
	}
}

?>
