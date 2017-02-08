<?php

require_once('lib/func.http_get_var.php');
require_once('lib/class.Plugin.php');

class Plugin_Text_Html extends Plugin {

	private $enable_edit = false;
	private $edited_content = null;
	private $smartyTemplate = 'page.default.html';
	private $smartyVars = ARRAY();

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

		if(http_get_var('filemanager'))
			$this->processFilemanager();
		else
			$this->processSiteactions();
	}

	protected function processSiteactions()
	{
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

	protected function processFilemanager()
	{
		// process uploads
		if(@$_FILES['upload'])
		{
			if( stristr($_FILES['upload']['name'], '..' ) )
				throw new Exception('Security exception');
			if( !@is_uploaded_file($_FILES['upload']['tmp_name']) )
				throw new Exception('Not an HTTP upload...');
			if( !@getimagesize($_FILES['upload']['tmp_name']) )
				throw new Exception('Only image uploads are allowed...');
			// generate unique filename if filename is taken
			$suffix = 0;
			while(file_exists($uploadFilename = WEB_ROOT.USER_UPLOAD_DIR.'/'.($suffix?$suffix:'').$_FILES['upload']['name']))
				$suffix++;
			if( !@move_uploaded_file($_FILES['upload']['tmp_name'], $uploadFilename) )
				throw new Exception('Could not save upload..');
		}

		$this->smartyTemplate = 'page_editor.filemanager.tpl';
		$this->smartyVars['file_path'] = USER_UPLOAD_DIR;
		$dir  = opendir(WEB_ROOT.USER_UPLOAD_DIR);
		while( $file = readdir($dir) )
			if(!is_dir($file) && $file[0] != '.' )
				$this->smartyVars['filelist'][] = $file;
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
		return $this->smartyTemplate;
	}

	public function getSmartyVariables()
	{
		if( isset($this->page['content']) )
			$this->smartyVars['CONTENT'] = $this->page['content'];
		if( $this->enable_edit )
			$this->smartyVars['ENABLE_EDITOR'] = true;
		return $this->smartyVars;
	}

	public function getAdminNavigation()
	{
		$ret = parent::getAdminNavigation();
		$ret[] =  ARRAY(
				'pageid' => null,
				'title' => 'Seite bearbeiten',
				'url' => 'javascript:show_window(\'content_editor\');',
		);
		return $ret;
	}
}

?>
