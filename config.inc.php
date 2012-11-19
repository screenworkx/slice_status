<?php
$mypage = 'slice_status';

$REX['ADDON']['rxid'][$mypage] = '1022';
//$REX['ADDON']['name'][$mypage] = 'Slice Status';
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['perm'][$mypage] = 'slice_status[]';
$REX['ADDON']['version'][$mypage] = '1.0.5';
$REX['ADDON']['author'][$mypage] = "WebDevOne";
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';

$REX['PERM'][] = 'slice_status[]';

// --- DYN
$REX['ADDON']['slice_status']['slice_titlebar_background'] = '#dddddd';
$REX['ADDON']['slice_status']['slice_content_background'] = '#e7e5e5';
$REX['ADDON']['slice_status']['slice_content_opacity'] = '0.6';
// --- /DYN

require_once($REX['INCLUDE_PATH'] . '/addons/slice_status/functions/functions_slice_status.inc.php');

if ($REX['REDAXO']) {
	// lang
	$I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang/');

	// handle slice menu
	rex_register_extension('ART_SLICE_MENU', 'modifySliceEditMenu');

	// update slice status in db
	if (rex_get('function') == 'changeslicestatus') {
		$status = rex_get('status');
		$slice_id = rex_get('slice_id');
		$sql = rex_sql::factory();
		//$sql->debugsql = true;
    	$sql->setQuery('UPDATE ' . $REX['TABLE_PREFIX'] . 'article_slice SET status = ' . $status . ' WHERE id=' . $slice_id);
		
		$article_id = rex_get('article_id');
		$clang = rex_get('clang');
		rex_deleteCacheArticleContent($article_id, $clang);
		//rex_generateAll(); 
	}

	// insert js code for coloring offline slices
	rex_register_extension('OUTPUT_FILTER', 'addJSCode');

	function addJSCode($params) {
		global $REX;
		
		$script = "
		<!-- BEGIN slice_status -->
		<script type=\"text/javascript\">
			var sliceTitleBarBackground = '" . $REX['ADDON']['slice_status']['slice_titlebar_background'] . "';
			var sliceContentBackground = '" . $REX['ADDON']['slice_status']['slice_content_background'] . "';
			var sliceContentOpacity = " . $REX['ADDON']['slice_status']['slice_content_opacity'] . ";

			jQuery(document).ready(function($) {
				var jSliceTitleBar = $('.slice-status-offline').parents('.rex-content-editmode-module-name');
				var jSliceContent = jSliceTitleBar.next('.rex-content-editmode-slice-output');

				// titlebar
				jSliceTitleBar.css('background', sliceTitleBarBackground);

				// slice content
				jSliceContent.wrap('<div style=\"background: ' + sliceContentBackground  + ';\" />');
				jSliceContent.css('background', sliceContentBackground);
				jSliceContent.css('opacity', sliceContentOpacity);
			});
		</script>
		<!-- END slice_status -->
		";

		return str_replace('</body>', $script . "\r\n" . '</body>', $params['subject']);
	}
}

rex_register_extension('SLICE_SHOW', 'sliceShow');
?>
