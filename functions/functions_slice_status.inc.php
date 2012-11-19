<?php
function modifySliceEditMenu($params) {
    global $REX;
	global $I18N;
	
    extract($params);
	//out($params);

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

    //array_push($subject, $statusSwitch);
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
		return $subject; // . '<style type="text/css">* { background: #000 !important; }</style>';
	} else {
		return '<?php if (false) { ?>' . $subject . '<?php } ?>';
	}
}
?>
