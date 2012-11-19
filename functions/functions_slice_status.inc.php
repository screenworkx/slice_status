<?php
function modifySliceEditMenu($params) {
	global $REX;
	global $I18N;
	
	extract($params);

	if (isset($status)) {
		// status param got set in class.rex_article_editor.inc.php
		// do nothing
	} else {
		$sqlStatement = 'SELECT status FROM '.$REX['TABLE_PREFIX'] . 'article_slice WHERE id = '. $slice_id;
		$sql = rex_sql::factory();
		$sql->setQuery($sqlStatement); 
		$status = $sql->getValue('status');
	}

	if ($status == 1) {
		//$aText = 'online';
		$aClass = 'slice-status-online';
		
		$aTitle = $I18N->msg('toggle_slice_offline');
		$imgSrc = 'on.png';
		$changeStatusParam = '0';
	} else {
		//$aText = 'offline';
		$aClass = 'slice-status-offline';
		$aTitle = $I18N->msg('toggle_slice_online');
		$imgSrc = 'off.png';
		$changeStatusParam = '1';
	}

	$function = 'changeslicestatus';
	$aHref = 'index.php?page=content&article_id=' . $article_id . '&mode=edit&slice_id=' . $slice_id . '&clang=' . $clang . '&ctype=' . $ctype . '&function=' . $function . '&status=' . $changeStatusParam . '#slice' . $slice_id;

	//$statusSwitch = '<a class="' . $aClass . '"title="" href="' . $aHref . '">' . $aText . '</a>';
	$statusSwitch = '<a class="' . $aClass . '" style="background: none; margin-left: -4px; margin-top: -1px;" href="' . $aHref . '" title="' . $aTitle . '"><img src="/files/addons/slice_status/' . $imgSrc . '"></a>';

	$subject[] = $statusSwitch;

    return $subject;
}

function sliceShow($params) {
	global $REX;
	
	extract($params);
	
	$sqlStatement = 'SELECT status, ctype FROM '.$REX['TABLE_PREFIX'] . 'article_slice WHERE id = '. $slice_id;
	$sql = rex_sql::factory();
	//$sql->debugsql = true;
	$sql->setQuery($sqlStatement);
	
	if (($sql->getValue('status') == 1) || $REX['REDAXO']) {
		return $subject;
	} else {
		return '<?php if (false) { ?>' . $subject . '<?php } ?>';
	}
}

function updateSliceStatusInDB() {
	global $REX;

	// update db	
	$status = rex_get('status');
	$slice_id = rex_get('slice_id');
	$sql = rex_sql::factory();
	//$sql->debugsql = true;
	$sql->setQuery('UPDATE ' . $REX['TABLE_PREFIX'] . 'article_slice SET status = ' . $status . ' WHERE id=' . $slice_id);
	
	// delete cached article
	$article_id = rex_get('article_id');
	$clang = rex_get('clang');
	
	rex_deleteCacheArticleContent($article_id, $clang);
}

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
?>
