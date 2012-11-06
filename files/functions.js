var sliceTitlebarBackground = '#dddddd';
var sliceContentBackground = '#e7e5e5';
var sliceContentOpacity = 0.6;

var $ = jQuery.noConflict();

$(document).ready( function() {
	var $sliceTitlebar = $('.slice-status-offline').parents('.rex-content-editmode-module-name');
	var $sliceContent = $sliceTitlebar.next('.rex-content-editmode-slice-output');

	// titlebar
	$sliceTitlebar.css('background', sliceTitlebarBackground);
	
	// slice content
	$sliceContent.wrap('<div style="background: ' + sliceContentBackground  + ';" />');
	$sliceContent.css('background', sliceContentBackground);
	$sliceContent.css('opacity', sliceContentOpacity);
});

