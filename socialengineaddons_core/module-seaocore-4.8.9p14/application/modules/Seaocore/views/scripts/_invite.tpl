<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _invite.tpl 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (0) : ?>
<?php
  //Check which action is currently called:
  $front = Zend_Controller_Front::getInstance();
  $module = $front->getRequest()->getModuleName(); 
  $invite_url = $front->getRequest()->getRequestUri();
  if ($module == 'suggestion') {
    $route = 'friends_suggestions_viewall';
  }
?>

<div class="headline"> 
	<div class="tabs">
	  <ul class="navigation">
	    <li id="<?php echo $module . '_invite_friends';?>">
	        <a class="menu_seaocore_admin_main seaocore_admin_upgrade" href="<?php echo ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->url(array(), $route, true);?>" ><?php echo $this->translate("Invite Friends") ?></a>
	    </li>
	    <li id="<?php echo $module . '_invite_statistics';?>">
	        <a class="menu_seaocore_admin_main seaocore_admin_info" href="<?php echo ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->url(array(), $module .'_invite_statistics', true);?>" ><?php echo $this->translate("Invite Statistics") ?></a>
	    </li>   
	  </ul>
	</div>
</div>	

<?php endif;?>