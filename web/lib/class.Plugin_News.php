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
	private $domain = null;
	private $eintragid = null;

	private $enable_edit = false;

	private $edited_short = null;
	private $edited_txt = null;

	private $viewMode = self::VIEWMODE_OVERVIEW;

	function __construct($pdo,$page,$eintragid,$domain) {
		$this->pdo = $pdo;
		$this->page = $page;
		$this->news = new News($pdo,$domain['domainid']);
		$this->domain = $domain;
		if($eintragid > 0) {
			$this->eintragid = $eintragid;
			$this->viewMode = self::VIEWMODE_SINGLE;
		}
	}

	public function enableEditing()
	{
		$this->enable_edit = true;
	}

	public function readInput() {
		// get the edited content from the browser
		if(http_get_var('editor') == 1) {
			$this->edited_title = http_get_var('news_title');
			$this->edited_short = http_get_var('news_short');
			$this->edited_txt = http_get_var('news_txt');
		}
	}

	public function processInput() {
		// do nothing if we are not in edit mode..
		if( !$this->enable_edit || (!isset($this->edited_short) || !isset($this->edited_txt) ) )
			return;

		// only save if content has been altered..
		if($this->viewMode == self::VIEWMODE_SINGLE) {
			$currentNews = $this->news->getSingleNews($this->eintragid);
			if($this->edited_short != $currentNews['short'] || $this->edited_txt != $currentNews['txt'])
			{
				$this->news->updateNews($this->eintragid,$this->edited_short,$this->edited_txt);
				$this->loadNews();
			}
		}elseif($this->viewMode == self::VIEWMODE_OVERVIEW) {
			// TODO implement new News
			$this->news->addNews($this->edited_title, $this->edited_short, $this->edited_txt, $_SESSION['_accountid']);
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
		if($this->viewMode == self::VIEWMODE_SINGLE) {
			return 'page.news_eintrag.html';
		}
		return 'page.news.html';
	}

	public function getSmartyVariables()
	{
		$ret = ARRAY();	
		if( $this->enable_edit )
		{
			$ret['ENABLE_EDITOR'] = true;
			$ret['XINHA_DIR'] = XINHA_WEBROOT;
		}

		if($this->viewMode == self::VIEWMODE_SINGLE) {
			$ret = $this->news->getSingleNews($this->eintragid);
			return $ret;
		}
		$ret['PAGEID'] = $this->page['pageid'];
		$ret['NEWSLISTE'] = $this->news->getNews();
		return $ret;
	}
}

?>
