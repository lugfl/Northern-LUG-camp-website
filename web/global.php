<?php

setlocale (LC_ALL, 'de_DE');

define('WEB_INSIDE',1);
define('WEB_ROOT',dirname($_SERVER["SCRIPT_FILENAME"]));
define('SMARTY_DIR',WEB_ROOT.'/lib/smarty/libs/');
define('XINHA_WEBROOT', '/lib/xinha/');

define('TEMPLATE_DIR',WEB_ROOT . '/templates/');
if( is_dir(WEB_ROOT . '/templates/' . $_SERVER['SERVER_NAME']) ) {
  define('TEMPLATE_STYLE',$_SERVER['SERVER_NAME']);
} else {
  define('TEMPLATE_STYLE','.');
}

//require_once('lib/inc.database.php');
require_once('config.php');

require_once('lib/inc.sponsoren.php');


?>
