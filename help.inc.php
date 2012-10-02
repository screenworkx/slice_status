<?php
/**
 * Redaxo Addon slice_onoff - Online-Status für Slices
 *
 * @author Fabian Michael me[AT]fm86[PUNKT]de
 * @author Sven Kesting <sk[AT]decaf[PUNKT]de>
 *
 * @link https://github.com/jdlx/slice_onoff
 * @link http://www.redaxo.org/de/download/addons/?addon_id=356
 *
 * @package redaxo 4.2.x/4.3.x/4.4.x
 * @version 0.3.1
 *
 */


$mode = rex_request('mode', 'string', '');

switch($mode)
{
  case 'changelog': $file = '_changelog.txt'; break;
  case 'todo': $file      = '_todo.txt'; break;
  default: $file          = '_readme.txt';
}
?>
<a href="?page=addon&amp;spage=help&amp;addonname=slice_onoff">ReadMe</a> |
<a href="?page=addon&amp;spage=help&amp;addonname=slice_onoff&amp;mode=changelog">ChangeLog</a> |
<a href="?page=addon&amp;spage=help&amp;addonname=slice_onoff&amp;mode=todo">ToDo</a>
<br /><br />
<?php

echo str_replace( '+', '&nbsp;&nbsp;+', file_get_contents(dirname( __FILE__) . '/' . $file));

?>
<br /><br />
<hr />
<br />
<p>Online-Status f&uuml;r Slices Addon by Fabian Michael | Kontakt: &lt;me[AT]fm86[PUNKT]de&gt;</p>
