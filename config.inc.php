<?php
/**
 * Redaxo Addon slice_onoff - Online-Status für Slices
 *
 * @author Fabian Michael me[AT]fm86[PUNKT]de
 * @author Sven Kesting <sk[AT]decaf[PUNKT]de>
 *
 * @link https://github.com/jdlx/slice_onoff
 * @link http://www.redaxo.org/de/download/addons/?addon_id=356
 *
 * @package redaxo 4.2.x/4.3.x/4.4.x
 * @version 0.3.1
 *
 */

// addon identifier
$mypage = 'slice_onoff';

$REX['ADDON']['rxid'][$mypage] = '356';
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['perm'][$mypage] = 'slice_onoff[]';
$REX['ADDON']['version'][$mypage] = '0.3.1';
$REX['ADDON']['author'][$mypage] = 'Fabian Michael <me[AT]fm86[PUNKT]de> | caching by Sven Kesting <sk[AT]decaf[PUNKT]de>';

// Berechtigungen
$REX['PERM'][] = 'slice_onoff[]';

require $REX['INCLUDE_PATH'] . '/addons/slice_onoff/classes/class.SliceOnOff.php';
SliceOnOff::instance();
