<?php
/**
 * slice_onoff Online-Status f�r Slices
 * @author me[AT]fm86[PUNKT]de Fabian Michael
 * @package redaxo4
 */

// addon identifier
$mypage = 'slice_onoff';

$REX['ADDON']['rxid'][$mypage] = '356';
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['perm'][$mypage] = 'slice_onoff[]';
$REX['ADDON']['version'][$mypage] = '0.2d';
$REX['ADDON']['author'][$mypage] = 'Fabian Michael <me[AT]fm86[PUNKT]de> | caching by Sven Kesting <sk[AT]decaf[PUNKT]de>';

// Berechtigungen
$REX['PERM'][] = 'slice_onoff[]';

require $REX['INCLUDE_PATH'] . '/addons/slice_onoff/classes/class.SliceOnOff.php';
SliceOnOff::instance();

?>