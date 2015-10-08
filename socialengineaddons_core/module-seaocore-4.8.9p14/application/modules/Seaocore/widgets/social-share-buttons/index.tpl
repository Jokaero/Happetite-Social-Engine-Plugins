<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/core.js');
?>
<div id="seao_social_share_buttons_wrapper" style="">
  <div id="social_share_buttons_container">
    <?php if (Engine_Api::_()->core()->hasSubject() && $this->buttons && in_array('facebook',$this->buttons) && Engine_Api::_()->hasModuleBootstrap('facebookse')): 
              $facebookse = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
              $facebookseversion = $facebookse->version; 
              if ($facebookseversion > '4.6.0p2') :
                  $resourcetype = Engine_Api::_()->core()->getSubject()->getType();
                  $front = Zend_Controller_Front::getInstance();
                  $content_href = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $front->getRequest()->getRequestUri();
                  $content_href = Engine_Api::_()->facebookse()->getSubjectUrl($content_href);
                  if ($resourcetype == 'sitereview_listing')
                      $resourcetype = $resourcetype . '_' . Engine_Api::_()->core()->getSubject()->listingtype_id; 
                  $metainfos = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->getMetainfo('', $resourcetype);
                  if (!empty($metainfos)) :
                     
    ?>               <div class="button facebook_container fb-like" data-href="<?php echo $content_href;?>" data-layout="box_count" data-action="<?php echo $metainfos->like_type;?>" data-show-faces="<?php echo $metainfos->like_faces;?>" data-share="<?php echo $metainfos->send_button;?>">
                      </div>
                      <script type="text/javascript">
                          fblike_moduletype = '<?php echo $resourcetype; ?>';
                          fblike_moduletype_id = '<?php echo Engine_Api::_()->core()->getSubject()->getIdentity(); ?>';
                      </script>
                 <?php endif; ?>
             <?php endif; ?>
    <?php endif; ?>
    <?php if ($this->buttons && in_array('twitter',$this->buttons)): ?>
    <div class="twitter_container">
      <a href="http://twitter.com/share" class="twitter-share-button" data-count="vertical"></a>
    </div>
    <?php endif; ?>
   <?php if ($this->buttons && in_array('linkedin',$this->buttons)): ?>
    <div class="button linkedin_container">      
    </div>
    <?php endif; ?>
    <?php if ($this->buttons && in_array('plusgoogle',$this->buttons)): ?>
    <div class="button google_container">
      <div class="g-plusone" data-size="tall" data-count="true" data-href="<?php echo $this->url() ?>">
      </div>
    </div>
    <?php endif; ?>
    <?php if ( $this->buttons && in_array('share',$this->buttons) && $this->subject()): ?>
      <div class="button siteshare_container">
        <a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'type' => $this->subject()->getType(), 'id' => $this->subject()->getIdentity(),'not_parent_refresh'=>1,'format'=>'smoothbox'), 'default', true); ?>" class="smoothbox seaocore_icon_share buttonlink"><?php echo $this->translate("Share") ?></a>
      </div>
    <?php endif; ?>
  </div>
</div>
<script type="text/javascript">
  en4.core.runonce.add(function() {
    en4.seaocore.setShareButtons($('seao_social_share_buttons_wrapper'), $('social_share_buttons_container'), {
      type: '<?php echo $this->position ?>'
    });
  });
</script>
