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

require_once($REX['INCLUDE_PATH'] . '/addons/slice_status/functions/functions_slice_status.inc.php');

if ($REX['REDAXO']) {
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

	// insert js file for coloring offline slices
	$insert = '<script src="../files/addons/slice_status/functions.js" type="text/javascript"></script>' . "\r\n";
	rex_register_extension('PAGE_HEADER', create_function('$params', 'return $params[\'subject\'] . \''. $insert . '\';'));
}

rex_register_extension('SLICE_SHOW', 'sliceShow');
?>
