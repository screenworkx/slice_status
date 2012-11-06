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


class SliceOnOff
{
  static private $instance         = null;

  protected $getParam_sliceId      = 'a356_slice_id';
  protected $getParam_setState     = 'a356_set_state';
  protected $getParam_ajaxRequest  = 'a356_ajax';
  protected $dbField_isOnline      = 'a356_is_online';
  protected $onlineSwitchFieldName = 'a356_onoff';
  protected $defaultOnlineValue    = 1;
  private   $onlineStates          = array();

  private   $debug                 = FALSE;

  private   $updatesDone           = false;
  private   $isAjaxRequest         = false;
  private   $ajaxResponse          = '';

  private   $isArticleOverviewPage = false;

  // Namen für Link-Klassen
  private   $redLinkClass          = 'rex-clr-grn';
  private   $greenLinkClass        = 'rex-clr-red';

  // Ersetzungen
  private   $arrToFind             = array('<input type="submit" name="btn_save"', '<input type="submit" value="Block speichern" name="btn_save"');

  private   $query_count           = 0;


  /**
    * Privater Konstruktor. Klasse wendet Singleton-Entwurfsmuster an, d.h. es kann nur eine Instanz von ihr geben.
    */
  private function __construct()
  {
    global $REX;

    if (isset($_REQUEST[$this->getParam_ajaxRequest])) {
      $this->isAjaxRequest = true;
    }

    if ($REX['REDAXO']) {
      rex_register_extension('ART_SLICE_MENU', array($this, 'handleSliceEditMenu'));
      rex_register_extension('OUTPUT_FILTER', array($this, 'outputFilter'));
    }
    rex_register_extension('SLICE_SHOW', array($this, 'sliceShow'));
    if ($this->debug) {
      rex_register_extension('OUTPUT_FILTER', array($this, 'frontendOutputFilter'));
    }

    // Ab Redaxo 4.2 werden neue Klassennamen verwendet und der HTML-Code des Backends wurde teilweise geändert
    if (intval($REX['SUBVERSION']) >= 2) {
      $this->redLinkClass = 'rex-tx2';
      $this->greenLinkClass = 'rex-tx3';
    }
  }


  /**
    * Debug-Funktion
    */
  public function frontendOutputFilter($params)
  {
    return str_replace('</body>', '<div style="position: absolute; top: 0; left: 0;">Queries done by Slice_onoff: ' . $this->query_count . '</div></body>', $params['subject']);
  }


  /**
    * Zugriff auf das Klassenobjekt
    *
    * @return     Eine Referenz auf die Instanz dieser Klasse
    */
  public static function instance()
  {
    if (SliceOnOff::$instance === null) {
      SliceOnOff::$instance = new SliceOnOff();
    }
    return SliceOnOff::$instance;
  }

  /**
    * Callback für ART_SLICE_MENU
    */
  public function handleSliceEditMenu($params)
  {
    global $REX;
    extract($params);
    $this->isArticleOverviewPage = true;
    $this->doUpdates($params);

    if (!$this->userHasPermission()) {
      return $subject;
    }

    // Falls Slice-Inhalt gerade bearbeitet wird, keinen Toolbar-Button zeigen.
    if ((rex_request('btn_update', 'string') != '' OR sizeof($_POST) == 0)
      AND rex_request('function', 'string') == 'edit'
      AND rex_request('slice_id', 'int') == $slice_id) return $subject;

    $is_online = $this->isOnline($slice_id, $article_id, $clang);

    $link = 'index.php?page=content&amp;clang=' . $clang . '&amp;ctype=' . $ctype .
      '&amp;category_id=' . $GLOBALS['category_id'] . '&amp;article_id=' . $article_id .
      '&amp;' . $this->getParam_sliceId . '=' . $slice_id;

    $js_onclick = "var obj = this; jQuery.ajax({type: 'GET', url: '" . $link .
      '&amp;' . $this->getParam_ajaxRequest . "=1&hash=' + parseInt(Math.random() * 2000000000), " .
      "success: function(msg){ if (msg.length != 1) { alert(msg); return false;}; " .
      "obj.innerHTML = (msg == 1 ? '<img src=\'/files/addons/slice_onoff/on.png\' />' : '<img src=\'/files/addons/slice_onoff/off.png\' />'); " .
      "obj.className = (msg == 1 ? '" . $this->greenLinkClass . "' : '" . $this->redLinkClass . "'); }}); return false;";

    $link .= '&amp;' . $this->getParam_setState . '=' . ($is_online ? 0 : 1);

    $label = ($is_online ? '<img src="/files/addons/slice_onoff/on.png" />' : '<img src="/files/addons/slice_onoff/off.png" />');
    $class = ($is_online ? $this->greenLinkClass : $this->redLinkClass);

    // style-Attribut verwindert Trennlinie in Redaxo 4.2
    $onoff_switch = "<li><a href=\"$link\" onclick=\"$js_onclick\" class=\"$class\" style=\"background: none;\">$label</a></li>";

    array_unshift($subject, $onoff_switch); // Switch in Elemente-Array einbauen
    return $subject;
  }


