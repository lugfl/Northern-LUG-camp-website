<?php

require_once('Text/Wiki.php');

class Site {

	const PAGETYPE_TEXT_HTML = 1;
	const PAGETYPE_TEXT_WIKI = 2;
	const PAGETYPE_PLUGIN_LOGIN = 3;
	const PAGETYPE_PLUGIN_EVENTS = 4;
	const PAGETYPE_PLUGIN_NEWS = 5;
	const PAGETYPE_PLUGIN_MYCAMP_RECHNUNG = 6;
	const PAGETYPE_PLUGIN_ARTIKEL = 7;

	const PAGERELATION_BEFORE = 'before';
	const PAGERELATION_BELOW = 'below';
	const PAGERELATION_IN = 'in';

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
		$SQL = "SELECT domainid,name,email,sslname,templatestyle FROM content_domain WHERE name=? OR sslname=?";
		$st = $this->pdo->prepare($SQL);
		$res = $st->execute(array($_SERVER['SERVER_NAME'],$_SERVER['SERVER_NAME']));
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
		$SQL = "SELECT pageid,domainid,parentpageid,pagetypeid,title,content,navorder,acl,sslreq FROM content_page ";
		$st = null;
		if( $pageid == null) {
			// query default landingpage
		  	$SQL .= " WHERE domainid=? AND parentpageid IS NULL LIMIT 1";
			$st = $this->pdo->prepare($SQL);
			$st->execute(array($this->domain['domainid']));
		} else  {
			if( is_numeric($pageid) ) {
				$SQL .= "WHERE domainid=? AND pageid=?";
			} else {
				$SQL .= 'WHERE domainid=? AND alias=?';
			}
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
		$SQL = "SELECT pageid,domainid,parentpageid,title,navorder,acl,sslreq FROM content_page ";
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


	/**
	 * creates a new page and returns the created page id, or null if none was created
	 */
	public function createPage($title, $type = self::PAGETYPE_TEXT_HTML, $position_relation = self::PAGERELATION_AFTER, $position_to = NULL, $role = NULL )
	{
		$new_site = null;

		// check if page we want to create the relation really exists
		$sql = "SELECT pageid, parentpageid,  navorder FROM content_page WHERE domainid=? AND pageid=? LIMIT 1";
		$st = $this->pdo->prepare($sql);
		$st->execute(array($this->domain['domainid'],$position_to));
		if($page = $st->fetch(PDO::FETCH_ASSOC))
		{
			$parent_page = null;
			$navorder = 100;

			// calculate parent_page and navorder depending on relation and position_to
			switch($position_relation)
			{
				case self::PAGERELATION_BEFORE:
					$parent_page = $page['parentpageid'];
					$navorder = $page['navorder'] - 10;
					break;
				case self::PAGERELATION_BELOW:
					$parent_page = $page['parentpageid'];
					$navorder = $page['navorder'] + 10;
					break;
				case self::PAGERELATION_IN:
					// do not allow to create 3'rd level and deeper menu items
					if($page['parentpageid'] != 0)
						throw new Exception("Creating 3'rd level and deeper menu-items is not supported...");
					$parent_page = $position_to;
					$navorder = 100;
					break;
				default:
					throw new Exception("Page relation type not supported..");
			}

			// site seems to exists so insert new site..
			$ins = $this->pdo->prepare("INSERT INTO content_page (domainId, parentpageid, pagetypeid, title, content, crdate, navorder,acl) VALUES (?,?,?,?,?,CURDATE(),?,?)");
			$success = $ins->execute( ARRAY(
					(int)$this->domain['domainid'],
					$parent_page,
					(int)$type,
					$title,
					'',
					(int)$navorder,
					$role )
				);
			if($success)
				$new_site = $this->pdo->lastInsertId();
			$ins->closeCursor();
		}
		$st->closeCursor();
		return $new_site;
	}

	/**
	 * deletes and existing page and returns the parent page id, or null if none if deletion failed..
	 */
	public function deletePage($page_id)
	{
		$ret = null;
		$st = $this->pdo->prepare("SELECT pageid, parentpageid FROM content_page WHERE domainid=? AND pageid=? LIMIT 1");
		$st->execute( ARRAY( $this->domain['domainid'], $page_id ));
		if($page = $st->fetch(PDO::FETCH_ASSOC))
		{
			// check if page has subpages..
			$childs = $this->pdo->query("SELECT pageid FROM content_page WHERE parentpageid=".(int)$page_id);
			if($childs->rowCount() > 0)
				throw new Exception("Page can not be deleted as it still contains subpages. Please consider to remove those first.");

			// requested page seems to exists, so remove it...
			$success = $this->pdo->query("DELETE FROM content_page WHERE pageid=".(int)$page_id." LIMIT 1");
			if($success)
				$ret = $page['parentpageid'];
		}
		$st->closeCursor();
		return $ret;
	}

	/**
	 * Determine the IP version of the given IP address
	 * @param string $ip Address whose IP version shall be determined
	 * @return string|bool Can be 'IPv4' or 'IPv6' in case of success or FALSE on error
	 */
	function get_ip_version($ip) {
		// Patterns don't check the IP address for exact validity. But
		// in this case the following checks should be sufficient.
		$ipv6_patterns = array( // Complete IPv6 address
			'([a-f0-9]{1,4}:){7}[a-f0-9]{1,4}',
			// IPv6 address with stripped leading zeros
			':(:[a-f0-9]{1,4}){1,6}',
			// IPv6 address with stripped trailing zeros
			'([a-f0-9]{1,4}:){1,6}:',
			// IPv6 address with stripped zeros in the middle
			'([a-f0-9]{1,4}:){1,6}(:[a-f0-9]{1,4}){1,6}',
			// IPv6 address with only zeros
			'::'
		);

		if (preg_match(sprintf('/^%s$/i', implode('|', $ipv6_patterns)), $ip)) {
			return 'IPv6';
		}
		elseif (preg_match('/^(\d{1,3}\.){3}\d{1,3}$/', $ip)) {
			return 'IPv4';
		}
		else {
			return FALSE;
		}
	}

}

?>
