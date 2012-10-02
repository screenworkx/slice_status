<?php
/**
 * slice_onoff Online-Status für Slices
 * @author me[AT]fm86[PUNKT]de Fabian Michael
 * @package redaxo4
 */

// DELETE/UPDATE DATABASE
$sql = new rex_sql();
$sql->setQuery('ALTER TABLE `'.$REX['TABLE_PREFIX'].'article_slice` DROP `a356_is_online`');

// DELETE/UPDATE MODULES
// -none-

// DELETE/UPDATE PAGES
// -none-

// REGENERATE SITE
// -none- vielleicht den gesamten Cache leeren?


$REX['ADDON']['install']['slice_onoff'] = 0;

?>