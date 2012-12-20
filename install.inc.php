<?php

// add new status field in slice_article table 
$sql = new rex_sql();
//$sql->debugsql = true;
$sql->setQuery('ALTER TABLE `' . $REX['TABLE_PREFIX'] . 'article_slice` ADD `status` TINYINT( 1 ) UNSIGNED ZEROFILL NOT NULL DEFAULT "1"');

// check if a356_is_online field of deprecated slice_onoff addon exists
$sql->setQuery('SELECT a356_is_online FROM `' . $REX['TABLE_PREFIX'] . 'article_slice`');

if ($sql->getRows() > 0) {
	// import data from slice_onoff column to slice_status column
	$sql->setQuery('UPDATE `' . $REX['TABLE_PREFIX'] . 'article_slice` SET status = a356_is_online');

	// import message
	if (!$I18N->hasMsg('import_msg')) {
		// this is for redaxo < 4.4
		$I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/slice_status/lang/');
	}
	
	echo rex_info($I18N->msg('import_msg'));

	// remove old slice_onoff querys from cached articles
	rex_generateAll();
}

$REX['ADDON']['install']['slice_status'] = 1;
?>
