<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: CoreController.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_CoreController extends Core_Controller_Action_Standard {

  // ACTION FOR DOWNLOAD
  public function downloadAction() {
    $file_id = (isset($_GET['file_id'])) ? $_GET['file_id'] : 0;
    if (empty($file_id))
      exit();
    //GET PATH
    $path = urldecode($_GET['path']);
    $includeSize = false;
    if (!preg_match("~^(?:f|ht)tps?://~i", $path)) {
      $path = preg_replace('/\.{2,}/', '.', $path);
      $path = preg_replace('/[\/\\\\]+/', '/', $path);
      $path = trim($path, './\\');

      $includeSize = true;
      if (!Engine_Api::_()->seaocore()->isCdn()) {
        $pathArray = explode('?', $path);
        $path = $pathArray['0'];
        $pathRemoveArray = explode('/', $path);

        if ($pathRemoveArray['0'] != 'public') {
          unset($pathRemoveArray['0']);
        }
        $path = implode('/', $pathRemoveArray);
        $path = APPLICATION_PATH . '/' . $path;
      }
    }

    $explodePath = explode("?", $path);
    $path = $explodePath['0'];
    $doFlush = true;
    if (ob_get_level()) {
      @ob_end_clean();
      $doFlush = false;
    }

    header("Content-Disposition: attachment; filename=" . @urlencode(basename($path)), true);
    header("Content-Transfer-Encoding: Binary", true);
    header("Content-Type: application/force-download", true);
    header("Content-Type: application/octet-stream", true);
    header("Content-Type: application/download", true);
    header("Content-Description: File Transfer", true);
    if (!Engine_Api::_()->seaocore()->isCdn() && $includeSize && $doFlush) {
      header("Content-Length: " . @filesize($path), true);
    }
    if ($doFlush) {
      flush();
    }
    echo Engine_Api::_()->getItem('storage_file', $file_id)->read();
    if ($doFlush) {
      flush();
    }

    exit();
  }

  public function showTooltipTagAction() {
    if (!$this->_helper->requireSubject()->checkRequire())
      return;

    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    // Subject doesn't have tagging
    if (!method_exists($subject, 'tags')) {
      throw new Engine_Exception('Subject doesn\'t support tagging');
    }

    // Get tagmao
    $tagmap = $subject->tags()->getTagMapById($this->_getParam('tagmap_id', 0));
    if (!($tagmap instanceof Core_Model_TagMap)) {
      throw new Engine_Exception('Tagmap missing');
    }
    $this->view->tagger = $tagmap->getTagger();
  }

}

?>