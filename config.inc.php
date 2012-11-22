<?php
// slice status addon
$REX['ADDON']['rxid']['slice_status'] = '1022';
$REX['ADDON']['page']['slice_status'] = 'slice_status';
$REX['ADDON']['version']['slice_status'] = '1.2.0';
$REX['ADDON']['author']['slice_status'] = "WebDevOne";
$REX['ADDON']['supportpage']['slice_status'] = 'forum.redaxo.de';
$REX['ADDON']['perm']['slice_status'] = 'slice_status[]';

$REX['PERM'][] = 'slice_status[]';

// settings
$REX['ADDON']['slice_status']['ajax_mode'] = true;

// includes
require_once($REX['INCLUDE_PATH'] . '/addons/slice_status/functions/functions_slice_status.inc.php');

if ($REX['REDAXO']) {
	// add lang file
	$I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/slice_status/lang/');

	// update slice status in db if necessary
	if (rex_get('function') == 'updateslicestatus') {
		updateSliceStatusInDB(rex_get('article_id'), rex_get('clang'), rex_get('slice_id'), rex_get('new_status'));
	}

	// handle slice menu
	rex_register_extension('ART_SLICE_MENU', 'modifySliceEditMenu');

	// add css file
	rex_register_extension('PAGE_HEADER', 'addCSSFile');

	// add js code for coloring offline slices and also for ajax mode
	rex_register_extension('OUTPUT_FILTER', 'addJSCode');
}

// handle slice visibility in frontend
rex_register_extension('SLICE_SHOW', 'sliceShow');
?>
