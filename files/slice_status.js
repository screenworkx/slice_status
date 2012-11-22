jQuery(document).ready(function($) {
	prepareSlices();
	toggleSliceVisibility();
});

function prepareSlices() {
	var jSliceTitleBar = jQuery('.slice-status').parents('.rex-content-editmode-module-name');
	var jSliceContent = jSliceTitleBar.next('.rex-content-editmode-slice-output');

	jSliceTitleBar.addClass('slice-title');
	jSliceContent.addClass('slice-content');
	jSliceContent.wrap('<div class=\"slice-content-wrap\" />'); // this is for opacity set in css
}

function toggleSliceVisibility() {
	// restore styles for all slices (only for ajax mode important)
	jQuery('.slice-title').removeClass('offline');
	jQuery('.slice-content-wrap').removeClass('offline');
	jQuery('.slice-content').removeClass('offline');

	// toggle visibility for offline slices
	var jOfflineSliceTitleBar = jQuery('.slice-status.offline').parents('.rex-content-editmode-module-name');
	var jOfflineSliceContentWrap = jOfflineSliceTitleBar.next('.slice-content-wrap');
	var jOfflineSliceContent = jOfflineSliceContentWrap.find('.rex-content-editmode-slice-output');

	jOfflineSliceTitleBar.addClass('offline');
	jOfflineSliceContentWrap.addClass('offline');
	jOfflineSliceContent.addClass('offline');
}

function updateSliceStatus(articleID, cLang, sliceID, curStatus) {
	var jCurSlice = jQuery('.slice-status.slice-' + sliceID);
	
	// retrieve stuff for new status
	if (curStatus == 1) {
		var aClass = 'slice-status slice-' + sliceID + ' offline';
		var aTitle = jCurSlice.attr('data-title-online');
		var newStatus = '0';
	} else {
		var aClass = 'slice-status slice-' + sliceID + ' online';
		var aTitle = jCurSlice.attr('data-title-offline');
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
			jCurSlice.attr('title', aTitle);
			jCurSlice.attr('href', aHref);
			jCurSlice.attr('class', aClass);
			
			toggleSliceVisibility();
		}
	});
}
