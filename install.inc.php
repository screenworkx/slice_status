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

if (intval(PHP_VERSION) < 5)
{
  $REX['ADDON']['installmsg']['slice_onoff'] = 'Dieses Addon ben&ouml;tigt PHP 5!';
  $REX['ADDON']['install']['slice_onoff'] = 0;
}
else
{
  // CREATE/UPDATE DATABASE
  $sql = rex_sql::factory();
  $sql->setQuery('ALTER TABLE `' . $REX['TABLE_PREFIX'] . 'article_slice`
    ADD `a356_is_online` TINYINT( 1 ) UNSIGNED ZEROFILL NOT NULL DEFAULT "1"');


  $REX['ADDON']['install']['slice_onoff'] = 1;
}
