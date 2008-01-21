<?php

require_once('lib/inc.database.php');
require_once('lib/func.http_get_var.php');

$PAGE['news']['name'] = "News";
$PAGE['news']['navilevel'] = 2;
$PAGE['news']['login_required'] = 0;
$PAGE['news']['phpclass'] = 'HtmlPage_news';
$PAGE['news']['parent'] = 'start';
$PAGE['news']['hidden'] = '0';



class HtmlPage_news extends HtmlPage {

	function news_teaser() {
		$content = "";
		$limit = http_get_var("limit");
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
		$news_query = my_query($SQL);
		if($news_query && mysql_num_rows($news_query)>0) {
			$content .= '
				<div class="news-list">
			';
			while($news_row = mysql_fetch_object($news_query)) {   
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
						<div class="news-teaser">'.nl2br($news_row->short).'</div>
					';
				}   
				$content .= '
					<div class="news-more"><a href="?p=news&amp;newsid='.$news_row->eintragid.'">mehr lesen</a></div>
					<div class="news-author">News geschrieben am '.$news_row->crdatestr.' von '.$news_row->author.'</div>
				</div>';
			} // while
			$content .= '
			</div>';
			mysql_free_result($news_query);
			if($limit != 'no') {   
				$content .=  '<p><a href="?p=news&amp;limit=no" class="news-navi-link">Alle News anzeigen</a></p>';
			}   
		} else { // if news_query
			$content .=  "<!-- MySQL-Fehler: ".mysql_errno() . " -->";
			$content .=  "Momentan sind keine News eingetragen.";
		}
		return $content;
	}
	
	function news_lesen($news_id)
	{
		$content = '';
		$SQL = "SELECT e.eintragid,e.title,e.catid,e.short,e.txt,DATE_FORMAT(crdate,'".WEB_DATEFORMAT."') crdatestr,e.author,c.pic AS catpic,c.name AS catname FROM news_eintrag e LEFT JOIN news_cat c ON e.catid=c.catid WHERE e.eintragid='$news_id'";
		$news_query = my_query($SQL);

		if($news_query) {
			$news_daten = mysql_fetch_array($news_query);
			$content .= '
				<div class="news-single"
				<div class="news-title">'.$news_daten["title"].'</div>
				<div class="news-cat">';
			if( is_file($news_daten['catpic']) ) {
				$content .=  '<img src="pics/cat_news/'.$news_daten['catpic'].'" alt="'.$news_daten['catname'].'">';
			}else{
				$content .=  "Kategorie: ".$news_daten['catname'];
			}
			$content .= '</div>
				<div class="news-text">'.nl2br($news_daten["txt"]).'</div>
				<div class="news-author">News geschrieben am '.$news_daten["crdatestr"].' von '.$news_daten["author"].'</div>
			</div>';
			$content .= '<a href="?p=news" class="news-navi-link">zur&uuml;ck zur News&uuml;bersicht.</a>';
			mysql_free_result($news_query);
		}else{ // if news_query
			$content .=  "<!-- MySQL-Fehler: ".mysql_errno() . " -->";
			$content .=  "Eine News mit dieser ID existiert nicht.";
		}
		return $content;
	}


	function getContent() {
		$newsid = http_get_var("newsid");
		if($newsid)
		{
			$ret = $this->news_lesen($newsid);
		}
		else
		{
			$ret = $this->news_teaser();
		}
		return $ret;
	}
}


?>
