<?php
// CREATE/UPDATE DATABASE
$sql = new rex_sql();
$sql->setQuery('ALTER TABLE `' . $REX['TABLE_PREFIX'] . 'article_slice` ADD `status` TINYINT( 1 ) UNSIGNED ZEROFILL NOT NULL DEFAULT "1"');
	
$REX['ADDON']['install']['slice_status'] = 1;
?>
