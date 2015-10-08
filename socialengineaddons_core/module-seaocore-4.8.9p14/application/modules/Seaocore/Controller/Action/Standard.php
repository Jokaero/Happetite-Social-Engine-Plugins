<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Standard.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
abstract class Seaocore_Controller_Action_Standard extends Core_Controller_Action_Standard {

  protected $_triggerJSEvent;

  protected function _redirectCustom($to, $options = array()) {
    $options = array_merge(array(
        'prependBase' => false
            ), $options);

    // Route
    if (is_array($to) && empty($to['uri'])) {
      $route = (!empty($to['route']) ? $to['route'] : 'default' );
      $reset = ( isset($to['reset']) ? $to['reset'] : true );
      unset($to['route']);
      unset($to['reset']);
      $to = $this->_helper->url->url($to, $route, $reset);
      // Uri with options
    } else if (is_array($to) && !empty($to['uri'])) {
      $to = $to['uri'];
      unset($params['uri']);
      $params = array_merge($params, $to);
    } else if (is_object($to) && method_exists($to, 'getHref')) {
      $to = $to->getHref();
    }

    if (!is_scalar($to)) {
      $to = (string) $to;
    }

    $message = (!empty($options['message']) ? $options['message'] : 'Changes saved!' );

    switch ($this->_helper->contextSwitch->getCurrentContext()) {
      case 'smoothbox':
        return $this->_forwardCustom('success', 'utility', 'core', array(
                    'messages' => array($message),
                    'smoothboxClose' => true,
                    'redirect' => $to
        ));
        break;
      case 'json': case 'xml': case 'async':
      // What should be do here?
      //break;
      default:
        if ($this->isAjaxPageRequest() && Engine_API::_()->seaocore()->isSiteMobileModeEnabled()) {
          $this->view->notSuccessMessage = true;
          return $this->_forwardCustom('success', 'utility', 'core', array(
                      'smoothboxClose' => true,
                      'redirect' => $to
          ));
        } else {
          return $this->_helper->redirector->gotoUrl($to, $options);
        }
        break;
    }
  }

  protected function _forwardCustom($action, $controller = null, $module = null, array $params = null) {
    // Parent
    $request = $this->getRequest();

    if (null !== $params) {
      $request->setParams($params);
    }

    if (null !== $controller) {
      $request->setControllerName($controller);

      // Module should only be reset if controller has been specified
      if (null !== $module) {
        $request->setModuleName($module);
      }
    }

    $request->setActionName($action);
    if (Engine_API::_()->seaocore()->isSiteMobileModeEnabled()) {
      $sr_response = Engine_Api::_()->sitemobile()->setupRequest($request);
    }
    $request->setDispatched(false);
  }

