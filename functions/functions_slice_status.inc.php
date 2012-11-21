<?php
function modifySliceEditMenu($params) {
	global $REX;
	global $I18N;
	
	extract($params);

	// get current status of slice
	$sqlStatement = 'SELECT status FROM '.$REX['TABLE_PREFIX'] . 'article_slice WHERE id = ' . $slice_id;
	$sql = rex_sql::factory();
	$sql->setQuery($sqlStatement); 
	$curStatus = $sql->getValue('status');

	// retrieve stuff for new status
	if ($curStatus == 1) {
		$aClass = 'slice-status slice-' . $slice_id . ' online';
		$aTitle = $I18N->msg('toggle_slice_offline');
		$imgSrc = '/files/addons/slice_status/on.png';
		$newStatus = '0';
	} else {
		$aClass = 'slice-status slice-' . $slice_id . ' offline';
		$aTitle = $I18N->msg('toggle_slice_online');
		$imgSrc = '/files/addons/slice_status/off.png';
		$newStatus = '1';
	}
	
	// construct href
	if ($REX['ADDON']['slice_status']['ajax_mode']) {
		$aHref = 'javascript:updateSliceStatus(' . $article_id . ',' . $clang . ',' . $slice_id . ',' . $curStatus . ');';
	} else {
		$aHref = 'index.php?page=content&article_id=' . $article_id . '&mode=edit&slice_id=' . $slice_id . '&clang=' . $clang . '&ctype=' . $ctype . '&function=updateslicestatus' . '&new_status=' . $newStatus . '#slice' . $slice_id;	
	}
	
	// inject link in slice menu
	$subject[] = '<a class="' . $aClass . '" style="background: none; margin-left: -4px; margin-top: -1px;" href="' . $aHref . '" title="' . $aTitle . '"><img src="' . $imgSrc . '"></a>';
	
    return $subject;
}

function sliceShow($params) {
	global $REX;
	
	extract($params);
	
	$sqlStatement = 'SELECT status, ctype FROM '.$REX['TABLE_PREFIX'] . 'article_slice WHERE id = '. $slice_id;
	$sql = rex_sql::factory();
	$sql->setQuery($sqlStatement);
	
	if (($sql->getValue('status') == 1) || $REX['REDAXO']) {
		return $subject;
	} else {
		return '<?php if (false) { ?>' . $subject . '<?php } ?>';
	}
}

function updateSliceStatusInDB($articleID, $cLang, $sliceID, $newStatus) {
	global $REX;

	// update db
	$sql = rex_sql::factory();
	$sql->setQuery('UPDATE ' . $REX['TABLE_PREFIX'] . 'article_slice SET status = ' . $newStatus . ' WHERE id=' . $sliceID);
	
	// delete cached article (important!)
	rex_deleteCacheArticleContent($articleID, $cLang);
}

function addJSCode($params) {
	global $REX;
	global $I18N;
	
	$script = "
		<!-- BEGIN slice_status -->
		<script type=\"text/javascript\">
			jQuery(document).ready(function($) {
				toggleSliceVisibility();
			});

			function toggleSliceVisibility() {
				// remove wrapper divs and make all slices visible (only for ajax mode important)
				jQuery('.bg-wrap').find('.rex-content-editmode-slice-output').unwrap();
				
				var jSliceTitleBar = jQuery('.slice-status').parents('.rex-content-editmode-module-name');
				var jSliceContent = jSliceTitleBar.next('.rex-content-editmode-slice-output');
				
				jSliceTitleBar.css('background', '');
				jSliceContent.css('background', '');
				jSliceContent.css('opacity', 1);

				// toggle visibility for offline slices
				var jOfflineSliceTitleBar = jQuery('.slice-status.offline').parents('.rex-content-editmode-module-name');
				var jOfflineSliceContent = jOfflineSliceTitleBar.next('.rex-content-editmode-slice-output');

				jOfflineSliceTitleBar.css('background', '" . $REX['ADDON']['slice_status']['offline_slice_titlebar_background'] . "');
				jOfflineSliceContent.wrap('<div class=\"bg-wrap\" style=\"background: ' + '" . $REX['ADDON']['slice_status']['offline_slice_content_background'] . "'  + ';\" />');
				jOfflineSliceContent.css('background', '" . $REX['ADDON']['slice_status']['offline_slice_content_background'] . "');
				jOfflineSliceContent.css('opacity', " . $REX['ADDON']['slice_status']['offline_slice_content_opacity'] . ");
				
			}

			function updateSliceStatus(articleID, cLang, sliceID, curStatus) {
				// retrieve stuff for new status
				if (curStatus == 1) {
					var aClass = 'slice-status slice-' + sliceID + ' offline';
					var aTitle = '" . $I18N->msg('toggle_slice_online') . "';
					var imgSrc = '/files/addons/slice_status/off.png';
					var newStatus = '0';
				} else {
					var aClass = 'slice-status slice-' + sliceID + ' online';
					var aTitle = '" . $I18N->msg('toggle_slice_offline') . "';
					var imgSrc = '/files/addons/slice_status/on.png';
					var newStatus = '1';
				}

				// construct href
				var aHref = 'javascript:updateSliceStatus(' + articleID + ',' + cLang + ',' + sliceID + ',' + newStatus + ');';
				
				// make ajax call to update slice status in db (php function 'updateSliceStatusInDB' is called in config.inc.php)
				jQuery.ajax({ 
					type: 'GET',
					url: '/redaxo/index.php?function=updateslicestatus&new_status=' + newStatus + '&slice_id=' + sliceID + '&article_id=' + articleID + '&clang=' + cLang + '',
					success: function(data) {
						// finally modify hmtl markup, so that new slice status is reflected
						var jCurSlice = jQuery('.slice-status.slice-' + sliceID);
						
						jCurSlice.attr('title', aTitle);
						jCurSlice.attr('href', aHref);
						jCurSlice.attr('class', aClass);
						jCurSlice.find('img').attr('src', imgSrc);
						
						toggleSliceVisibility();
					}
				});
			}
		</script>
		<!-- END slice_status -->
	";

	return str_replace('</body>', $script . "\r\n" . '</body>', $params['subject']);
}
?>
