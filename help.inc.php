<?php
/**
 * slice_onoff Online-Status für Slices
 * @author me[AT]fm86[PUNKT]de Fabian Michael
 * @package redaxo4
 */


$mode = rex_request('mode', 'string', '');

switch ( $mode) {
   case 'changelog': $file = '_changelog.txt'; break;
   case 'todo': $file = '_todo.txt'; break;
   default: $file = '_readme.txt';
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