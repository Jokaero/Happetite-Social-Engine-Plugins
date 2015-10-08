<?php

/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_Content
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Abstract.php 9747 2012-07-26 02:08:08Z john $
 */

/**
 * @category   Engine
 * @package    Engine_Content
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Seaocore_Content_Widget_Abstract extends Engine_Content_Widget_Abstract {

  protected $_mobileAppFile = false;

  public function renderScript() {
    $view = $this->getView();
    $path = $this->getScriptPath();
    $mobilePath = $path = str_replace(APPLICATION_PATH . DIRECTORY_SEPARATOR, '', $path);

    $path .= DIRECTORY_SEPARATOR . $this->_action . '.tpl';
    if ($view->checkSiteModeSM()) {
      if (Engine_Api::_()->sitemobile()->isApp()) {
        $mobileAppPath = $mobilePath;
        $mobileAppPath.= DIRECTORY_SEPARATOR . 'sitemobileapp' . DIRECTORY_SEPARATOR . $this->_action . '.tpl';
        if ($this->_mobileAppFile) {
          $path = $mobileAppPath;
        } else {
          $mobilePath.= DIRECTORY_SEPARATOR . 'sitemobile' . DIRECTORY_SEPARATOR . $this->_action . '.tpl';
          if (file_exists($mobilePath)) {
            $path = $mobilePath;
          }
        }
      } else {
        $mobilePath.= DIRECTORY_SEPARATOR . 'sitemobile' . DIRECTORY_SEPARATOR . $this->_action . '.tpl';
        if (file_exists($mobilePath)) {
          $path = $mobilePath;
        }
      }
    }
    return $view->render($path);
  }

}
