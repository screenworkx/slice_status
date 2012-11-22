<?php
// DELETE/UPDATE DATABASE
$sql = new rex_sql();
$sql->setQuery('ALTER TABLE `' . $REX['TABLE_PREFIX'] . 'article_slice` DROP `status`');

$REX['ADDON']['install']['slice_status'] = 0;
?>