  /**
    * Callback für SLICE_SHOW im Frontend und Backend
    */
  public function sliceShow($params)
  {
    global $REX;
    extract($params);
    $this->doUpdates($params);

    // Dieser Extension-Point erlaubt es anderen Addons, zu beeinflussen, wann ein Slice angezeigt wird und wann nicht.
    $conditions = '';
    $conditions = rex_register_extension_point('A356_SLICE_SHOW_CONDITION', $conditions, $params);

    return "<?php if(".($this->isOnline($slice_id)?'TRUE':'FALSE')." OR SliceOnOff::isBackend()$conditions): ?>" . $subject . '<?php endif; ?>';
  }


  /**
    * Callback fuer OUTPUT_FILTER im Backend
    */
  public function outputFilter($params)
  {
    global $REX, $REX_USER;
    extract($params);

    if ($this->isAjaxRequest) {
      if (!isset($REX_USER)) {
        $this->sendAjaxResponseOnOutput('Your session has expired or you have logged out. Please login to continue editing.');
      }
      return $this->ajaxResponse;
    }

    $function = rex_request('function', 'string');
    $slice_id = rex_request('slice_id', 'int');
    $page = rex_request('page', 'string');
    $mode = rex_request('mode', 'string');

    // When editing or adding a Slice, add a switch to the editing form
    if($page == 'content' AND $mode == 'edit' AND ($function == 'add' OR $function == 'edit'))
    {
      // When editiing a slice, use it's online state, when adding a slice, use the default value
      $is_online = ($function == 'edit') ? $this->isOnline($slice_id) : $this->defaultOnlineValue;

      $str_to_add = '<span style="float: right; width: auto; margin: 5px; 0 0 0; padding: 0;">Status:
        <label class="rex-clr-grn" style="float: none; display: inline; font-weight: bold;">
          <input type="radio" name="' . $this->onlineSwitchFieldName . '" value="1"' . ($is_online == 1 ? ' checked="checked"' : '') . ' /> Online</label> /
        <label class="rex-clr-red" style="float: none; display: inline; font-weight: bold;">
          <input type="radio" name="' . $this->onlineSwitchFieldName . '" value="0"' . ($is_online == 0 ? ' checked="checked"' : '') . ' /> Offline</label>
      </span>';

      if (intval($REX['SUBVERSION']) >= 2) {
        if ($function == 'edit') {
          $to_find = '<p class="rex-form-col-a rex-form-submit">';
        } else {
          $to_find = '<input class="rex-form-submit" type="submit" name="btn_save" ';
        }
        $to_replace = $str_to_add . $to_find;

      } else {
        $to_find = $this->arrToFind;
        $to_replace = array($str_to_add . $this->arrToFind[0], $str_to_add . $this->arrToFind[1]);
      }

      return str_replace($to_find, $to_replace, $subject);
    }

    return $subject;
  }

  /**
    * Online-Status einmalig bei Seitenaufruf aktualisieren
    */
  private function doUpdates($params)
  {
    if ($this->updatesDone === true) {
      return;
    }
    extract($params);

    $post_online_state = rex_request($this->onlineSwitchFieldName, 'int', -1);
    $switch_slice_id = rex_request($this->getParam_sliceId, 'int', 0);

    if ($extension_point == 'ART_SLICE_MENU' AND $switch_slice_id != 0) {

      if (!$this->userHasPermission()) {
        $error_msg = 'You do not have permission to change the online state of slices.';
        if ($this->isAjaxRequest) {
          $this->sendAjaxResponseOnOutput($error_msg);
        } else {
          rex_warning($error_msg);
        }
        return;
      }

      $online_state = rex_request($this->getParam_setState, 'int', -1);

      if ($online_state != -1) {
        $this->setOnline($switch_slice_id, $online_state);
      } else {
        $this->toggleOnline($switch_slice_id);
      }

      if ($this->isAjaxRequest) {
        $this->sendAjaxResponseOnOutput($this->isOnline($switch_slice_id));
      }
    } else if ($post_online_state != -1 AND $this->userHasPermission()) {

      $function = rex_request('function', 'string');
      $slice_id = rex_request('slice_id', 'int');
      if ($function == 'edit') {
        $this->setOnline($slice_id, $post_online_state);
      } else if ($function == 'add') {
        $this->setOnline($this->getLastInsertedSliceId(), $post_online_state);
      }
    }

    $this->updatesDone = true;
  }


  private function userHasPermission()
  {
    global $REX_USER;

    if (!isset($REX_USER)) {
      if ($this->isAjaxRequest) {
        $this->sendAjaxResponseOnOutput('Your session has expired or you have logged out. Please login to continue editing.');
      }
      return false;
    }

    if ($REX_USER->isAdmin() OR $REX_USER->hasPerm('slice_onoff[]')) {
      return true;
    } else {
      if ($this->isAjaxRequest) {
        $this->sendAjaxResponseOnOutput('You do not have permission to change the online state of slices.');
      }
      return false;
    }
  }