  protected function _gotoRouteCustom(array $urlOptions = array(), $name = null, $reset = false, $encode = true) {
    if ($this->isAjaxPageRequest() && Engine_API::_()->seaocore()->isSiteMobileModeEnabled()) {
      $url = Zend_Controller_Front::getInstance()->getRouter()->assemble($urlOptions, $name, $reset, $encode);
      $this->view->notSuccessMessage = true;
      $this->_forwardCustom('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'redirect' => $url
      ));
    } else {
      $this->_helper->redirector->gotoRoute($urlOptions, $name, $reset, $encode);
    }
  }

  protected function isAjaxPageRequest() {
    $request = $this->getRequest();
    if (Engine_Api::_()->hasModuleBootstrap('sitemobile') && Engine_API::_()->sitemobile()->isApp()) {
      return true;
    }
    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || $request->getParam('REQUEST_TYPE') == 'xmlhttprequest';
  }

  public function addResetContentTriggerEvent() {
//    if (!Engine_API::_()->seaocore()->isSiteMobileModeEnabled())
//      return;
    $mobileApi = Engine_Api::_()->sitemobile();
    $this->view->headTranslate($mobileApi->translateData());
    $this->view->languageData = $this->view->headTranslate()->render();
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewerDetails = array(
        'type' => $viewer->getType(),
        'id' => $viewer->getIdentity(),
        'guid' => $viewer->getGuid()
    );
    $this->view->mlocale = $this->view->locale()->getLocale()->__toString();
    $eventname = 'refreshcontent';
    $this->_triggerJSEvent[$eventname] = $eventname;
    $this->view->triggerEventsOnContentLoad = $this->_triggerJSEvent;
  }

  public function addContentTriggerEvent($eventname) {
    $this->_triggerJSEvent[$eventname] = $eventname;
    $this->view->triggerEventsOnContentLoad = $this->_triggerJSEvent;
  }

  protected function renderWidgetCustom($pageName = null) {
    $tabstype = $this->_getParam('tabstype');
//    if (empty($tabstype))
//      return;
    if (!Engine_Api::_()->seaocore()->isSitemobileApp())
      return;
    $content_id = $this->_getParam('tab');
    //  $params = $this->_getAllParams();
    // Render by content row
    if (null !== $content_id) {
      $view = $this->_getParam('view');
      $show_container = $this->_getParam('container', true);

      $moduleName = Engine_Api::_()->sitemobile()->isApp() ? 'sitemobileapp' : 'sitemobile';
      $prefix = '';
      if (Engine_Api::_()->sitemobile()->checkMode('tablet-mode')) {
        $prefix = 'tablet';
      }
      $contentTable = Engine_Api::_()->getDbtable($prefix . 'content', $moduleName);
      $row = $contentTable->find($content_id)->current();

      if (null !== $row) {
        // Build full structure from children
        $page_id = $row->page_id;
        
        if (empty($tabstype)) {
          $row_parent = $contentTable->find($row->parent_content_id)->current();
          if(empty($row_parent) || $row_parent->name !='sitemobile.container-tabs-columns')
            return;

          $pageTable = Engine_Api::_()->getDbtable($prefix . 'pages', $moduleName);
          $page = $pageTable->findRow(array('page_id = ?' => $page_id));
          if ($page) {
            if (empty($pageName))
              $pageName = $this->_helper->content
                      ->getContentName();
            if ($pageName != $page->name)
              return;
          }
        }
        $content = $contentTable->fetchAll($contentTable->select()->where('page_id = ?', $page_id));
        $structure = $pageTable->createElementParams($row);
        $children = $pageTable->prepareContentArea($content, $row);
        if (!empty($children)) {
          $structure['elements'] = $children;
        }
        $structure['request'] = $this->getRequest();
        $structure['action'] = $view;
        if($this->getContainerContent($structure, $show_container))
          return true;
      }
    }
  }

  public function getContainerContent($structure, $show_container) {

    // Create element (with structure)
    $element = new Engine_Content_Element_Container(array(
          'elements' => array($structure),
          'decorators' => array(
              'Children'
          )
    ));             
    // Strip decorators
    if (!$show_container) {
      foreach ($element->getElements() as $cel) {
        $cel->clearDecorators();
      }
    }

    foreach ($element->getElements() as $child) {
      $child->removeDecorator('Title');
    }
    $contentRender = $element->render();
    if ($element->getNoRender())
      return;

    $title = "";
    foreach ($element->getElements() as $child) {
      $title = $this->view->translate($child->getTitle());
      if (!$title)
        $title = $child->getName();

      if ($title) {
        if (method_exists($child, 'getWidget') && method_exists($child->getWidget(), 'getChildCount')) {
          $childCount = $child->getWidget()->getChildCount();
          if ($childCount)
            $title .=" ($childCount)";
        }
      }

      if (Engine_Api::_()->core()->hasSubject())
        $title .= ": " . Engine_Api::_()->core()->getSubject()->getTitle();

      Zend_Registry::set('setOnlyHeaderTitle', $title);

      break;
    }

    Zend_Registry::set('setHeaderBack', 'true');

    $this->getResponse()->setBody($contentRender);

    $this->_helper->viewRenderer->setNoRender(true);
    return true;
  }  
}