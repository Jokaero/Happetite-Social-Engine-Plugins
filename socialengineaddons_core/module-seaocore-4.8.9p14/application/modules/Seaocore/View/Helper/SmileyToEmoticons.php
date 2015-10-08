<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: UserFriendship.php 8835 2011-04-10 05:11:55Z jung $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Seaocore_View_Helper_SmileyToEmoticons extends Zend_View_Helper_Abstract {

  public function smileyToEmoticons($string) {
    $SEA_EMOTIONS_TAG = unserialize(SEA_EMOTIONS_TAG);

    if (!empty($SEA_EMOTIONS_TAG)){


   // $string = htmlspecialchars_decode($string);
    $string = str_replace("&lt;:o)","<:o)",$string);
    $string = str_replace("(&amp;)","(&)",$string);
    $SEA_EMOTIONS_TAG = @array_merge($SEA_EMOTIONS_TAG[0], $SEA_EMOTIONS_TAG[1]);

    $string = strtr($string, $SEA_EMOTIONS_TAG);
    $translate = Zend_Registry::get('Zend_Translate');
    $string = preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "<img class=\"emotions_use\"  src=\"" . $this->view->layout()->staticBaseUrl . "application/modules/Seaocore/externals/emoticons/$1\" border=\"0\" alt=\"$2\" title=\"".$translate->translate("$3")." $2\" />", $string);
}
    return ($this->view->BBCode($string, array('link_no_preparse'=> true)));
  }

}
