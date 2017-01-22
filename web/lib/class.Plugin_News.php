<?php

require_once('lib/func.http_get_var.php');
require_once('lib/class.Plugin.php');
require_once('lib/class.News.php');

class Plugin_News extends Plugin {

	const VIEWMODE_OVERVIEW = 0;
	const VIEWMODE_SINGLE = 1;

	private $news = null;
	private $domain = null;
	private $eintragid = null;

	private $enable_edit = false;

	private $in = null;
	private $smarty_assign = null;

	private $viewMode = self::VIEWMODE_OVERVIEW;

	function __construct($pdo,$page,$eintragid,$domain) {
		parent::__construct($pdo, $page);
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
			$this->in['edited_title'] = http_get_var('news_title');
			$this->in['edited_short'] = http_get_var('news_short');
			$this->in['edited_txt'] = http_get_var('news_txt');
			$this->in['news_submit'] = http_get_var('news_submit');
		}
	}

	public function processInput() {

		$this->smarty_assign = ARRAY();	
		$this->smarty_assign['PAGEID'] = $this->page['pageid'];

		if($this->viewMode == self::VIEWMODE_SINGLE) {
			$this->smarty_assign = $this->news->getSingleNews($this->eintragid);
		}elseif($this->viewMode == self::VIEWMODE_OVERVIEW) {
			$this->smarty_assign['NEWSLISTE'] = $this->news->getNews();
		}

		// do nothing more if we are not in edit mode..
		if( !$this->enable_edit)
			return;

		if( $this->enable_edit )
		{
			$this->smarty_assign['ENABLE_EDITOR'] = true;

			// only save if content has been altered..
			if($this->in['news_submit'] == "submitted") {
				if($this->viewMode == self::VIEWMODE_SINGLE) {
					$currentNews = $this->news->getSingleNews($this->eintragid);
					if($this->in['edited_short'] != $currentNews['short'] 
						|| $this->in['edited_txt'] != $currentNews['txt'])
					{
						$this->news->updateNews(
							$this->eintragid,
							$this->in['edited_short'],
							$this->in['edited_txt']);
						$this->loadNews();
					}
				}elseif($this->viewMode == self::VIEWMODE_OVERVIEW) {
					// TODO implement new News
					$this->news->addNews(
						$this->in['edited_title'], 
						$this->in['edited_short'], 
						$this->in['edited_txt'], 
						$_SESSION['_accountid']);
				}
			}
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
		return $this->smarty_assign;
	}

	public function getAdminNavigation()
	{
		$ret = parent::getAdminNavigation();
		$ret[] = ARRAY(
				'pageid' => null,
				'title' => 'News schreiben',
				'url' => 'javascript:show_window(\'news_addform\');',
		);
/* NEEDED AS NOT News dependant here in the top admin nav ??
		$ret[] = ARRAY(
				'pageid' => null,
				'title' => 'News bearbeiten',
				'url' => 'javascript:editor_show();',
		);
		$ret[] = ARRAY(
				'pageid' => null,
				'title' => 'News löschen',
				'url' => 'javascript:news_delete_show();',
		);
*/
		return $ret;
	}
}

?>
