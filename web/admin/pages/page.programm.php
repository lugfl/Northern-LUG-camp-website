<?php

require_once('lib/func.sys_get_temp_dir.php');
require_once('lib/func.parse_ical.php');

$PAGE['programm']['name'] = "Programm";
$PAGE['programm']['navilevel'] = 1;
$PAGE['programm']['login_required'] = 1;
$PAGE['programm']['phpclass'] = 'HtmlPage_programm';
$PAGE['programm']['parent'] = 'root';

class HtmlPage_programm extends HtmlPage {

	var $name = "Programm";
	var $navilevel = 1;
	var $login_required = 1;

	var $action = "";
	var $ceventid = 0;

	function HtmlPage_programm() {
		global $CURRENT_EVENT_ID;
		if(isset($CURRENT_EVENT_ID) && is_numeric($CURRENT_EVENT_ID))
			$this->ceventid = $CURRENT_EVENT_ID;		

		$this->action = http_get_var("a");
		if($this->action == "import") 
			$this->import();

	}
	
	function getContent() {
   	$ret = '
		<h1>Programmverwaltung</h1>
		<form action="?" method="post" enctype="multipart/form-data">
			<input type="hidden" name="p" value="programm"/>
			<input type="hidden" name="a" value="import"/>
			<input type="file" name="programm_file"/>
			<br/>
			<select size="1" name="input_encoding">
				<option value="ISO-8859-1">ISO-8859-1</option>
				<option value="UTF-8">UTF-8</option>
			</select>
			<br/>
			<input type="submit" value="Absenden"/>
		</form>
		';

		$SQL = "SELECT titel,start,ende,kategorie,beschreibung ";
		$SQL .= "FROM event_programm WHERE eventid=".$this->ceventid." ORDER BY start";
		$res = my_query($SQL);
		if(mysql_num_rows($res)>0) {
			$ret .= '
				<table>
			';
			while($row = mysql_fetch_assoc($res)) {
				$ret .= '
					<tr>
						<td>'.$row['start'].'</td>
						<td>'.$row['ende'].'</td>
						<td>'.$row['titel'].'</td>
						<td>'.$row['kategorie'].'</td>
					</tr>
					<tr>
						<td colspan="4">'.$row['beschreibung'].'</td>
					</tr>
				';
			}
			$ret .= '
				</table>
			';
		}
		mysql_free_result($res);
		return $ret;
	}

	function import() {
		my_query("SET time_zone='Europe/Berlin'");
		if($this->action == "import") {
			if(isset($_FILES['programm_file'])) {
				if($_FILES['programm_file']['type'] != 'text/calendar') {
					$ret .= '<div class="error">Nur Dateien vom typ text/calendar sind zugelassen.</div>';
				}else{
					// Dateityp ist hier akzeptiert
					$tmpdir = sys_get_temp_dir();
					if($tmpdir) {
						$uploadfile = $tmpdir . "/" . basename($_FILES['programm_file']['name']);
						if(move_uploaded_file($_FILES['programm_file']['tmp_name'], $uploadfile) ) {
							// File erfolgreich verschoben
							//$ret .= "Upload nach $uploadfile verschoben.";
							$input_encoding = http_get_var('input_encoding');
							if($input_encoding == "UTF-8") {
								$fn = basename($uploadfile,".ics");
								//$ret .= "Filename: $fn";
								$newfn = $tmpdir .= "/" . $fn.".ISO-8859-1.ics";
								$this->convertFileUTF8ISO8859($uploadfile,$newfn);
								$uploadfile = $newfn;
							}
							$importfile = dirname($uploadfile) . "/" . basename($uploadfile,".ics");
							$data = parse_ical($importfile);
							$importinfo = array_shift($data);
							//print_r($data);
							$SQL = "DELETE FROM event_programm WHERE eventid=".$this->ceventid;
							my_query($SQL);
							foreach($data as $id=>$d) {
								$p = Array();
								array_push($p,"eventid=".$this->ceventid);
								array_push($p,"titel='".my_escape_string($d['summary'])."'");
								array_push($p,"beschreibung='".my_escape_string($d['description'])."'");
								array_push($p,"start=FROM_UNIXTIME(".$d['start_unix'].")");
								array_push($p,"ende=FROM_UNIXTIME(".$d['end_unix'].")");
								array_push($p,"kategorie='".my_escape_string($d['categories'])."'");
								if(count($p)>0) {
									$SQL = "INSERT INTO event_programm SET ".join(", ",$p);
									my_query($SQL);
								}
							}
						}else{
							// Error Upload-File-Move
						}
					}else{
						$ret .= '<div class="error">Temp-Directory missing.</div>';
					}					
				}
			}else{
				$ret .= '<div class="error">Es wurde keine Datei angegeben.</div>';
			}
		}
	}

	function convertFileUTF8ISO8859($filename,$to) {
		$ret = false;
		if($fp = fopen($filename,"r")) {
			$in = fread($fp,filesize($filename));
			fclose($fp);

			$str = iconv('utf-8','ISO-8859-1',$in);
			if($fp = fopen($to,"w")) {
				fputs($fp,$str);
				fclose($fp);
				$ret = true;
			}
		}
		return $ret;
	}

	function importIcal($file,$encoding) {
		
	}
}


?>
