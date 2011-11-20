<?php

/**
 * http://pear.php.net/package/Text_Wiki/
 * pear install Text_Wiki
 * http://openbook.galileocomputing.de/php_pear/11_0_text-003.htm
 */

require_once('Text/Wiki.php');

$demo = '
';

// Einlesen des Textes 
$text=file_get_contents('text1.txt'); 
 
// Neues Objekt instanziieren 
$wiki = new Text_Wiki(); 
 
// Text nach XHTML formatieren 
$xhtml = $wiki->transform($text, 'Xhtml'); 
 
// Code ausgeben 
echo $xhtml; 
?>
