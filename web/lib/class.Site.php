<?php

require_once('Text/Wiki.php');

class Site {

	const PAGETYPE_TEXT_HTML = 1;
	const PAGETYPE_TEXT_WIKI = 2;
	const PAGETYPE_PLUGIN_LOGIN = 3;
	const PAGETYPE_PLUGIN_EVENTS = 4;
	const PAGETYPE_PLUGIN_NEWS = 5;
	const PAGETYPE_PLUGIN_MYCAMP_RECHNUNG = 6;

	protected $pdo = null;
	protected $domain = null;
	private $pageCache = array();

	function __construct($pdo) {
		$this->pdo = $pdo;
		$this->searchMyDomain();
	}

	/**
	 * Search for matching domain configuration
	 */
	protected function searchMyDomain() {
		$ret = FALSE;
		$SQL = "SELECT domainid,name FROM content_domain WHERE name=?";
		$st = $this->pdo->prepare($SQL);
		$res = $st->execute(array($_SERVER['SERVER_NAME']));
		if ( $row = $st->fetch(PDO::FETCH_ASSOC)) {
			$this->domain = $row;
			$ret = TRUE;
		}
		if($st != null) {
			$st->closeCursor();
		}
		return $ret;
	}

	/**
	 * Get the current domain configuration
	*/
	public function getDomain() {
		return $this->domain;
	}
	
	/**
	* Get the Configuration of a specific page
	*/
	public function getPage($pageid=null) {

		if( $pageid != null && isset($this->pageCache[$pageid]) ) {
			return $this->pageCache[$pageid];
		}

		$ret = null;
		$SQL = "SELECT pageid,domainid,parentpageid,pagetypeid,title,content,navorder,acl FROM content_page ";
		$st = null;
		if( $pageid == null) {
		  	$SQL .= " WHERE domainid=? AND parentpageid IS NULL LIMIT 1";
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($this->domain['domainid']));
		} else  {
			$SQL .= "WHERE domainid=? AND pageid=?";
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($this->domain['domainid'],$pageid));
 		}
		if( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
			$ret = $row;
			$this->pageCache[$row['pageid']] = $row;
		}
		if($st != null) {
			$st->closeCursor();
		}
		return $ret;
	}

	/**
	* Get the navigation elements for the specified parentpage.
	*
	* @param int parentpageid, may be null for query root-Level navigation
	*/
	public function getNavigation($parentpageid = null) {
		$ret = null;
		$SQL = "SELECT pageid,domainid,parentpageid,title,navorder,acl FROM content_page ";
		$st = null;
		if( $parentpageid == null) {
	  		$SQL .= " WHERE domainid=? AND parentpageid IS NULL ORDER BY navorder";
			$st = $this->pdo->prepare($SQL);
	    		$st->execute(array($this->domain['domainid']));
		} else  {
			$SQL .= "WHERE domainid=? AND parentpageid=? ORDER BY navorder";
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($this->domain['domainid'],$parentpageid));
		}
		while( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
			$ret[] = $row;
		}
		if($st != null) {
			$st->closeCursor();
		}
		return $ret;
		
	}

	/**
	 * Read Page-Content from Database by using the right plugin
	 * 
	 * @deprecated
	 */
	public function getPageContent($pageid) {
		$ret = null;
		$page = $this->getPage($pageid);
		if($page == null) {
			$ret = '404er';
		} else {
			switch($page['pagetypeid']) {
				case Site::PAGETYPE_TEXT_HTML:
					$ret = $page['content'];
					break;
				case Site::PAGETYPE_TEXT_WIKI:
					// Neues Objekt instanziieren
					$wiki = new Text_Wiki();
					// Text nach XHTML formatieren
					$ret = $wiki->transform($page['content'], 'Xhtml');
				case Site::PAGETYPE_PLUGIN_LOGIN:
					// TODO
					break;
				default:
					$ret = '404er';
					break;
			} // switch pagetypeid
		}
		return $ret;
	}

	/**
	 * Get the pagetype for the specified page
	*/
	public function getPageType($pageid) {
		$page = $this->getPage($pageid);
		return $page['pagetypeid'];
	}

	/**
	 * Get the navigation-path from specified page to root-level
	*/
	function getRootPath($pageid) {
		$ret = array();

		$page = $this->getPage($pageid);
		if( $page == null ) {
			// Startseite existiert nicht
			return array();
		}
		$parentpageid = $page['parentpageid'];
		if( is_numeric($parentpageid) ) {
			// once more...
			$tmp = $this->getRootPath($page['parentpageid']);
			$ret = array_merge($ret,$tmp);
			$ret[]= $pageid;
		} else {
			$ret[]= $pageid;
		}
		return $ret;
	}


	/**
	 * Check if current user is authentificated
	 */
	public function auth_ok() {
		global $_SESSION;
		$ret = false;
		if(isset($_SESSION['_login_ok']) && $_SESSION['_login_ok'] == 1) {
			$ret = true;
		}
		return $ret;
	}

	/**
	 * Check if the current user is member of the specified role
	 */
	public function isInRole($rolename) {
		global $_SESSION;
		$ret = FALSE;

		if( $rolename == null || $rolename == '') {
			// used for anonymous browsing.
			// Database-Values of null or '' means "every user"
			return TRUE;
		}

		$arry = array();
		if( isset( $_SESSION['_acl'] ) ) {
			$arry = explode(",",$_SESSION['_acl']);
		}
		if( in_array($rolename,$arry) ) {
			$ret = TRUE;
		}
		return $ret;
	}

	/**
	 * Get ID of current logedin account
	 */
	public function getMyAccountID() {
		$ret = null;
		if( isset($_SESSION['_accountid']) ) {
			$ret = $_SESSION['_accountid'];
		}
		return $ret;
	}

}

?>
