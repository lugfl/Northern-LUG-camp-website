<?php

/**
 * Access class for News.
 */
class News {

	const NEWS_TEASER_COUNT = 5;

	private $pdo = null;

	private $news = array();

	public function __construct($pdo) {
		$this->pdo = $pdo;
		$this->loadNews();
	}

	/**
	 * Get List of countries
	 */
	public function getNews() {
		return $this->news;
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
			$SQL = 'SELECT n.eintragid, n.title, c.name as category, n.short, n.txt, n.author, DATE_FORMAT(n.crdate,"%e.%c.%Y") AS date '
				.'FROM news_eintrag n '
				.'LEFT JOIN news_cat c ON n.catid = c.catid '
//				.'LEFT JOIN account a ON n.accountid = a.accountid'
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
	 * Search a News or create a new DB-Entry, if not exists.
	 * TODO copied from Countries, where is this used? Replace it with just an Add function ?
	 */
	public function searchOrAdd($title, $catid, $short, $txt, $author) {
		$ret = null;
		foreach( $this->news as $c ) {
			if( $c['title'] == $title ) {
				$ret = $c;
			}
		}
		if( $ret == null ) {
			// no Entry found
			try {
				$SQL = 'INSERT INTO news_eintrag (title, catid, short, txt, author,crdate) VALUES (?,?,?,?,?,NOW())';
				$st = $this->pdo->prepare($SQL);
				$st->execute( array($title, $catid, $short, $txt, $author) );
				$st->closeCursor();
				$this->loadNews(); // List Modified; reload it
				$ret = $this->SearchOrAdd($title, $catid, $short, $txt, $author);
			} catch (PDOException $p) {
				print $e;
			}
		}
		return $ret;
	}
}

?>
