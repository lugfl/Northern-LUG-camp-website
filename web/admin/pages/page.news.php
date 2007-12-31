<?php

require_once('lib/class.RowSetMySQL.php');

$PAGE['news']['name'] = "News";
$PAGE['news']['navilevel'] = 1;
$PAGE['news']['login_required'] = 1;
$PAGE['news']['phpclass'] = 'HtmlPage_news';
$PAGE['news']['parent'] = 'root';

class HtmlPage_news extends HtmlPage {

	var $name = "News";
	var $navilevel = 1;
	var $login_required = 1;

	var $cmd;			// Kommando
	var $tn;			// Tabellenname
	var $id;			// ID der News oder Newscat
	var $details;			// Detaildaten zur News oder Newscat
	var $formdata;			// Eingabedaten aus dem Formular

	function HtmlPage_news() {
		$this->tn = '';
		$this->id = 0;
		$this->details = Array();
		
		$this->cmd = strtolower(http_get_var('c'));
		$tmp = strtolower(http_get_var('tn'));
		if($tmp == 'news_eintrag') {
			$this->tn = $tmp;
			$_id = http_get_var('eintragid');
			if(is_numeric($_id) ) {
				$this->id = $_id;
			}
		}else if ($tmp == 'news_cat') {
			$this->tn = $tmp;
			$_id = http_get_var('catid');
			if(is_numeric($_id) ) {
				$this->id = $_id;
			}
		}
		
	}
	
	function getContent() {
		global $_SESSION;
    		$ret = '';
		//$ret .= print_r($_SESSION,true);	
		switch($this->cmd) {
			case 'detailview':
				if($this->tn == 'news_eintrag' && $this->id != 0) {
					$this->loadDetails();
					$ret .= $this->detail_news_eintrag();
				}else if($this->tn == 'news_cat' && $this->id != 0) {
					$this->loadDetails();
					$ret .= $this->detail_news_cat();
				}else{
					$ret .= 'Eingabefehler';
				}
			break;
			case 'edit':
				if($this->tn == 'news_eintrag') {
					$this->loadDetails();
					$ret .= $this->form_news_eintrag();
				}else if($this->tn == 'news_cat') {
					$this->loadDetails();
					$ret .= $this->form_news_cat();
				}else{
					$ret .= 'Eingabefehler';
				}
			break;
			case 'save':
				$this->readFormInput();
				$this->loadDetails();
				if($this->tn == 'news_eintrag') {
					if($this->formdata['title'] != '' && $this->formdata['txt'] != '') {
						$rc = $this->saveFormInput();
					}
				}
				$ret .= $this->cmd_default();
			break;
			case 'delete':
				if(is_numeric($this->id)) {
					$SQL = 'DELETE FROM news_eintrag WHERE eintragid='.$this->id;
					my_query($SQL);

				}
				$ret .= $this->cmd_default();
			break;
			default:
				$ret .= $this->cmd_default();
			break;
		} // switch
		return $ret;
	}

	function readFormInput() {
		$title = http_get_var('title');
		$this->formdata['title'] = strip_tags($title,'<a><i><b><u>');
		
		$short = http_get_var('short');
		$this->formdata['short'] = strip_tags($short,'<a><i><b><u>');
		
		$txt = http_get_var('txt');
		$this->formdata['txt'] = strip_tags($txt,'<a><i><b><u><p><img>');
		
	}
	
	function saveFormInput() {
		global $_SESSION;
		if($this->tn == 'news_eintrag') {
			$pairs = Array();
			if(!isset($this->details['title']) || $this->details['title'] != $this->formdata['title']) {
				$tmp = "title='".my_escape_string($this->formdata['title'])."'";
				array_push($pairs,$tmp);
			}

			if(!isset($this->details['short']) || $this->details['short'] != $this->formdata['short']) {
				$tmp = "short='".my_escape_string($this->formdata['short'])."'";
				array_push($pairs,$tmp);
			}

			if(!isset($this->details['txt']) || $this->details['txt'] != $this->formdata['txt']) {
				$tmp = "txt='".my_escape_string($this->formdata['txt'])."'";
				array_push($pairs,$tmp);
			}

			if($this->id == 0) {

				if(isset($_SESSION['USER'])) {
					$vn = $_SESSION['USER']->getVorname();
					$nn = $_SESSION['USER']->getNachname();
					$un = $_SESSION['USER']->getUsername();

					$autor = '';
					if($vn!='') {
						$autor .= $vn;
					}
					if($nn != '') {
						if($autor != '') {
							$autor .= ' ';
						}
						$autor .= $nn;
					}
					if($autor=='') {
						$autor .= $un;
					}
					array_push($pairs,"author='".my_escape_string($autor)."'");
				}
				array_push($pairs,'crdate=NOW()');
				$SQL = "INSERT INTO news_eintrag SET ";
				$SQL .= join(',',$pairs);
				my_query($SQL);
			}else{
				if(count($pairs)>0) {
					// Wenn sich nichts geaendert hat, brauch auch nichts upgedatet zu werden.
					$SQL = "UPDATE news_eintrag SET ";
					$SQL .= join(',',$pairs);
					$SQL .= ' WHERE eintragid='.$this->id;
					
					my_query($SQL);
				}
			}
		}else if($this->tn == 'news_cat') {
		}
	}
	
