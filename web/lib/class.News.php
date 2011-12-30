<?php

/**
 * Access class for News.
 */
class News {

	const NEWS_TEASER_COUNT = 5;

	private $pdo = null;
	private $domainid = 0;

	private $news = array();

	public function __construct($pdo,$domainid) {
		$this->pdo = $pdo;
		$this->domainid = $domainid;
		$this->loadNews();
	}

	/**
	 * Get List of countries
	 */
	public function getNews() {
		return $this->news;
	}

	public function getSingleNews($id) {
		for($i = 0; $i < count($this->news); $i++) {
			$eintrag = $this->news[$i];
			if($eintrag['eintragid'] == $id) {
				return $eintrag;
			}
		}
		return null;
	}

	/**
	 * Load latest News from database.
	 *
	 * current cached list ist cleared before.
	 */
	public function loadNews() {
		// clear current News-List
		$this->news = array();

		// load news from database
		try {
			$SQL = 'SELECT n.eintragid, n.title, n.short, n.txt, a.username as author, DATE_FORMAT(n.crdate,"%e.%c.%Y") AS date '
				.'FROM news_eintrag n '
				.'LEFT JOIN content_domain d ON d.domainid = n.domainid '
				.'LEFT JOIN account a ON n.accountid = a.accountid '
				.'WHERE d.domainid = '.$this->domainid.' '
				.'ORDER BY n.eintragid DESC '
				.'LIMIT '.self::NEWS_TEASER_COUNT;
			$st = $this->pdo->prepare($SQL);
			$st->execute();
			while($row = $st->fetch(PDO::FETCH_ASSOC)) {
				$this->news[] = $row;
			}
			$st->closeCursor();
		} catch( PDOException $e) {
			print $e;
		}
	}

	/**
	 * create a new DB-Entry
	 */
	public function addNews($title, $short, $txt, $accountid) {
		try {
			$SQL = 'INSERT INTO news_eintrag (title, short, txt, accountid, domainid, crdate) VALUES (?,?,?,?,?,NOW())';
			$st = $this->pdo->prepare($SQL);
			$st->execute( array($title, $short, $txt, $accountid, $this->domainid) );
			$st->closeCursor();
			$this->loadNews(); // List Modified; reload it
		} catch (PDOException $p) {
			print $p;
		}
	}

	public function updateNews($eintragid,$short,$txt) {
		try{
			$SQL = "UPDATE `news_eintrag` SET `short`=?, `txt`=? WHERE `eintragid`=?";
			$st = $this->pdo->prepare($SQL);
			$res = $st->execute( ARRAY($short, $txt, $eintragid) );
			$this->loadNews(); // List Modified; reload it
			if(!$res)
				throw new Exception("Could not update news..");
		} catch (PDOException $p) {
			print $p;
		}
	}
}

?>
