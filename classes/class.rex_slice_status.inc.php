<?php
class rex_slice_status {
	static function fetchSliceStatus() {
		global $REX;
		$fetchedSliceStatus = array();
	
		$sqlStatement = 'SELECT id, status FROM ' . $REX['TABLE_PREFIX'] . 'article_slice';
		$sql = rex_sql::factory();
		$sql->setQuery($sqlStatement);

		for ($i = 0; $i < $sql->getRows(); $i++) {
			$fetchedSliceStatus[$sql->getValue('id')] = $sql->getValue('status');
			$sql->next();
		}

		return $fetchedSliceStatus;
	}

	static function modifySliceEditMenu($params) {
		global $REX;
		global $I18N;
		global $slices;
	
		extract($params);

		// get status of current slice
		if (!isset($slices)) {
			// with this now only one db query is necessary
			$slices = self::fetchSliceStatus();
		}
	
		$curStatus = $slices[$slice_id];

		// retrieve stuff for new status
		if ($curStatus == 1) {
			$aClass = 'slice-status slice-' . $slice_id . ' online';
			$aTitle = $I18N->msg('toggle_slice_offline');
			$newStatus = '0';
		} else {
			$aClass = 'slice-status slice-' . $slice_id . ' offline';
			$aTitle = $I18N->msg('toggle_slice_online');
			$newStatus = '1';
		}
	
		// construct href
		if ($REX['ADDON']['slice_status']['ajax_mode']) {
			$aHref = 'javascript:updateSliceStatus(' . $article_id . ',' . $clang . ',' . $slice_id . ',' . $curStatus . ');';
		} else {
			$aHref = 'index.php?page=content&article_id=' . $article_id . '&mode=edit&slice_id=' . $slice_id . '&clang=' . $clang . '&ctype=' . $ctype . '&function=updateslicestatus' . '&new_status=' . $newStatus . '#slice' . $slice_id;	
		}
	
		// inject link in slice menu
		$dataAttributes = 'data-title-online="' . $I18N->msg('toggle_slice_online') . '" data-title-offline="' . $I18N->msg('toggle_slice_offline') . '"';
		$statusLink = '<a class="' . $aClass . '" href="' . $aHref . '" title="' . $aTitle . '" ' . $dataAttributes . '><span>Slice Status</span></a>';
	
		$subject[] = $statusLink;
	
		return $subject;
	}

	static function sliceShow($params) {
		global $REX;
	
		extract($params);
	
		$sqlStatement = 'SELECT status FROM '.$REX['TABLE_PREFIX'] . 'article_slice WHERE id = '. $slice_id;
		$sql = rex_sql::factory();
		$sql->setQuery($sqlStatement);
	
		if (($sql->getValue('status') == 1) || $REX['REDAXO']) {
			return $subject;
		} else {
			return '<?php if (false) { ?>' . $subject . '<?php } ?>';
		}
	}

	static function updateSliceStatusInDB($articleID, $cLang, $sliceID, $newStatus) {
		global $REX;

		// update db
		$sql = rex_sql::factory();
		$sql->setQuery('UPDATE ' . $REX['TABLE_PREFIX'] . 'article_slice SET status = ' . $newStatus . ' WHERE id=' . $sliceID);
	
		// delete cached article (important!)
		rex_deleteCacheArticleContent($articleID, $cLang);
	}

	static function appendToPageHeader($params) {
		$insert = '<!-- BEGIN slice_status -->' . PHP_EOL;
		$insert .= '<link rel="stylesheet" type="text/css" href="../files/addons/slice_status/slice_status.css" />' . PHP_EOL;
		$insert .= '<script type="text/javascript" src="../files/addons/slice_status/slice_status.js"></script>' . PHP_EOL;
		$insert .= '<!-- END slice_status -->';
	
		return $params['subject'] . PHP_EOL . $insert;
	}
}
?>
