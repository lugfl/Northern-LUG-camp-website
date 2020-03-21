<?php

require_once('lib/class.RowSetMySQL.php');

$PAGE['lugs']['name'] = "LUGS";
$PAGE['lugs']['navilevel'] = 1;
$PAGE['lugs']['login_required'] = 1;
$PAGE['lugs']['phpclass'] = 'HtmlPage_lugs';
$PAGE['lugs']['parent'] = 'root';

class HtmlPage_lugs extends HtmlPage {

	var $name = "LUGS";
	var $navilevel = 1;
	var $login_required = 1;

	var $cmd;			// Kommando
	var $tn;			// Tabellenname
	var $id;			// ID der LUG
	var $details;			// 
	var $formdata;			// Eingabedaten aus dem Formular

	function __construct() {
		$this->tn = '';
		$this->id = 0;
		$this->details = Array();
		
		$this->cmd = strtolower(http_get_var('c'));
		$tmp = strtolower(http_get_var('tn'));
		if($tmp == 'event_lug') {
			$this->tn = $tmp;
			$_id = http_get_var('lugid');
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
				if($this->tn == 'event_lug' && $this->id != 0) {
					$this->loadDetails();
					$ret .= $this->detail_lug();
				}else{
					$ret .= 'Eingabefehler';
				}
			break;
			case 'edit':
				if($this->tn == 'event_lug') {
					$this->loadDetails();
					$ret .= $this->form_lug();
				}else{
					$ret .= 'Eingabefehler';
				}
			break;
			case 'save':
				$this->readFormInput();
				$this->loadDetails();
				if($this->tn == 'event_lug') {
					if($this->formdata['name'] != '') {
						$rc = $this->saveFormInput();
					}
				}
				$ret .= $this->cmd_default();
			break;
			default:
				$ret .= $this->cmd_default();
			break;
		} // switch
		return $ret;
	}

	function cmd_default() {
		$ret = '';
		
		$args = Array();
		$args[] = 'tn=event_lug';
		$args[] = 'lugid=0';
		$args[] = 'c=edit';
		
		$SQL = "SELECT event_lug.name,event_lug.abk,event_lug.url,event_lug.crdate,event_lug.lugid
		FROM event_lug ORDER BY event_lug.crdate DESC";
		$result = my_query($SQL);
		$rowset = new RowSetMySQL($result);

		$ret .= htmlpage_link('lugs','Neue LUG eintragen',$args);
		$ret .= $rowset->getHtmlTable();
		mysqli_free_result($result);
		$ret .= htmlpage_link('lugs','Neue LUG eintragen',$args);
		return $ret;
	}

	function readFormInput() {
		$name = http_get_var('name');
		$this->formdata['name'] = strip_tags($name,'<a><i><b><u>');
		
		$abk = http_get_var('abk');
		$this->formdata['abk'] = strip_tags($abk,'<a><i><b><u>');
		
		$url = http_get_var('url');
		$this->formdata['url'] = strip_tags($url,'<a><i><b><u><p><img>');
		
	}
	
	function saveFormInput() {
		global $_SESSION;
		if($this->tn == 'event_lug') {
			$pairs = Array();
			if(!isset($this->details['name']) || $this->details['name'] != $this->formdata['name']) {
				$tmp = "name='".my_escape_string($this->formdata['name'])."'";
				array_push($pairs,$tmp);
			}

			if(!isset($this->details['abk']) || $this->details['abk'] != $this->formdata['abk']) {
				$tmp = "abk='".my_escape_string($this->formdata['abk'])."'";
				array_push($pairs,$tmp);
			}

			if(!isset($this->details['url']) || $this->details['url'] != $this->formdata['url']) {
				$tmp = "url='".my_escape_string($this->formdata['url'])."'";
				array_push($pairs,$tmp);
			}

			if($this->id == 0) {

				array_push($pairs,'crdate=NOW()');
				$SQL = "INSERT INTO event_lug SET ";
				$SQL .= join(',',$pairs);
				my_query($SQL);
			}else{
				if(count($pairs)>0) {
					// Wenn sich nichts geaendert hat, brauch auch nichts upgedatet zu werden.
					$SQL = "UPDATE event_lug SET ";
					$SQL .= join(',',$pairs);
					$SQL .= ' WHERE lugid='.$this->id;
					
					my_query($SQL);
				}
			}
		}
	}

	function form_lug() {
		$ret = '';
		$name = '';
		if(isset($this->details['name'])) {
			$name = $this->details['name'];
		}
		$abk = '';
		if(isset($this->details['abk'])) {
			$abk = $this->details['abk'];
		}
		$url = '';
		if(isset($this->details['url'])) {
			$url = $this->details['url'];
		}
		$ret .= '
		<form action="./index.php" method="post">
			<input type="hidden" name="p" value="lugs"/>
			<input type="hidden" name="tn" value="event_lug"/>
			<input type="hidden" name="c" value="save"/>
			<input type="hidden" name="lugid" value="'.$this->id.'"/>

			<dl class="news_form">
				<dt>Name</dt>
				<dd><input type="text" name="name" value="'.$name.'" class="news_text""/></dd>

				<dt>Abk&uuml;rzung</dt>
				<dd><input type="text" name="abk" class="news_textarea" value="'.$abk.'"/></dd>

				<dt>URL</dt>
				<dd><input type="text" name="url" class="news_textarea" value="'.$url.'"></dd>
			</dl>
			<input type="submit" value="speichern" class="button"/>
			
		</form>
		';
		return $ret;
	}

	function detail_lug() {
		$ret = '<h2>Detail LUG</h2>';
		$name = $this->details['name'];
		$abk = $this->details['abk'];
		$url = $this->details['url'];
		$ret .= '
			<div class="news-single">
				<div class="news-title">'.$name.'</div>
				<div class="news-teaser">'.$abk.'</div>
				<div class="news-text">'.$url.'</div>
			</div>
		';
		//$ret .= '<pre>'.print_r($this->details,true).'</pre>';
		return $ret;
	}

	function loadDetails() {
		if($this->tn == 'event_lug' && is_numeric($this->id) && $this->id != 0) {
			$SQL = 'SELECT event_lug.* 
			FROM event_lug  
			WHERE event_lug.lugid=' . $this->id;
			$result = my_query($SQL);
			if($result ) {
				if(mysqli_num_rows($result)==1) {
					$this->details = mysqli_fetch_assoc($result);
				}
				mysqli_free_result($result);
			}
		}
		
	}
}


?>
