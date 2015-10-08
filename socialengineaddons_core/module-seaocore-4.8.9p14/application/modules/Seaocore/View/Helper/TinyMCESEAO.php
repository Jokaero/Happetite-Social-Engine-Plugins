<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: GetContent.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_View_Helper_TinyMCESEAO extends Zend_View_Helper_Abstract {

  /**
   * Assembles action string
   * 
   * @return string
   */
  protected $_version;

  public function tinyMCESEAO() {
    if (!$this->_version) {
      $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
      $this->_version = $coremodule->version;
    }
    return $this;
  }

  public function addJS($onlyPath = false) {
    #WE ARE NOT USING STATIC BASE URL BECAUSE SOCIAL ENGINE ALSO NOT USE FOR THIS JS
    #CHECK HERE Engine_View_Helper_TinyMce => protected function _renderScript()
    if ($this->_version < '4.7.0') {
      if ($onlyPath)
        return $this->view->baseUrl() . '/externals/tinymce/tiny_mce.js';
      $this->view->headScript()->appendFile($this->view->baseUrl() . '/externals/tinymce/tiny_mce.js');
    } else {
      if ($onlyPath)
        return $this->view->baseUrl() . '/externals/tinymce/tinymce.min.js';
      $this->view->headScript()->appendFile($this->view->baseUrl() . '/externals/tinymce/tinymce.min.js');
    }
  }

  public function render($params = array()) {
    if (!isset($params['upload_url']))
      $params['upload_url'] = '';
    if ($this->_version < '4.7.0') {
      return 'tinyMCE.init({
		mode: "exact",
		elements : ' . $params['element_id'] . ',
        forced_root_block: false,
        force_p_newlines: false,
		plugins: "directionality,preview,table,layer,style,xhtmlxtras,media,paste,spellchecker,iespell,fullscreen",
		theme: "advanced",
		theme_advanced_buttons1: "ltr,rtl,fullscreen,preview,code,|,cut,copy,paste,pastetext,pasteword,|,undo,redo,|, link,unlink,anchor,charmap,image,media,|,hr,removeformat,cleanup",
		theme_advanced_buttons2: "bold,italic,underline,strikethrough,|,bullist,numlist,|,outdent,indent,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,sub,sup,|,table",
		theme_advanced_buttons3: "formatselect,fontselect,fontsizeselect,|,forecolor,backcolor,|,spellchecker,iespell",
		theme_advanced_toolbar_align: "left",
		theme_advanced_toolbar_location: "top",
		element_format: "html",
		height: "225px",
		convert_urls: false,
		media_strict: false,
		language: "' . $params['language'] . '",
		directionality: "' . $params['directionality'] . '",
		upload_url: "' . $params['upload_url'] . '"
		});';
    } else {
      if (empty($params['upload_url'])) {
        $uploadPlugin = '';
      } else {
        $uploadPlugin = "jbimages,";
      }
      return 'tinymce.init({
        mode: "exact",
        forced_root_block: false,
        force_p_newlines: false,
        elements : ' . $params['element_id'] . ',
        plugins: "directionality,advlist,autolink,lists,link,image,' . $uploadPlugin . 'charmap,print,preview,hr,anchor,pagebreak,searchreplace,wordcount,visualblocks,visualchars,code,fullscreen,insertdatetime,media,nonbreaking,save,table,contextmenu,directionality,emoticons,paste,textcolor",
        theme: "modern",
        menubar: true,
        statusbar: false,
        toolbar1: "ltr,rtl,undo,redo,removeformat,pastetext,|,code,link,media,image,' . $uploadPlugin . 'emoticons,|,bullist,numlist,|,print,preview,fullscreen",
        toolbar2: "fontselect,fontsizeselect,bold,italic,underline,strikethrough,forecolor,backcolor,|,alignleft,aligncenter,alignright,alignjustify,|,outdent,indent,blockquote",
toolbar3: "",
        element_format: "html",
        height: "225px",
        convert_urls: false,
        language: "' . $params['language'] . '",
        directionality: "' . $params['directionality'] . '",
        upload_url: "' . $params['upload_url'] . '",
        image_advtab: true
        });';
    }
  }

  public function renderUploadPhotoResult($params = array()) {
    $params['version'] = $this->_version;
    return $this->view->partial(
                    'tinymce/upload-photo.tpl', 'seaocore', $params);
  }

}