  public function isOnline($slice_id)
  {
    global $REX;
    $slice_id = (int) $slice_id;

    if ($slice_id < 1) {
      $this->throwError('Slice parameter must not be equal 1 or greater.');
      return;
    }
    $this->preloadOnlineStates($slice_id, 'slice_id');
    return $this->onlineStates[$slice_id];
  }


  private function toggleOnline($slice_id)
  {
    if ($this->isOnline($slice_id)) {
      $this->setOnline($slice_id, 0);
    } else {
      $this->setOnline($slice_id, 1);
    }
    return $this->isOnline($slice_id);
  }


  private function setOnline($slice_id, $online_state)
  {
    global $REX;
    $this->preloadOnlineStates($slice_id, 'slice_id');

    $slice_id = (int) $slice_id;
    $online_state = ($online_state == 1 ? 1 : 0);

    if ($slice_id < 1) {
      $this->throwError('Slice ID parameter must be equal 1 or greater. Given ID was: ' . $slice_id);
      return;
    }

    $sql = rex_sql::factory();

    $sql->setQuery('UPDATE ' . $REX['TABLE_PREFIX'] . 'article_slice
      SET ' . $this->dbField_isOnline . ' = ' . $online_state . ' WHERE id=' . $slice_id);
    $this->query_count++;
    $this->onlineStates[$slice_id] = $online_state;

    if ($this->isAjaxRequest) {
      // update article cache file
      $sql->setQuery('SELECT article_id, clang FROM '.$REX['TABLE_PREFIX'] . 'article_slice WHERE id = '.$slice_id);
      $result = $sql->getRow();
      if ($result['article_id']) {
        $article_id = $result['article_id'];
        $clang = $result['clang'];
        rex_generateArticleContent($article_id, $clang);
      }
    }
  }


  /**
    * Lädt den Online-Status für alle Module aus einem oder mehrere Artikel
    *
    * @param mixed Ein int article_id oder ein Array der Form array(int article_id, int article_id2, ...)
    * @return void
    */
  public function preloadOnlineStates($param_ids, $load_by='article_id')
  {
    global $REX;

    $sql = rex_sql::factory();

    if ($load_by == 'article_id') {
      if (is_array($param_ids)) {
        $where_conditition = 'article_id IN (' . implode(',', array_map('intval', $param_ids)) . ')';
      } else {
        $where_conditition = 'article_id=' . intval($param_ids);
      }
    } else if ($load_by == 'slice_id') {
      if (isset($this->onlineStates[$param_ids])) {
        //$this->sendAjaxResponseOnOutput("Online state for $param_ids already loaded");
        return;
      }

      $result = $sql->getArray('SELECT article_id FROM ' . $REX['TABLE_PREFIX'] . 'article_slice
        WHERE id = ' . intval($param_ids));
      if (empty($result)) {
        $this->throwError("Could not preload online states for given slice_id ($param_ids).");
        return false;
      }
      //echo "Loading online states for slice_id=$param_ids<br />";
      $where_conditition = 'article_id=' . intval($result[0]['article_id']);
    } else {
      $this->throwError('No valid slice parameters givven');
      return;
    }

    $result = $sql->getArray('SELECT id, ' . $this->dbField_isOnline . ' FROM ' . $REX['TABLE_PREFIX'] . 'article_slice
      WHERE ' . $where_conditition);
    $this->query_count++;
    if (sizeof($result) == 0) {
      $this->throwError("Could not preload online states where condition was: $where_conditition");
      return false;
    }

    foreach ($result AS $row) {
      if (isset($this->onlineStates[$row['id']])) continue;

      $this->onlineStates[$row['id']] = $row[$this->dbField_isOnline];
    }
  }


  private function getLastInsertedSliceId()
  {
    global $REX, $REX_USER;
    $sql = rex_sql::factory();
    $res = $sql->getArray('SELECT MAX(id) AS max_id FROM ' . $REX['TABLE_PREFIX'] . 'article_slice
      WHERE createuser = ' . $sql->escape($REX_USER->getValue('login'))); // Zuletzt hinzugefï¿½gte ID bestimmen
    return $res[0]['max_id'];
  }


  private function sendAjaxResponseOnOutput($message)
  {
    global $REX;
    header('Content-Type: text/plain; charset=utf-8');
    $this->ajaxResponse .= strstr($REX['LANG'], 'utf8') ? utf8_encode($message) : $message;
  }


  private function throwError($message)
  {
    $this->sendAjaxResponseOnOutput("Error: $message");
    error_log('Error in REDAXO slice_onoff Addon: ' . $message);
  }


  public static function isBackend()
  {
    global $REX;
    if ($REX['REDAXO']) {
      return true;
    } else {
      return false;
    }
  }
}
