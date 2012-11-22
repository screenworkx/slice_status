<?php
$mypage = 'slice_status';

$REX['ADDON']['rxid'][$mypage] = '1022';
$REX['ADDON']['name'][$mypage] = 'Slice Status';
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['version'][$mypage] = '1.0.5';
$REX['ADDON']['author'][$mypage] = "WebDevOne";
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';
$REX['ADDON']['perm'][$mypage] = 'slice_status[]';

$REX['PERM'][] = 'slice_status[]';

// --- DYN
$REX['ADDON']['slice_status']['offline_slice_titlebar_background'] = '#dddddd';
$REX['ADDON']['slice_status']['offline_slice_content_background'] = '#e7e5e5';
$REX['ADDON']['slice_status']['offline_slice_content_opacity'] = '0.6';
$REX['ADDON']['slice_status']['ajax_mode'] = true;
// --- /DYN

// includes
require_once($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/functions/functions_slice_status.inc.php');

if ($REX['REDAXO']) {
	// add lang file
	$I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang/');

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