	function cmd_default() {
		$ret = '';
		
		$args = Array();
		$args[] = 'tn=news_eintrag';
		$args[] = 'eintragid=0';
		$args[] = 'c=edit';
		
		$SQL = "SELECT news_eintrag.title,news_cat.name,news_eintrag.short,
			news_eintrag.crdate,news_eintrag.author,news_eintrag.eintragid 
		FROM news_eintrag LEFT JOIN news_cat  ON news_eintrag.catid=news_cat.catid ORDER BY news_eintrag.crdate DESC";
		$result = my_query($SQL);
		$rowset = new RowSetMySQL($result);

		$ret .= htmlpage_link('news','Neue News eintragen',$args);
		$ret .= $rowset->getHtmlTable();
		mysql_free_result($result);
		$ret .= htmlpage_link('news','Neue News eintragen',$args);
		return $ret;
	}

	function form_news_eintrag() {
		$ret = '';
		$title = '';
		if(isset($this->details['title'])) {
			$title = $this->details['title'];
		}
		$short = '';
		if(isset($this->details['short'])) {
			$short = $this->details['short'];
		}
		$txt = '';
		if(isset($this->details['txt'])) {
			$txt = $this->details['txt'];
		}
		$ret .= '
		<form action="./index.php" method="post">
			<input type="hidden" name="p" value="news"/>
			<input type="hidden" name="tn" value="news_eintrag"/>
			<input type="hidden" name="c" value="save"/>
			<input type="hidden" name="eintragid" value="'.$this->id.'"/>

			<dl class="news_form">
				<dt>Titel</dt>
				<dd><input type="text" name="title" value="'.$title.'" class="news_text""/></dd>

				<dt>Schlagzeile (Teaser)</dt>
				<dd><textarea name="short" class="news_textarea">'.$short.'</textarea></dd>

				<dt>Text</dt>
				<dd><textarea name="txt" class="news_textarea">'.$txt.'</textarea></dd>
			</dl>
			<input type="submit" value="speichern" class="button"/>
			
		</form>
		';
		return $ret;
	}

	function form_news_cat() {
		$ret = '';
		return $ret;
	}

	function detail_news_eintrag() {
		$ret = '<h2>Detail News Eintrag</h2>';
		$title = $this->details['title'];
		$teaser = $this->details['short'];
		$text = $this->details['txt'];
		$ret .= '
			<div class="news-single">
				<div class="news-title">'.$title.'</div>
				<div class="news-teaser">'.$teaser.'</div>
				<div class="news-text">'.$text.'</div>
			</div>
		';
		//$ret .= '<pre>'.print_r($this->details,true).'</pre>';
		return $ret;
	}

	function detail_news_cat() {
		$ret = 'Detail News Cat';
		$ret .= print_r($this->details,true);
		return $ret;
	}

	function loadDetails() {
		if($this->tn == 'news_eintrag' && is_numeric($this->id) && $this->id != 0) {
			$SQL = 'SELECT news_eintrag.*,news_cat.* 
			FROM news_eintrag LEFT JOIN news_cat ON news_eintrag.catid=news_cat.catid 
			WHERE news_eintrag.eintragid=' . $this->id;
			$result = my_query($SQL);
			if($result ) {
				if(mysql_num_rows($result)==1) {
					$this->details = mysql_fetch_assoc($result);
				}
				mysql_free_result($result);
			}
		}
		
	}
}


?>
