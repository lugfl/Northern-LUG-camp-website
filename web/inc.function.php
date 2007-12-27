<?php
function getVar($name, $type)
{
  global $HTTP_GET_VARS;
  global $HTTP_POST_VARS;

  if( isset($HTTP_GET_VARS[$name]) )
  {
    $ret = $HTTP_GET_VARS[$name];
  }
  else if( isset($HTTP_POST_VARS[$name]) )
  {
  	$ret = $HTTP_POST_VARS[$name];
  }
  else
  {
  	$ret = "";
  }

  switch($type)
  {
    case "number":
      $ret = ereg_replace(",", ".", $ret);
      if (!is_numeric($ret))
      {
        $ret = "";
      }
      break;
    case "text":
      $ret = $ret;
      break;
    default:
      $ret = $ret;
  }
  return $ret;
}
?>
