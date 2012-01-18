<?php

abstract class Plugin {

	const OUTPUT_METHOD_BUILDIN = 0;
	const OUTPUT_METHOD_SMARTY = 1;

	const METHOD_GET = 1;
	const METHOD_POST = 2;
	const METHOD_HEAD = 3;

	protected $pdo = null;
	protected $page = null;

	public function __construct($pdo, $page)
	{
		$this->pdo = $pdo;
		$this->page = $page;
	}

	/**
	 */
	public function getRequestMethod() {
		$ret = 0;
		switch( $_SERVER["REQUEST_METHOD"] ) {
			case "GET":
				$ret = Plugin::METHOD_GET;
				break;
			case "POST":
				$ret = Plugin::METHOD_POST;
				break;
			case "HEAD":
				$ret = Plugin::METHOD_HEAD;
				break;
		}
		return $ret;
	}

	/**
	 * Read all required GET/POST Variables, but do nothing with ut
	 */
	abstract public function readInput();

	/**
	 * Process variables from readInput() and exec Changes or read Content
	 */
	abstract public function processInput();

	/**
	 * Return the Method for Output-Generated.
	 *
	 * currently only buildin with getOutput() or Smarty-based
	 */
	abstract public function getOutputMethod();

	/**
	 * Return Array with Admin-Navigation Links.
	 *
	 * Each Item should have the followin Attributes:
	 *
	 * - pageid
	 * - url (incl. all required URL-Parameter)
	 * - title
	 */
	public function getAdminNavigation()
	{
		// return default actions for all plugins/pages
		return ARRAY(
				ARRAY(
					'pageid' => null,
					'title' => 'Seite erstellen',
					'url' => 'javascript:show_window(\'page_addform\');',
				),
				ARRAY(
					'pageid' => null,
					'title' => 'Seite löschen',
					'url' => 'javascript:show_window(\'page_delete\');',
				),
/* NOT IMPLEMENTED yet..				ARRAY(
					'pageid' => null,
					'title' => 'Seite verschieben',
					'url' => 'javascript:show_window(\'page_move\');',
				),
				ARRAY(
					'pageid' => null,
					'title' => 'Seite umbenennen',
					'url' => 'javascript:show_window(\'page_rename\');',
				)
*/		);
	}

	/**
	 * Processes Admin related input, similar to what
	 * readInput() and processInput() are doing
	 * but only gets called when in admin role
	 *
	 * Remember to call this parent when subclassing the function !
	 */
	public function processAdminInput()
	{
		// process action dependant code
		switch(http_get_var('a'))
		{
			case 'page_add':
				global $site;
				// create the page using Site class
				$role = http_get_var('page_role');
				if($role == "-")
					$role = NULL;
				$new_pageid = $site->createPage( http_get_var('page_title'), Site::PAGETYPE_TEXT_HTML, http_get_var('page_relation'), http_get_var('page_pos'), $role);
				if($new_pageid)
				{
					// redirect to created page
					header ("HTTP/1.1 301 Moved Permanently");
					header ("Location: /?p=".$new_pageid);
					exit();
				}
				break;
			case 'page_delete':
				global $site;
				$redirect_to = $site->deletePage($this->page['pageid']);
				if($redirect_to)
				{
					// redirect to created page
					header ("HTTP/1.1 301 Moved Permanently");
					header ("Location: /?p=".$redirect_to);
					exit();
				}
				break;
                }
	}

	protected function checkMaintenance() {
		global $MAINTENANCE_MODE;
		$ret = FALSE;
		if(isset($MAINTENANCE_MODE) && $MAINTENANCE_MODE) {
			$ret = TRUE;
		}
		return $ret;
	}
}

?>
