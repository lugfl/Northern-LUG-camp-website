<?php

require_once('lib/func.http_get_var.php');
require_once('lib/class.Plugin.php');
require_once('lib/class.News.php');

class Plugin_News extends Plugin {

	const VIEWMODE_OVERVIEW = 0;
	const VIEWMODE_SINGLE = 1;

	private $pdo = null;
	private $page = null;
	private $news = null;

	private $viewMode = VIEWMODE_OVERVIEW;

	function __construct($pdo,$page) {
		$this->pdo = $pdo;
		$this->page = $page;
		$this->news = new News($pdo);
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
		// TODO anpassen, page gibts nimmer
/*
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
*/	}

	public function getOutputMethod()
	{
		return Plugin::OUTPUT_METHOD_SMARTY;
	}

	/**
	 * @return Filename of Smarty-Template.
	*/
	public function getSmartyTemplate()
	{
		if($viewmode == VIEWMODE_SINGLE) {
			return 'page.news_eintrag.html';
		}
		return 'page.news.html';
	}

	public function getSmartyVariables()
	{
		$ret = ARRAY();
		if($viewmode == VIEWMODE_SINGLE) {
			$news = loadSingleNews();
			// TODO add correct category 
			$ret['CATEGORY'] = 'Allgemein';
			$ret['TITLE'] = $this->page['title'];
			$ret['SHORT'] = $this->page['short'];
			$ret['AUTHOR'] = $this->page['author'];
			if( isset($this->page['txt']) )
				$ret['TXT'] = $this->page['txt'];
			if( $this->enable_edit )
			{
				$ret['ENABLE_EDITOR'] = true;
				$ret['XINHA_DIR'] = XINHA_WEBROOT;
			}
			return $ret;
		}
		$ret['PAGEID'] = $this->page['pageid'];
		$ret['NEWSLISTE'] = $this->news->getNews();
		return $ret;
	}
}

?>
