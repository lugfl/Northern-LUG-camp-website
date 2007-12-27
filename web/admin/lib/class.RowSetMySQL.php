<?php

class RowSetMySQL {
	var $result;
	var $colmeta;
	var $numcols;

	function RowSetMySQL($res) {
		$this->colmeta = Array();
		$this->result = $res;
		$this->numcols = mysql_num_fields($this->result);
		$i = 0;
		while($i < $this->numcols) {
			$meta = mysql_fetch_field($this->result,$i);
			$debug_str = "<pre>
			blob:         $meta->blob
			max_length:   $meta->max_length
			multiple_key: $meta->multiple_key
			name:         $meta->name
			not_null:     $meta->not_null
			numeric:      $meta->numeric
			primary_key:  $meta->primary_key
			table:        $meta->table
			type:         $meta->type
			default:      $meta->def
			unique_key:   $meta->unique_key
			unsigned:     $meta->unsigned
			zerofill:     $meta->zerofill
			</pre>";
			if(defined('DEBUG')) {
				//print $debug_str;
			}
			$this->colmeta[$i] = $meta;
			$i++;
		}	
	}

	function getHtmlTable() {
		global $DB_SCHEMA;
		
		$rows = Array();
		$tmprow = Array();

		// Header bauen
		for($i=0; $i < $this->numcols;$i++) {
			array_push($tmprow,$this->getColName($this->colmeta[$i]->table,$this->colmeta[$i]->name));
		}
		$str2 = '<tr><th>'.join('</th><th>',$tmprow).'</th></tr>';
		array_push($rows,$str2);
		
		// Inhalt auflisten
		while($row = mysql_fetch_array($this->result)) {
			$tmprow = Array();
			for($i=0; $i < $this->numcols;$i++) {
				$inhalt = '';
				$ct = $this->colmeta[$i]->type;
				if($this->colmeta[$i]->primary_key) {
					$tn = $this->colmeta[$i]->table;
					$cn = $this->colmeta[$i]->name;
					if(
						isset($DB_SCHEMA[$tn]['cols'][$cn]['cmd']) and
						is_array($DB_SCHEMA[$tn]['cols'][$cn]['cmd'])
					) {
						$cmd_links = Array();
						foreach( $DB_SCHEMA[$tn]['cols'][$cn]['cmd'] as $cmdname=>$cmd ) {
							// einzelne Befehle zusammenstellen
							$args = Array();
							array_push($args,'tn='.$tn);
							array_push($args,$cn.'='.$row[$i]);
							array_push($args,'c='.$cmdname);
							if(!isset($cmd['name'])) {
								$cmd['name'] = '?';
							}
							if(isset($cmd['p'])) {
								// ohne eine Zielpage wuerde das keinen Sinn machen
								array_push($cmd_links,htmlpage_link($cmd['p'],$cmd['name'],$args));
							}

						} // foreach cmd
						$inhalt .= join(' ',$cmd_links);
					}else{ // if cmd
						
					}
				}else if($ct == 'datetime') {
					// Datumsspalte
					$inhalt = $row[$i];
					
				}else{
					// Inhalt anzeigen
					$inhalt = $row[$i];
				}
				array_push($tmprow,$inhalt);
			}
			$str2 = '<tr><td>'.join('</td><td>',$tmprow).'</td></tr>';
			array_push($rows,$str2);
			
		}
		return '<table>'.join('',$rows).'</table>';
		
	}

	function getColName($table,$col) {
		global $DB_SCHEMA;
		$ret = "<!-- $table.$col -->";
		
		if(isset($DB_SCHEMA[$table]['cols'][$col]['name'])) {
			$ret = $DB_SCHEMA[$table]['cols'][$col]['name'];
		}
		return $ret;
	}

}

?>
