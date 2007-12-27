<style type="text/css">
<!--
.news-single {
	padding-top:10px;
	padding-bottom:10px;
}
.news-title {
	font-size:1.2em;
	font-weight:bold;
}
.news-teaser {
	margin-left:20px;
}
.news-author {
	font-size:0.7em;
}
.news-more {
	font-size:0.7em;
}

-->
</style>
<?php

define ('WEB_DATEFORMAT','%d.%m.%Y, %H:%m');
define ('WEB_NEWSTEASER_ANZAHL',3);

$db_news = @mysql_connect('localhost','01_lc2008_dev','oinkoink');
if(mysql_errno() != 0) {
	trigger_error("Database Problem",E_USER_ERROR);
}

mysql_select_db('01_lc2008_dev',$db_news);
if(mysql_errno() != 0) {
	trigger_error("Database Problem",E_USER_ERROR);
}

$news_id = http_get_var('newsid');
$ret = '';
if($news_id)
{
	$ret = news_lesen($news_id);
}
else
{
	$ret = news_teaser();
}

print $ret;

function news_teaser() {
	$content = "";
	$limit = http_get_var('limit');
	$SQL = "SELECT e.eintragid,e.title,e.catid,e.short,e.txt,DATE_FORMAT(e.crdate,'".WEB_DATEFORMAT."') crdatestr,e.author,c.name AS catname,c.pic AS catpic ";
	$SQL .= " FROM news_eintrag e LEFT JOIN news_cat c ON e.catid=c.catid ";
	$SQL .= " ORDER BY e.eintragid DESC";
	
	if($limit == 'no')
	{
		$SQL .= " LIMIT 100 ";
	}
	else
	{
		$SQL .= " LIMIT ". WEB_NEWSTEASER_ANZAHL;
	}
	$news_query = @mysql_query($SQL);
	if($news_query) {
		$content .= '
			<div class="news-list">
		';
		while($news_row = mysql_fetch_object($news_query))
		{
			$content .= '
				<div class="news-single">
					<div class="news-title">'.$news_row->title.'</div>
			';
			if($news_row->catname != '') {
				$content .= '
					<div class="news-cat">
				';
				if($news_row->catpic!='') {
					$catpic_file = "pics/cat_news/".$news_row->catpic;
					if( is_file($catpic_file) ) {
						$content .=  '<img src="pics/cat_news/'.$news_row->catpic.'" alt="'.$news_row->catname.'" class="news-catpic">';
					}else{
						$content .=  "Kategorie: ".$news_row->catname;
					}
				}
				$content .= '
					</div>
				';
			} // if catname
			if($news_row->short != '') {
				$content .= '
					<div cat="news-teaser">'.nl2br($news_row->short).'</div>
				';
			}
			$content .= '
					<div class="news-author">News geschrieben am '.$news_row->crdatestr.' von '.$news_row->author.'</div>
					<div class="news-more"><a href="?p=news&newsid='.$news_row->eintragid.'">mehr lesen</a></div>
				</div>';
		} // while
		$content .= '
			</div>';
		mysql_free_result($news_query);
		if($limit != 'no')
		{
			$content .=  '<p><a href="?p=news&limit=no">Alle News anzeigen</a></p>';
		}
		else
		{ // if news_query
			$content .=  "<!-- MySQL-Fehler: ".mysql_errno() . " -->";
			$content .=  "Momentan sind keine News eingetragen.";
		}
	}
	return $content;
}
function news_lesen($news_id)
{
	$content = '';
	$SQL = "SELECT e.eintragid,e.title,e.catid,e.short,e.txt,DATE_FORMAT(crdate,'".WEB_DATEFORMAT."') crdatestr,e.author,c.pic AS catpic,c.name AS catname FROM news_eintrag e LEFT JOIN news_cat c ON e.catid=c.catid WHERE e.eintragid='$news_id'";
	$news_query = mysql_query($SQL);
	if($news_query)	
	{
		$news_daten = mysql_fetch_array($news_query);
		$content .= '
		<div class="news-single"
			<div class="news-title">'.$news_daten["title"].'</div>
			<div class="news-cat">';
		if( is_file($news_daten['catpic']) ) {
			$content .=  '<img src="pics/cat_news/'.$news_daten['catpic'].'" alt="'.$news_daten['catname'].'">';
		}
		else
		{
			$content .=  "Kategorie: ".$news_daten['catname'];
		}
		$content .= '</div>
			<div class="news-text">'.nl2br($news_daten["txt"]).'</div>
			<div class="news-author">News geschrieben am '.$news_daten["crdatestr"].' von '.$news_daten["author"].'</div>
		</div>';
		$content .= '<a href="?p=news">zur&uuml;ck zur News&uuml;bersicht.</a>';
		mysql_free_result($news_query);
	}else{ // if news_query
		$content .=  "<!-- MySQL-Fehler: ".mysql_errno() . " -->";
		$content .=  "Eine News mit dieser ID existiert nicht.";
	}
	return $content;
}

function http_get_var($name,$default='') {
	$ret = "";
	
	if(isset($_POST[$name]))
		$ret = $_POST[$name];
	else if(isset($_GET[$name]))
		$ret = $_GET[$name];
	else
		$ret = $default;
	return $ret;
}

?>
