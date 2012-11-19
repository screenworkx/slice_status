<?php
$mypage = 'slice_status';

$REX['ADDON']['rxid'][$mypage] = '1022';
//$REX['ADDON']['name'][$mypage] = 'Slice Status';
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['version'][$mypage] = '1.0.5';
$REX['ADDON']['author'][$mypage] = "WebDevOne";
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';
$REX['ADDON']['perm'][$mypage] = 'slice_status[]';

$REX['PERM'][] = 'slice_status[]';

// --- DYN
$REX['ADDON']['slice_status']['slice_titlebar_background'] = '#dddddd';
$REX['ADDON']['slice_status']['slice_content_background'] = '#e7e5e5';
$REX['ADDON']['slice_status']['slice_content_opacity'] = '0.6';
// --- /DYN

// includes
require_once($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/functions/functions_slice_status.inc.php');

if ($REX['REDAXO']) {
	// add lang file
	$I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang/');

	// update slice status in db
	if (rex_get('function') == 'changeslicestatus') {
		updateSliceStatusInDB();
	}

	// handle slice menu
	rex_register_extension('ART_SLICE_MENU', 'modifySliceEditMenu');

	// insert js code for coloring offline slices
	rex_register_extension('OUTPUT_FILTER', 'addJSCode');
}

// handle slice visibility in frontend
rex_register_extension('SLICE_SHOW', 'sliceShow');
?>
