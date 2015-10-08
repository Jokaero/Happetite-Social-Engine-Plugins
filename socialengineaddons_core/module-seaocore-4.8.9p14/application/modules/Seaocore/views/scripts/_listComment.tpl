<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _listComment.tpl 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<br />
<?php $subjectType = $this->subject()->getType();?>
<?php if(isset($this->subject()->listingtype_id)):?>
  <?php $subjectType .= '_' . $this->subject()->listingtype_id;?>
<?php endif;?>
<?php 

if(Engine_Api::_()->seaocore()->checkEnabledNestedComment($subjectType)) :?>
    <?php 
       include_once APPLICATION_PATH . '/application/modules/Nestedcomment/views/scripts/_nestedComment.tpl';
   ?>
 <?php else:?>
     <?php echo $this->action("list", "comment", "seaocore", array("type" =>$this->subject()->getType(), "id" => $this->subject()->getIdentity()));?>
 <?php endif;?>
