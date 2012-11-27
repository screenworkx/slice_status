<?php

// add new status field in slice_article table 
$sql = new rex_sql();
//$sql->debugsql = true;
$sql->setQuery('ALTER TABLE `' . $REX['TABLE_PREFIX'] . 'article_slice` ADD `status` TINYINT( 1 ) UNSIGNED ZEROFILL NOT NULL DEFAULT "1"');

// check if a356_is_online field of deprecated slice_onoff addon exists
$sql->setQuery('SELECT a356_is_online FROM `' . $REX['TABLE_PREFIX'] . 'article_slice`');

if ($sql->getRows() > 0) {
	// import data from slice_onoff row to slice_status row
	$sql->setQuery('UPDATE `' . $REX['TABLE_PREFIX'] . 'article_slice` SET status = a356_is_online');

	echo rex_info($I18N->msg('import_msg'));
}

$REX['ADDON']['install']['slice_status'] = 1;
?>
