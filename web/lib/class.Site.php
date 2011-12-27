<?php


class Site {

	const PAGETYPE_TEXT_HTML = 1;
	const PAGETYPE_TEXT_WIKI = 2;

  protected $pdo = null;
	protected $domain = null;

  function __construct($pdo) {
		$this->pdo = $pdo;
		$this->searchMyDomain();
  }

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

	public function getDomain() {
		return $this->domain;
	}
	
	public function getPage($pageid=null) {

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
	  }
		if($st != null) {
			$st->closeCursor();
		}
  	return $ret;
	}

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
					// @todo Implement Wiki-Syntaxparser
					$ret = '<pre>' . $page['content'] . '</pre>';
				default:
					$ret = '404er';
					break;
			} // switch pagetypeid
		}
		return $ret;
	}

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
		}	else {
			$ret[]= $pageid;
		}
		return $ret;
	}
}

?>
