<?php

/**
 * slice_onoff Online-Status für Slices
 * @author me[AT]fm86[PUNKT]de Fabian Michael
 * @package redaxo4
 */

if (intval(PHP_VERSION) < 5) {
  $REX['ADDON']['installmsg']['slice_onoff'] = 'Dieses Addon ben&ouml;tigt PHP 5!';
  $REX['ADDON']['install']['slice_onoff'] = 0;

}
else
{
  // CREATE/UPDATE DATABASE
  $sql = new rex_sql();
  $sql->setQuery('ALTER TABLE `' . $REX['TABLE_PREFIX'] . 'article_slice`
    ADD `a356_is_online` TINYINT( 1 ) UNSIGNED ZEROFILL NOT NULL DEFAULT "1"');

  // CREATE/UPDATE MODULES
  // -none-

  // CREATE/UPDATE PAGES
  // -none-

  // REGENERATE SITE
  // -none-

  // COPY FILES
  // -none-


  $REX['ADDON']['install']['slice_onoff'] = 1;
  // ERRMSG IN CASE: $REX['ADDON']['installmsg']['example'] = 'Error occured while installation';
}

?>