<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Form_Admin_Changes_Settings extends Engine_Form {

  public function init() {

//    $this
//        ->setTitle('Lightbox for Photos in Activity Feeds')
//        ->setDescription('Below you have been given 2 ways of enabling the advanced lightbox display for album photos which come in the Activity Feeds. Some mandatory changes need to be done in the activity feed template file which are specified below.<br /> <div class= "tip"> <span> NOTE: Whenever you upgrade SocialEngine Core for your website, these template level changes will be overwritten and you will have to do them again in the respective files as mentioned below.</span> </div>');
//		 
//    $this->addElement('Radio', 'seaocore_lightbox_activityedit', array(
//            'label' => 'Activity Feed Text template file modification',
//            'description' => 'Do you want the Activity Feed Text template file to be overwritten automatically for the minor addition to it, or do you want to make the change to it manually? (This is required to enable Advanced Lightbox Display for the album photos which appear in the Activity Feeds. NOTE:If you have the <a href="http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-photo-albums"  target="_blank">Photo Albums Extension</a> of <a href="http://www.socialengineaddons.com/socialengine-directory-pages-plugin"  target="_blank">Directory / Pages Plugin</a> installed on your website, then these changes will work for the Lightbox Display of Page Album Photos as well.)',
//            'multiOptions' => array(
//                    1 => 'Yes, automatically overwrite the Activity Feed Text template file. (Note: Even after selecting this option and saving this form, if the lightbox display does not come for album photos in activity feeds, then this would be because of inadequate file permissions for automatic overwrite. In that case, please follow the second option here for manual template file changes.)',
//                    0 => 'No, I will manually modify the template file. (Click on this radio button to see the changes that need to be made.)'
//            ),
//            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.lightbox.activityedit', 0),
//            'onclick' => 'show_activitymanual(this.value)',
//    ));
//    $this->getElement('seaocore_lightbox_activityedit')->getDecorator('Description')->setOption('escape', false);
//
//    $this->addElement('Dummy', 'seaocore_show_activitymanual', array(
//            'description' => 'To display the album photos which comes in the activity feed in Advanced Lightbox, follow the steps given below:<br /><br />
//			Step 1: OPEN this file: "/application/modules/Activity/views/scripts/_activityText.tpl".<br /><br />
//			Step 2: FIND the code given below at the start of this file around line no. 13 (approx) :<br /><br />
//			<div class="code">
//				&lt;?php if( empty($this->actions) ) {<br />
//			</div><br />
//
//			Now, INSERT the code given below just ABOVE the code mentioned above :<br /><br />
//
//			<div class="code">
//				&lt;?php // Sitepageplugin ?&gt;<br />
//				&lt;?php $sitepageAlbumEnable = Engine_Api::_()->getDbtable(\'modules\', \'core\')->isModuleEnabled(\'sitepagealbum\'); ?&gt;<br />
//				&lt;?php <br />
//				if ($sitepageAlbumEnable && Engine_Api::_()->sitepage()->canShowPhotoLightBox()):<br />
//					include_once APPLICATION_PATH . \'/application/modules/Sitepagealbum/views/scripts/_lightboxImage.tpl\';<br />
//				endif;<br />
//				?&gt;<br />
//				&lt;?php // Sitealbum plugin ?&gt;<br />
//				&lt;?php $sitealbumEnable = Engine_Api::_()->getDbtable(\'modules\', \'core\')->isModuleEnabled(\'sitealbum\');<br />
//				if ($sitealbumEnable && Engine_Api::_()->sitealbum()->showLightBoxPhoto()):<br />
//					include_once APPLICATION_PATH . \'/application/modules/Sitealbum/views/scripts/_lightboxPhoto.tpl\';<br />
//				endif;<br />
//				?&gt;<br />
//			</div><br />
//
//			Step 3: Now, FIND the code given below around line no. 127 (approx.) :<br /><br />
//			<div class="code">
//				&lt;?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, \'thumb.normal\', $attachment->item->getTitle()), $attribs) ?&gt;<br /><br />
//			</div><br />
//
//			Now, INSERT the below code just ABOVE the code mentioned above :<br /><br />
//	
//			<div class="code">
//				&lt;?php // Sitealbum plugin Start ?&gt;	<br />
//				&lt;?php if($sitealbumEnable && $attachment->item->getType() == "album_photo" && Engine_Api::_()->sitealbum()->showLightBoxPhoto()): <br />
//						$attribs=@array_merge($attribs,	Array(\'onclick\'=> \'openLightBoxAlbum("\'.$attachment->item->getPhotoUrl().\'","\'. Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($attachment->item) .\'");return false;\')); <br />
//					endif; ?&gt; <br />
//				&lt;?php // Sitealbum plugin End ?&gt; <br />
//			</div><br />
//
//			Step 4: Now, FIND the code given below around line no. 149 (approx.) :<br /><br />
//
//			<div class="code">
//				&lt;?php elseif( $attachment->meta->mode == 2 ): // Thumb only type actions ?&gt;<br />
//						&lt;div class="feed_attachment_photo"&gt;<br />
//							&lt;?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, \'thumb.normal\', $attachment->item->getTitle()), array(\'class\' => \'feed_item_thumb\')) ?&gt;<br />
//						&lt;/div&gt;<br />
//			</div><br />
//
//			Now, REPLACE the above code by below block of code :<br /><br />
//
//			<div class="code">
//				&lt;?php elseif( $attachment->meta->mode == 2 ): // Thumb only type actions ?&gt;<br />
//				&lt;div class="feed_attachment_photo"&gt;<br />
//						&lt;?php $attribs = Array(\'class\' => \'feed_item_thumb\'); ?&gt;<br />
//					&lt;?php if($sitepageAlbumEnable && $attachment->item->getType() == "sitepage_photo" && Engine_Api::_()->sitepage()->canShowPhotoLightBox()): <br />
//						$attribs=@array_merge($attribs, array(\'onclick\'=>\'openLightBox("\'.$attachment->item->getPhotoUrl().\'","\'. Engine_Api::_()->sitepage()->getHreflink($attachment->item) .\'");return false;\')); <br />
//						endif; ?&gt;<br />
//					&lt;?php if($sitealbumEnable && $attachment->item->getType() == "album_photo" && Engine_Api::_()->sitealbum()->showLightBoxPhoto()): <br />
//							$attribs=@array_merge($attribs,	Array(\'onclick\'=> \'openLightBoxAlbum("\'.$attachment->item->getPhotoUrl().\'","\'. Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($attachment->item) .\'");return false;\'));
//						endif; ?&gt;<br />
//					&lt;?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, \'thumb.normal\', $attachment->item->getTitle()), $attribs) ?&gt;<br />
//				&lt;/div&gt;	<br />
//				</div><br />',
//    ));
//    $this->getElement('seaocore_show_activitymanual')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));
//
//    // Add submit button
//    $this->addElement('Button', 'submit', array(
//            'label' => 'Save Changes',
//            'type' => 'submit',
//            'ignore' => true
//    ));
  }

  public function changeFile() {
//    $changeFile = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.lightbox.activityedit', 0);
//    $values = $this->getValues();
//    if (array_key_exists('seaocore_show_activitymanual', $values)) {
//      unset($values['seaocore_show_activitymanual']);
//    }
//    if (isset($values['seaocore_lightbox_activityedit']) && !empty($values['seaocore_lightbox_activityedit']) && empty($changeFile)) {
//
//
//      $activityTextPath_Original = APPLICATION_PATH
//          . '/application/modules/Activity/views/scripts/_activityText.tpl';
//      $activityTextPath_New = APPLICATION_PATH
//          . '/application/modules/Seaocore/externals/Activity_activityText/_activityText.tpl';
//      if (is_file($activityTextPath_Original)) {
//        @chmod($activityTextPath_Original, 0777);
//      }
//      if (!@copy($activityTextPath_New, $activityTextPath_Original)) {
//        //Do Nothing.....
//      }
//      @chmod($activityTextPath_Original, 0755);
//    }
//    if (isset($values['submit']))
//      unset($values['submit']);
//    foreach ($values as $key => $value) {
//      Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
//    }
  }

}
?>
