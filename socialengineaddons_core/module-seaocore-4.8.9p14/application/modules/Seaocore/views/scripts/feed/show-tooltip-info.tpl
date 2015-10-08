<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: show-tooltip-info.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $coverTop = '0px';
	switch($this->resource_type) {
		case 'sitepage_page':
			$route_name = 'sitepage_general_category';
			$category_id = 'category_id';
		break;
		case 'sitebusiness_business':
			$route_name = 'sitebusiness_general_category';
			$category_id = 'category_id';
		break;
		case 'sitegroup_group':
			$route_name = 'sitegroup_general_category';
			$category_id = 'category_id';
		break;
		case 'sitestore_store':
			$route_name = 'sitestore_general_category';
			$category_id = 'category_id';
		break;
		case 'siteevent_event':
			$route_name = 'siteevent_general_category';
			$category_id = 'category_id';
		break;
		case 'list_listing':
			$route_name = 'list_general_category';
			$category_id = 'category';
		break;
		case 'recipe':
			$route_name = 'recipe_general_category';
			$category_id = 'category_id';
		break;
	  case 'sitestoreproduct_product':
			$route_name = 'sitestoreproduct_general_category';
			$category_id = 'category_id';
		break;
	  case 'sitefaq_faq':
			$route_name = 'sitefaq_general_category';
			$category_id = 'category';
		break;
		case 'sitetutorial_tutorial':
			$route_name = 'sitetutorial_general_category';
			$category_id = 'category';
		break;
	//  case 'album':
	 // case 'sitealbum_album':
	//		$route_name = 'sitealbum_general_category';
	//		$category_id = 'category_id';
		//break;
		case 'sitereview_listing':
			$route_name = 'sitereview_general_category_listtype_' . $this->result->listingtype_id;
			$category_id = 'category_id';
		break;
	}

	$coreSettings = Engine_Api::_()->getApi('settings', 'core');
	$coreModules = Engine_Api::_()->getDbtable('modules', 'core');
	
  $info_values = $coreSettings->getSetting('seaocore.action.link', array("poke" => "poke", "share" => "share", "message" => "message", "addfriend" => "addfriend", "suggestion" => "suggestion", "joinpage" => "joinpage", "requestpage" => "requestpage", "review_wishlist" => "review_wishlist", "joinevent" => "joinevent", "editevent" => "editevent", "inviteevent" => "inviteevent"));

  $informationArray = $coreSettings->getSetting('seaocore.information.link', array( "category" => "category", "like" => "like" , "eventmember" => "eventmember",	"groupmember"	=> "groupmember", "mutualfriend" => "mutualfriend" , "friendcommon" => "friendcommon", "joingroupfriend" =>		"joingroupfriend", "attendingeventfriend" => "attendingeventfriend", "price" => "price", "review_count" => "review_count", "rating_count" => "rating_count", "recommend" => "recommend", "review_helpful" => "review_helpful", "rwcreated_by" => "rwcreated_by", "rewishlist_item" => "rewishlist_item", "location" => "location", "sitecontentcoverphoto_cover" => "Content Cover Photo (For Content Items)", "siteusercoverphoto_cover" => "User Cover Photo" ));
?>	

<?php 
$photoName='';
if( isset($this->result->category_id) && $this->result->category_id) : ?>
	<?php if($this->resource_type == 'siteevent_event') :?>
		<?php	 $category = Engine_Api::_()->getItem('siteevent_category', $this->result->category_id); ?>
		<?php if(isset($category->banner_id) && $category->banner_id)
						$photoName = Engine_Api::_()->storage()->get($category->banner_id, '')->getPhotoUrl(); ?>
	<?php elseif($this->resource_type == 'sitereview_listing'):?>
		<?php	$category = Engine_Api::_()->getItem('sitereview_category', $this->result->category_id); ?>
			<?php 
			if(isset($category->banner_id) && $category->banner_id)
			$photoName = Engine_Api::_()->storage()->get($category->banner_id, '')->getPhotoUrl(); ?>
  <?php endif;?>
<?php endif;?>

<?php $level_id = $this->level_id?>

<?php $setCoverPhoto = 0;?>

<?php if ($this->resource_type != 'user' && !empty($informationArray) && in_array("sitecontentcoverphoto_cover", $informationArray)) : ?>
    <?php if($coreModules->isModuleEnabled('sitecontentcoverphoto')):?>	

			<?php if(isset($this->result->listingtype_id)):?>
				<?php $setCoverPhoto = (bool) Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto')->checkEnableModule(array('resource_type' => $this->result->getType() . '_' .$this->result->listingtype_id )); ?>
			<?php else:?>
				<?php $setCoverPhoto = (bool) Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto')->checkEnableModule(array('resource_type' => $this->result->getType())); ?>
      <?php endif;?>

  <?php endif;?>
<?php elseif ($this->resource_type == 'user' && !empty($informationArray) && in_array("siteusercoverphoto_cover",
                $informationArray)):?>
  <?php if($coreModules->isModuleEnabled('siteusercoverphoto')):?>	
    <?php $setCoverPhoto = 1;?>
  <?php endif;?>           
<?php endif;?>


<div class="info_tip_wrapper" style="top:40%; left:40%;">
  <div class="uiOverlay info_tip" style="width:<?php if($setCoverPhoto):?>351px<?php else: ?>350px<?php endif;?>; top: 0px; ">
    <div class="info_tip_content_wrapper">
      <div class="info_tip_content <?php if($setCoverPhoto):?> info_tip_has_cover <?php endif;?>">
				
	
        <!--START SHOW COVER PHOTO IN INFOTOOLTIP-->
					<?php if(strtolower($this->result->getModuleName()) != 'user' && $setCoverPhoto) :?>
						<?php if($coreModules->isModuleEnabled('sitecontentcoverphoto')):?>	
							<div class="info_tip_cover_photo_wrapper">
								<?php $photo=''; ?>
								<?php $fieldName = strtolower($this->result->getShortType()) . '_cover';?>
								<?php if(strtolower($this->result->getModuleName()) == 'album' || strtolower($this->result->getModuleName()) == 'sitealbum'): ?>
								<?php 
									if ($coreModules->isModuleEnabled('album') && isset($this->result->$fieldName)  && !empty($this->result->$fieldName)) {
										$photo = Engine_Api::_()->getItem("album_photo", $this->result->$fieldName);
									}
								?>
								<?php else : ?>
								<?php 
									if (isset($this->result->$fieldName)  && !empty($this->result->$fieldName)) {
										$photo = Engine_Api::_()->getItem(strtolower($this->result->getModuleName())."_photo", $this->result->$fieldName);
									}
								?>
								<?php endif; ?>

								<?php if ($photo): ?>
                <?php $modulename =strtolower($this->result->getModuleName());
                 if( $modulename != 'album') {
									if($modulename == 'sitealbum') :
										$album = Engine_Api::_()->getItem("album", $photo->album_id);
									else: 
										$album = Engine_Api::_()->getItem(strtolower($this->result->getModuleName()) . "_album", $photo->album_id);
									endif;
									
									} else {
										if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
											$album = Engine_Api::_()->getItem("advalbum_album", $photo->album_id);
										} elseif($coreModules->isModuleEnabled('album')) {
									    $album = Engine_Api::_()->getItem("album", $photo->album_id);
										}
									}
                 
								if ($album && $album->cover_params) {
									if(!is_array($album->cover_params)) {
										$decoded_cover_param = Zend_Json_Decoder::decode($album->cover_params);
										if($decoded_cover_param['top'])
										$coverTop = ($decoded_cover_param['top'] / 2 + 20) . 'px'; 
                    else 
                    $coverTop = ($decoded_cover_param['top'] / 2) . 'px';  
									} else {
										$decoded_cover_param = $album->cover_params;
										if($decoded_cover_param['top'])
										$coverTop = ($decoded_cover_param['top'] / 2 + 20) . 'px'; 
                    else 
                    $coverTop = ($decoded_cover_param['top'] / 2) . 'px'; 
									}
                } ?>
									<div class="info_tip_cover_photo ">
										<a href="<?php echo $photo->getHref(); ?>" onclick='openSeaocoreLightBox("<?php echo $photo->getHref(); ?>");return false;'>
											<?php echo $this->itemPhoto($photo, 'thumb.cover', '', array('align' => 'left', 'class' => 'cover_photo', 'style'=> "top:$coverTop;")); ?>
										</a>
                    <div class="info_tip_cover_photo_cover"></div>
									</div>
		
								<?php elseif (Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($this->result, strtolower($this->result->getModuleName()), $this->result->getOwner()->level_id, 0)): ?>
                 <?php
                      $postionParams = Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0));
											$decoded_cover_param = Zend_Json_Decoder::decode(Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsParams($this->result, strtolower($this->result->getModuleName()), $this->result->getOwner()->level_id, $postionParams));
											if($decoded_cover_param['top'])
										$coverTop = ($decoded_cover_param['top'] / 2 + 20) . 'px'; 
                    else 
                    $coverTop = ($decoded_cover_param['top'] / 2) . 'px';  ?>
                
									<div class="info_tip_cover_photo">
										<img src="<?php echo Engine_Api::_()->storage()->get(Engine_Api::_()->sitecontentcoverphoto()->getSiteContentDefaultSettingsIds($this->result, strtolower($this->result->getModuleName()), $this->result->getOwner()->level_id, 0), 'thumb.cover')->map() ?>" align="left" class="cover_photo" style="top:<?php echo $coverTop;?>;" />
                        />
                    <div class="info_tip_cover_photo_cover"></div>
									</div>
								<?php elseif ($photoName): ?>
									<div class="info_tip_cover_photo">
										<img src="<?php echo $photoName;?>" align="left" class="cover_photo" style="top:0px" />
                    <div class="info_tip_cover_photo_cover"></div>
									</div>
								<?php else: ?>
									<div class="info_tip_cover_photo_empty"></div>
								<?php endif; ?>
							</div>
						<?php endif;?>
					<?php elseif(strtolower($this->result->getModuleName()) == 'user' && $setCoverPhoto):?>
						<?php if($coreModules->isModuleEnabled('siteusercoverphoto')):?>	
							<div class="info_tip_cover_photo_wrapper">
								<?php $photo='';$level_id=$this->result->level_id;?>
								<?php 
									$user = Engine_Api::_()->getItem('user', $this->result->getIdentity());
									if(isset($user->user_cover) && $user->user_cover) {
										if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
											$photo = Engine_Api::_()->getItem('advalbum_photo', $user->user_cover);
										} else {
											$photo = Engine_Api::_()->getItem('album_photo', $user->user_cover);
										}
									}
								?>

								<?php if ($photo): ?>
 <?php           if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
										$album = Engine_Api::_()->getItem("advalbum_album", $photo->album_id);
                 } else {
										$album = Engine_Api::_()->getItem("album", $photo->album_id);
                 }	
								if ($album && $album->cover_params) {
									if(!is_array($album->cover_params)) {
										$decoded_cover_param = Zend_Json_Decoder::decode($album->cover_params);
										if($decoded_cover_param['top'])
										$coverTop = ($decoded_cover_param['top'] / 2 + 20) . 'px'; 
                    else 
                    $coverTop = ($decoded_cover_param['top'] / 2) . 'px';  
									} else {
										$decoded_cover_param = $album->cover_params;
										if($decoded_cover_param['top'])
										$coverTop = ($decoded_cover_param['top'] / 2 + 20) . 'px'; 
                    else 
                    $coverTop = ($decoded_cover_param['top'] / 2) . 'px'; 
									}
								}

 ?>
									<div class="info_tip_cover_photo">
										<a href="<?php echo $photo->getHref(); ?>" onclick='openSeaocoreLightBox("<?php echo $photo->getHref(); ?>");return false;'>
											<?php echo $this->itemPhoto($photo, 'thumb.cover', '', array('align' => 'left', 'class' => 'cover_photo', 'style'=> "top:$coverTop;")); ?>
										</a>
									</div>
								<?php elseif (Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.id")): ?>
								<?php
										$postionParams = Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0));
										$decoded_cover_param = Zend_Json_Decoder::decode(Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.params", $postionParams));
                    if($decoded_cover_param['top'])
										$coverTop = ($decoded_cover_param['top'] / 2 + 20) . 'px'; 
                    else 
                    $coverTop = ($decoded_cover_param['top'] / 2) . 'px'; 
									?>
									<div class="info_tip_cover_photo">
										<img src="<?php echo Engine_Api::_()->storage()->get(Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.id"), 'thumb.cover')->map() ?>" align="left" class="cover_photo"  style="top:<?php echo $coverTop;?>;"  />
									</div>
								<?php else: ?>
									<div class="info_tip_cover_photo_empty"></div>
								<?php endif; ?>
							</div>
						<?php endif;?>
					<?php endif;?>
				<!--END SHOW COVER PHOTO IN INFOTOOLTIP-->
        
        
				<div class="tip_main_photo">
					<?php if ($coreModules->isModuleEnabled('sitemember') && !empty($informationArray) && in_array("featured", $informationArray) && $this->featured):  ?>
						<i class="sitemember_list_featured_label"></i>
					<?php endif; ?>
					
					<?php if ($this->resource_type == 'user') : ?>
						<?php  echo $this->htmlLink($this->result->getHref(), $this->itemPhoto($this->result, 'thumb.profile')); ?>
					<?php elseif ($this->resource_type == 'blog' || $this->resource_type == 'forum_topic' ||
            $this->resource_type == 'poll' || $this->resource_type == 'feedback' || $this->resource_type == 'sitefaq_faq' || $this->resource_type == 'sitereview_wishlist' || $this->resource_type == 'sitestore_wishlist'  || $this->resource_type == 'sitereview_review' || $this->resource_type == 'sitestoreproduct_review' || $this->resource_type == 'sitestoreproduct_wishlist'  || $this->resource_type == 'sitereview_topic' || $this->resource_type == 'sitemember_review'):   ?>
						<?php echo $this->htmlLink($this->result->getHref(), $this->itemPhoto($this->result->getOwner(), 'thumb.profile')); ?>
					<?php elseif($this->resource_type == 'document' || $this->resource_type == 'groupdocument_document' || $this->resource_type == 'eventdocument_document' || $this->resource_type == 'sitepagedocument_document' || $this->resource_type == 'sitebusinessdocument_document'): ?>
						<?php if(!empty($this->result->photo_id)): ?>
							<?php echo $this->htmlLink($this->result->getHref(), $this->itemPhoto($this->result, 'thumb.icon'), array()) ?>
						<?php elseif ($this->resource_type == 'document'): ?>
							<?php echo $this->htmlLink($this->result->getHref(), '<img src="'. $this->result->thumbnail .'" class="thumb_icon" />', array() ) ?>
						<?php elseif ($this->resource_type == 'groupdocument_document' || $this->resource_type == 'eventdocument_document' || $this->resource_type == 'sitebusinessdocument_document' || $this->resource_type == 'sitepagedocument_document'): ?>
							<?php echo $this->htmlLink($this->result->getHref(), '<img src="'. $this->result->thumbnail .'" class="thumb_icon" />', array() ) ?>
						<?php endif; ?>
					<?php elseif(empty($this->result->photo_id) &&  ($this->resource_type == 'music_playlist')):?>
						<?php echo $this->htmlLink($this->result->getHref(), $this->itemPhoto($this->result->getOwner(), 'thumb.profile')); ?>
					<?php else: ?>
						<?php echo $this->htmlLink($this->result->getHref(), $this->itemPhoto($this->result, 'thumb.normal')); ?>
					<?php endif; ?>
					
					<?php if ($coreModules->isModuleEnabled('sitemember') && !empty($informationArray) && in_array("sponsored", $informationArray) && !empty($this->sponsored)): ?>
						<div class="sitemember_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.sponsoredcolor', '#FC0505'); ?>">
							<?php echo $this->translate('SPONSORED'); ?>
						</div>
					<?php endif; ?>
				</div>
				
				<?php 
        $verify_count = $verify_limit = null;
        if ($this->resource_type == 'user' && $coreModules->isModuleEnabled('siteverify')):
          $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($this->result->user_id);
          $verify_limit = Engine_Api::_()->authorization()->getPermission($this->result->level_id, 'siteverify_verify', 'verify_limit');
        endif;
        ?>
		    <div class="tip_main_body">
          <div class="tip_main_body_head_wrap">
            <div class="tip_main_body_head">
              <div class="tip_main_body_title fleft">
                <?php echo $this->htmlLink($this->result->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($this->result->getTitle(), 64) , array( 'class' => 'fleft', )) ?>
                <?php if ($this->resource_type == 'user' && $coreModules->isModuleEnabled('siteverify') && !empty($informationArray) && in_array("verify", $informationArray) && ($verify_count >= $verify_limit)): ?>
                  <i class="sitemember_list_verify_label mleft5"></i>
                <?php endif; ?>
              </div>
              <?php if ($this->resource_type == 'user' && $coreModules->isModuleEnabled('siteverify')): ?>
                    <?php 	if (!empty($informationArray) && in_array("verify", $informationArray) && ($verify_count >= $verify_limit)): ?>               
                        <span class="siteverify_tip_wrapper">
                            <i class="sitemember_list_verify_label mleft5"></i>
                            <span class="siteverify_tip"><?php echo $this->translate('Verified'); ?><i></i></span>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>

              <?php if ($this->resource_type != 'user' && !empty($informationArray) && in_array("category", $informationArray)) : ?>

                <?php 
                      $getShortType = $this->result->getShortType();
                      if($getShortType == 'playlist') {
                        $getShortType = 'Music';
                      } elseif($getShortType == 'topic') {
                        $getShortType = 'Forum Topic';
                      } elseif($getShortType == 'business') {
                        $getShortType = $this->translate(' Business ');
                      } elseif($getShortType == 'group') {
                        $getShortType = $this->translate(' Group ');
                      }
                      elseif($getShortType == 'page') {
                        $getShortType = $this->translate(' Page ');
                      } elseif($getShortType == 'store') {
                        $getShortType = $this->translate(' Store ');
                      } elseif($getShortType == 'event') {
                        $getShortType = $this->translate(' Event ');
                      }
                      else {
                      $getShortTypeArray=explode('_',$this->result->getShortType());
                      foreach ($getShortTypeArray as  $k=>$str)
                        $getShortTypeArray[$k]=ucfirst($str);
                      $getShortType=implode(' ',$getShortTypeArray);
                      }
                      if (empty($this->getCategoryText)) { ?>
                    <div class="tip_main_body_stat">
                        <?php echo $this->translate($getShortType); ?> &#160;
                    </div>
                <?php } else { ?>
                  <div class="tip_main_body_stat">
                  <?php echo $this->translate($getShortType); ?>
                  &#187;
                  <?php if (!empty($route_name)) :?>
										<?php echo $this->htmlLink($this->url(array("$category_id" => $this->result->category_id, 'categoryname' => Engine_Api::_()->seaocore()->getSlug($this->getCategoryText, 225)), $route_name), $this->translate($this->getCategoryText)) ?>
										<?php //echo $this->getCategoryText; ?> &#160;
                  <?php else: ?>
										<?php echo $this->getCategoryText; ?> &#160;
                  <?php endif; ?>
                  </div>
                <?php } ?>
                  <?php endif; ?>
            </div>
          </div>
            <?php if ($this->resource_type == 'user' && !empty($informationArray) && in_array("profile_field", $informationArray) && $coreModules->isModuleEnabled('sitemember')): ?>
								<?php
								  $customfieldHeading = $coreSettings->getSetting('seaocore.customfieldheading', 0);
									$customParams = $coreSettings->getSetting('seaocore.customParams', 5);
									$customfieldtitle = $coreSettings->getSetting('seaocore.customfieldtitle', 0);
								  $user_subject = Engine_Api::_()->user()->getUser($this->resource_id);
									$fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($user_subject);
									$userProfileFields = $this->userFieldValueLoop($user_subject, $fieldStructure, array('customParams' => $customParams, 'custom_field_title' => $customfieldtitle, 'custom_field_heading' => $customfieldHeading));
									if (!empty($userProfileFields)) {
										echo '<div class="sitemember_listings_stats"><i class=""></i><div class="o_hidden f_small">' . $userProfileFields . '</div></div>';
									}
								?>
							<?php endif; ?>
                
							<?php  //here we show date of event content.
							if (!empty($this->result->starttime) && $this->resource_type == 'event') : ?>
								<div class="tip_main_body_stat">
									<?php  echo $this->translate('Date: '); ?>
									<?php echo $this->locale()->toDateTime($this->result->starttime);  ?>
								</div>
							<?php endif; ?>

              <?php  //here we show date of event content.
							if (!empty($this->result->starttime) && $this->resource_type == 'siteevent_event') : ?>
            <div class="tip_main_body_stat" title="<?php echo $this->locale()->toEventDateTime($this->result->starttime, array('size' => 'full')) ?>">
                   <?php $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium'); ?>
									<?php  echo $this->translate('Date: '); ?>
									<?php echo $this->locale()->toEventDateTime($this->result->starttime, array('size' => $datetimeFormat));  ?>
								</div>
							<?php endif; ?>
							
							<?php if($this->resource_type == "sitereview_wishlist") : ?>
								<div class="tip_main_body_stat">
								  <?php if (!empty($informationArray) && in_array("rwcreated_by", $informationArray)) : ?>
										<?php echo $this->translate('Created by %s', $this->result->getOwner()->toString()) ?><br />
									<?php endif; ?>
									<?php if (!empty($informationArray) && in_array("rewishlist_item", $informationArray)) : ?>
									<?php echo $this->translate(array('%s entry', '%s entries', count($this->result)), count($this->result)); ?>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						
							<?php if ($this->resource_type == 'sitereview_listing' && $this->result->price > '0.00' && !empty($informationArray) && in_array("price", $informationArray)) : ?>
								<div class="tip_main_body_stat">
									<?php  echo $this->translate('Price: '); ?>
									<span class="discount_value"><?php echo $this->locale()->toCurrency($this->result->price, $coreSettings->getSetting('payment.currency', 'USD')) ?></span>
								</div>
							<?php elseif($this->resource_type == 'sitestoreproduct_product' && $this->result->price > '0.00' && !empty($informationArray) && in_array("price", $informationArray)): ?>
									<div class="tip_main_body_stat">
										<?php echo Engine_Api::_()->sitestoreproduct()->getProductDiscount($this->result); ?>
									</div>
							<?php endif; ?>
							
							<?php if (isset($this->result->photos_count) && ($this->resource_type == 'album' || $this->resource_type == 'sitealbum_album')): ?>
								<div class="tip_main_body_stat">
									<?php echo $this->translate(array('%s photo', '%s photos', $this->result->photos_count), $this->result->photos_count) ?>&nbsp;&nbsp;
								</div>
							<?php endif; ?>

              <?php if ($this->resource_type == 'sitereview_listing'): ?>
							  <div class="tip_main_body_stat">
							  	<?php if (!empty($this->result->review_count) && !empty($informationArray) && in_array("review_count", $informationArray)) : ?>
										<?php echo $this->translate(array('%s review', '%s reviews', $this->result->review_count), $this->result->review_count) ?>&nbsp;&nbsp;
									<?php endif; ?>
									<?php if (!empty($this->result->rating_avg) && !empty($informationArray) && in_array("rating_count", $informationArray)) : ?>
											<?php echo $this->translate(array('%s rating', '%s ratings', $this->result->rating_avg), $this->result->rating_avg) ?>
									<?php endif; ?>
								</div>
              <?php endif; ?>
              
              <?php if ($this->resource_type == 'sitestoreproduct_product') : ?>
							  <div class="tip_main_body_stat">
							  	<?php if (!empty($this->result->review_count) && !empty($informationArray) && in_array("storeproductreview_count", $informationArray)) : ?>
										<?php echo $this->translate(array('%s review', '%s reviews', $this->result->review_count), $this->result->review_count) ?>&nbsp;&nbsp;
									<?php endif; ?>
									<?php if (!empty($this->result->rating_avg) && !empty($informationArray) && in_array("storeproductrating_count", $informationArray)) : ?>
											<?php echo $this->translate(array('%s rating', '%s ratings', $this->result->rating_avg), $this->result->rating_avg) ?>
									<?php endif; ?>
								</div>
              <?php endif; ?>

              <?php if ($this->resource_type == 'sitestoreproduct_review' && !empty($informationArray) && in_array("storeproductrecommend", $informationArray)) : ?>
								<div class="tip_main_body_stat">
									<?php echo $this->translate('Recommended: '); ?>
									<?php if ($this->result->recommend == '1') : ?>
										<?php echo $this->translate('Yes'); ?>
									<?php else: ?>
										<?php echo $this->translate('No'); ?>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<?php if ($this->resource_type == 'sitestoreproduct_review' && !empty($informationArray) && in_array("storeproductreview_helpful", $informationArray)) : ?>
								<?php $review = $this->result; ?>
								<div class="tip_main_body_stat">
							  		<?php $helpfulTable = Engine_Api::_()->getDbtable('helpful', 'sitestoreproduct'); ?>
									<span><?php echo $this->translate("Helpful: "); ?></span> 
									<?php $this->countHelpfulReviews = $helpfulTable->getCountHelpful($review->review_id, 1); ?>
									<span class="thumb-up"></span>
									<?php echo $this->countHelpfulReviews ?><?php echo $this->translate(" Yes,"); ?>
									<?php $this->countHelpfulReviews = $helpfulTable->getCountHelpful($review->review_id, 2); ?>
									<span class="thumb-down"></span>
									<?php echo $this->countHelpfulReviews; ?><?php echo $this->translate(" No"); ?>
								</div>
              <?php endif; ?>

              <?php if (($this->resource_type == 'sitereview_review' || $this->resource_type == 'sitestore_review') && !empty($informationArray) && in_array("recommend", $informationArray)) : ?>
								<div class="tip_main_body_stat">
									<?php echo $this->translate('Recommended: '); ?>
									<?php if ($this->result->recommend == '1') : ?>
										<?php echo $this->translate('Yes'); ?>
									<?php else: ?>
										<?php echo $this->translate('No'); ?>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							
							<?php if (($this->resource_type == 'sitereview_review' || $this->resource_type == 'sitestore_review') && !empty($informationArray) && in_array("review_helpful", $informationArray)) : ?>
								<?php $review = $this->result; ?>
								<div class="tip_main_body_stat">
								  <?php $helpfulTable = Engine_Api::_()->getDbtable('helpful', 'sitereview'); ?>
									<span><?php echo $this->translate("Helpful: "); ?></span> 
									<?php $this->countHelpfulReviews = $helpfulTable->getCountHelpful($review->review_id, 1); ?>
									<span class="thumb-up"></span>
									<?php echo $this->countHelpfulReviews ?><?php echo $this->translate(" Yes,"); ?>
									<?php $this->countHelpfulReviews = $helpfulTable->getCountHelpful($review->review_id, 2); ?>
									<span class="thumb-down"></span>
									<?php echo $this->countHelpfulReviews; ?><?php echo $this->translate(" No"); ?>
								</div>
              <?php endif; ?>
              
							<?php if ($this->resource_type == "sitepage_page" || $this->resource_type == "sitebusiness_business") : ?>
								<div class="tip_main_body_stat">
									<?php if (!empty($informationArray) && in_array("phone", $informationArray) && !empty($this->result->phone)) : ?>
									<?php  echo  $this->translate("Phone: ") ?><?php echo $this->result->phone; ?><br />
									<?php endif; ?>
									<?php if (!empty($informationArray) && in_array("email", $informationArray) && !empty($this->result->email)) : ?>
									<?php  echo  $this->translate("Email: ") ?><?php echo $this->result->email; ?><br />
									<?php endif; ?>
									<?php if (!empty($informationArray) && in_array("website", $informationArray) && !empty($this->result->website)) : ?>
									<?php  echo  $this->translate("Website: ") ?><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($this->result->website, 30); ?>
									<?php endif; ?>
								</div>
              <?php endif; ?>
              
              <?php if (!empty($informationArray) && in_array("location", $informationArray)) : ?>
								<?php if (!empty($this->result->location)) : ?>
									<div class="tip_main_body_stat clr">
										
										<?php if (($this->resource_type == 'event' || $this->resource_type == 'video'  || $this->resource_type == 'group' || $this->resource_type == 'user') && $coreModules->isModuleEnabled('sitetagcheckin')): ?>
                        <?php if($this->resource_type == 'user' && !$this->isHidden) :?>
                            <?php  echo $this->translate('Location: ');  ?>
                            <?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->result->seao_locationid, 'resouce_type' => 'seaocore'), $this->result->location, array('onclick' => 'owner(this);return false')) ; ?>
                        <?php endif;?>
									  	<?php elseif (isset($this->result->seao_locationid) && !$coreModules->isModuleEnabled('sitetagcheckin')): ?>
											<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->result->seao_locationid, 'resouce_type' => 'seaocore'), $this->result->location, array('onclick' => 'owner(this);return false')) ; ?>
										<?php else: ?>
											<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->resource_id, 'resouce_type' => $this->resource_type), $this->result->location, array('onclick' => 'owner(this);return false')) ; ?>
										<?php endif; ?>
										<?php //echo $this->result->location;  ?>
									</div>
								<?php endif; ?>
								
								<?php if ($this->resource_type == 'classified'): ?>
									<?php if(!empty($this->locationItem)) : ?>
										<div class="tip_main_body_stat">
											<?php  echo $this->translate('Location: '); ?>
											<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->resource_id, 'resouce_type' => 'classified'), $this->locationItem, array('onclick' => 'owner(this);return false')) ; ?>
											<?php //echo $this->locationItem;  ?>
										</div>
									<?php endif; ?>
								<?php endif; ?>
						  <?php endif; ?>

	          <?php //FOR GROUP SHOW MEMBER.
            if ($this->resource_type == 'group' && !empty($informationArray) && in_array("groupmember",$informationArray)) { ?>
           	<div class="tip_main_body_stat">
              <?php echo $this->translate(array('%s member', '%s members', $this->result->member_count), $this->result->member_count) ?>
            </div>
            <?php if (!empty($informationArray) && in_array("joingroupfriend", $informationArray)) : ?>
            <div class="tip_main_body_stat" style="margin-top:7px;">
              <?php if (!empty($this->friendLikeCount))  { ?>
								<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' =>'seaocore', 'controller' => 'feed', 'action'=>'common-member-list', 'resouce_type' => $this->resource_type, 'resource_id' => $this->resource_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><?php echo $this->translate(array('%s friend is member','%s friends are members', $this->friendLikeCount),$this->friendLikeCount);?> </a>
        <!--      echo $this->translate(array('%s friend joined', '%s friends joined',
                 $this->friendLikeCount),$this->friendLikeCount);-->
                <?php } ?>
            </div>
            <?php endif; ?>
            <?php } elseif (($this->resource_type == 'event' || $this->resource_type == 'siteevent_event')) { ?>
             <?php $showMemberCount= true; ?>
                
            <?php if ($this->resource_type == 'siteevent_event') {
                $siteeventVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('siteevent')->version;
                if($siteeventVersion >= '4.8.8p1' && Engine_Api::_()->siteevent()->isTicketBasedEvent()) {
                    $showMemberCount= false;
                }
            } ?>                
                
             <?php if($showMemberCount && !empty($informationArray) && in_array("eventmember", $informationArray)):?> 
             <div class="tip_main_body_stat">
                <?php echo $this->translate(array('%s member', '%s members', $this->result->member_count), $this->result->member_count) ?>
              </div>
           <?php endif; ?>
              <?php if ($showMemberCount && !empty($informationArray) && in_array("attendingeventfriend", $informationArray)) : ?>
              <div class="tip_main_body_stat" style="margin-top:7px;">
                <?php if (!empty($this->friendLikeCount))  { ?>
                <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' =>'seaocore', 'controller' => 'feed', 'action'=>'common-member-list', 'resouce_type' => $this->resource_type, 'resource_id' => $this->resource_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><?php echo $this->translate(array('%s friend is attending','%s friends are attending', $this->friendLikeCount),$this->friendLikeCount);?> </a>
              <!--  echo $this->translate(array('%s friend attending' , '%s friend attending',
                 $this->friendLikeCount), $this->friendLikeCount);-->
                 <?php } ?>
              </div>
              <?php endif; ?>          
            
              <?php } elseif ($this->resource_type == 'user') { ?>
              
							<?php if ($coreModules->isModuleEnabled('siteverify')): ?>
							<?php $verifyCount = Engine_Api::_()->getDbTable('verifies', 'siteverify')->getVerifyCount($this->result->user_id, 'user'); ?>
								<?php if (!empty($verifyCount) && !empty($informationArray) && in_array("verify_count", $informationArray)): ?>
									<div class="tip_main_body_stat" style="display:inline-block;">
										<?php	echo $this->translate(array('%s verified', '%s verified', $verifyCount), $verifyCount) ?>
									</div>
								<?php endif; ?>
              <?php endif; ?>
							
              <?php if (!empty($this->likeCount) && !empty($informationArray) && in_array("like", $informationArray)): ?>
								<div class="tip_main_body_stat"  style="display:inline-block;">
                <?php if(!empty($verifyCount) && !empty($this->likeCount)): ?>
								&nbsp;|&nbsp;
                <?php endif; ?>
                  <?php	echo $this->translate(array('%s likes this', '%s like this', $this->likeCount), $this->likeCount) ?>&#160;
								</div>
              <?php endif; ?>

              <?php if (!empty($this->muctualfriendLikeCount) && ($this->resource_id != $this->viewer_id) && !empty($informationArray) && in_array("mutualfriend", $informationArray)) { ?>
                <div class="tip_main_body_stat" style="margin-top:7px;">
									<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'feed','action'=>'more-mutual-friend', 'id' =>	$this->resource_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><?php	echo $this->translate(array('%s mutual friend','%s mutual friends', $this->muctualfriendLikeCount), $this->muctualfriendLikeCount);?>		</a>
                </div>
              <?php  } ?>
             <?php } else  { ?>
             
              <div class="tip_main_body_stat">
                <?php if (!empty($this->likeCount) && !empty($informationArray) && in_array("like",$informationArray))
                echo $this->translate(array('%s likes this', '%s like this', $this->likeCount), $this->likeCount) ?>
              </div>
              <div class="tip_main_body_stat" style="margin-top:7px;">
                <?php
                if (!empty($this->friendLikeCount) && !empty($informationArray) && in_array("friendcommon",
                 $informationArray) ) { ?>
										<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'feed', 'action'=>'common-member-list', 'resouce_type'	=> $this->resource_type, 'resource_id' => $this->resource_id, 'format' => 'smoothbox'), 'default'	, true)); ?>'); return false;" ><?php echo $this->translate(array('%s friend likes this','%s friends like this', $this->friendLikeCount),$this->friendLikeCount);?>
	                  </a>
                <!--echo $this->translate(array('%s friend likes this', '%s friends like this',
$this->friendLikeCount), $this->friendLikeCount);-->
               <?php } ?>
              </div>
            <?php  } ?>
            <?php if($this->resource_type == 'user' && ($this->resource_id != $this->viewer_id) &&
!empty($informationArray) && in_array("mutualfriend", $informationArray) ) { ?>
            	<?php  if (!empty($this->muctualfriendLikeCount)): ?>
		            <?php
		              $container = 1;
		              foreach( $this->muctualFriend as $friendInfo ) { 
		            ?>
            	<div class="info_tip_member_thumb info_show_tooltip_wrapper">
	              <?php
	                  $user_subject = Engine_Api::_()->user()->getUser($friendInfo['user_id']);
	                  $profile_url = $this->url(array('id' => $friendInfo['user_id']), 'user_profile');
	              ?>
	              <div class="info_show_tooltip">
	              	<?php echo $this->user($user_subject)->getTitle() ?>
	              	<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" alt="" />
	              </div>
                <a href="<?php echo $profile_url ?>" target="_parent">
                	<?php echo $this->itemPhoto($this->user($user_subject),   'thumb.icon') ?>
                </a>
              </div>
              <?php if($container == 5): break; endif; ?>
                <?php $container++ ; } ?>
              <?php endif; ?>
            <?php } elseif($this->resource_type != 'user'  && !empty($informationArray) &&
in_array("friendcommon", $informationArray)){ ?>
	            <?php  if (!empty($this->friendLikeCount)): ?>
		            <?php
		              $container = 1;
		              foreach( $this->activity_result as $friendInfo ) {
		              ?>
              <div class="info_tip_member_thumb info_show_tooltip_wrapper">
                <?php
                  if ($this->resource_type == 'group' || $this->resource_type == 'event' || $this->resource_type == 'siteevent_event') {
                    $user_subject = Engine_Api::_()->user()->getUser($friendInfo->user_id);
                    $profile_url = $this->url(array('id' => $friendInfo->user_id), 'user_profile');
                  } else {
                    $user_subject = Engine_Api::_()->user()->getUser($friendInfo->poster_id);
                    $profile_url = $this->url(array('id' => $friendInfo->poster_id), 'user_profile');
                  }
                ?>
	              <div class="info_show_tooltip">
	              	<?php echo $this->user($user_subject)->getTitle() ?>
	              	<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" alt="" />
	              </div>
                <a href="<?php echo $profile_url ?>" target="_parent">
                	<?php echo $this->itemPhoto($this->user($user_subject),   'thumb.icon') ?>
                </a>
              </div>
              <?php if($container == 5): break; endif; ?>
              <?php $container++ ; } ?>
              <?php endif; ?>
            <?php } ?>
    		</div>
			</div>
       <?php $flag = false; ?>
			  <?php if ($this->resource_type == 'user') { ?>
                <?php  //POKE WORK
								$user_subject = Engine_Api::_()->user()->getUser($this->resource_id);

                if (!empty($this->pokeEnabled) && (!empty($this->getpokeFriend)) && ($this->resource_id !=
                   $this->viewer_id) && !empty($info_values) && in_array("poke", $info_values) && (!$this->viewer->isBlockedBy($user_subject) || $this->viewer->isAdmin())) { ?>
                  <?php if (!$flag) : ?>
                    <div class="info_tip_content_bottom">
                  <?php $flag = true; endif; ?>
                  <a class="seaocore_poke" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('route' => 'poke_general', 'module' => 'poke', 'controller' => 'pokeusers',
                  'action'=>'pokeuser', 'pokeuser_id' =>  $this->resource_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Poke/externals/images/poke_icon.png);"><?php echo $this->translate("Poke") ?></a>
                <?php } //END POKE WORK. ?>

                <?php //FOR SUGGESTION LINK SHOW IF SUGGESTION PLUGIN INSTALL AT HIS SITE. ?>
                <?php 
                if (!empty($this->suggestionEnabled) && !empty($this->getMemberFriend) &&
                 (!empty($this->suggestion_frienf_link_show)) && !empty($info_values) &&
                  in_array("suggestion", $info_values) && (!empty($this->viewer_id))) {
                ?>
                  <?php if (!$flag) : ?>
                    <div class="info_tip_content_bottom">
                  <?php $flag = true; endif; ?>
                  <a class="seaocore_suggest" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module'=> 'suggestion', 'controller' => 'index', 'action' =>
                  'switch-popup','modName' => $this->moduleNmae, 'modContentId' => $this->resource_id, 'modError' => 1, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/sugg_blub.png);"><?php echo $this->translate("Suggest to Friends") ?></a>
                <?php } //END SUGGESTION WORK. ?>

                <?php //FOR MESSAGE LINK
                $item = Engine_Api::_()->getItem('user', $this->resource_id);
                if (!empty($info_values) && in_array("message",
                 $info_values) && (Engine_Api::_()->seaocore()->canSendUserMessage($item)) &&
(!empty($this->viewer_id))) {
                ?>
                  <?php if (!$flag) : ?>
                    <div class="info_tip_content_bottom">
                  <?php $flag = true; endif; ?>
                  <a class="seaocore_message" href="<?php echo $this->base_url; ?>/messages/compose/to/<?php echo $this->resource_id ?>" style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Messages/externals/images/send.png);">
                  <?php echo $this->translate('Message'); ?> </a>
                <?php } ?>
                <?php  if( !empty($info_values) && in_array("addfriend", $info_values) &&
(!empty($this->viewer_id)) ): ?>
                <?php $uaseFRIENFLINK = $this->userFriendshipAjax($this->user($this->resource_id)); ?>
                <?php if (!$flag && !empty($uaseFRIENFLINK)) : ?>
                    <div class="info_tip_content_bottom">
                    <!--<div id="addfriend">-->
                  <?php $flag = true; endif; ?>
                  <?php //VIEWER IS VIEW PROFILE OF ANOTHER USER AND NOT A FRIEND THEN ADD FRIEND
                   //LINK IS SHOW. ?>
                  <?php echo $uaseFRIENFLINK; ?>
                <?php endif; ?>
                <?php //VIEWER IS VIEW PROFILE OF ANOTHER USER AND NOT A FRIEND THEN ADD FRIEND LINK IS SHOW.?>
        <?php }	?>
        
				<?php if (!empty($this->suggestionEnabled) && ($this->resource_type != 'user') &&
				(!empty($this->suggestion_frienf_link_show)) && !empty($info_values) && in_array("suggestion", $info_values) && (!empty($this->viewer_id))) : ?>
					<?php if (!$flag) : ?>
						<div class="info_tip_content_bottom">
						<?php $flag = true; ?> 
					<?php endif; ?>
					<a class="seaocore_suggest" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module'=> 'suggestion', 'controller' => 'index', 'action' =>
					'switch-popup','modName' =>$this->moduleNmae, 'modContentId' => $this->resource_id, 'modError' => 1, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/sugg_blub.png);"><?php echo $this->translate("Suggest to Friends") ?></a>
				<?php endif; ?>

        <?php if (!empty($this->viewer_id) && (!empty($info_values) && in_array("review_wishlist", $info_values)) && !empty($this->listingtypeArray->wishlist) && $this->resource_type == 'sitereview_listing')  : ?>
					<?php if (!$flag) : ?>
						<div class="info_tip_content_bottom">
						<?php $flag = true; ?>
					<?php endif; ?>
					<a class="seaocore_wishist" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('route' => 'sitereview_wishlist_general','module' => 'sitereview','controller' => 'wishlist', 'action' => 'add', 'listing_id' => $this->resource_id), 'default' , true)); ?>'); return false;" style="background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitereview/externals/images/icons/wishlist_add.png);"><?php echo $this->translate('Add to Wishlist');?></a>
				<?php elseif (!empty($this->viewer_id) && (!empty($info_values) && in_array("storeproductreview_wishlist", $info_values)) && $this->resource_type == 'sitestoreproduct_product')  : ?>
					<?php if (!$flag) : ?>
						<div class="info_tip_content_bottom">
						<?php $flag = true; ?>
					<?php endif; ?>
					<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('action' => 'add', 'product_id' => $this->resource_id), 'sitestoreproduct_wishlist_general' , true)); ?>'); return false;" style="background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/icons/wishlist.png);"><?php echo $this->translate('Add to Wishlist');?></a>
				<?php endif; ?>

				<?php if ((!empty($info_values) && in_array("joinpage", $info_values)) && !empty($this->joinFlag) && $this->resource_type == 'sitepage_page' && !empty($this->member_approval))  : ?>
					<?php if (!$flag) : ?>
						<div class="info_tip_content_bottom">
						<?php $flag = true; ?>
					<?php endif; ?>
					<a class="seaocore_joinpage" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('route' => 'sitepage_profilepagemember', 'module' => 'sitepagemember', 'controller' => 'member', 'action' => 'join', "page_id" => $this->resource_id), 'default' , true)); ?>'); return false;" style="background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagemember/externals/images/member/join.png);"><?php echo $this->translate("Join Page");?></a>
				<?php endif; ?>
				
        <?php if ((!empty($info_values) && in_array("requestpage", $info_values)) && !empty($this->requestFlag) && $this->resource_type == 'sitepage_page' && empty($this->member_approval))  : ?>
					<?php if (!$flag) : ?>
						<div class="info_tip_content_bottom">
						<?php $flag = true; ?>
					<?php endif; ?>
					<a class="seaocore_request" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('route' => 'sitepage_profilepagemember', 'module' => 'sitepagemember', 'controller' => 'member', 'action' => 'request', "page_id" => $this->resource_id), 'default' , true)); ?>'); return false;" style="background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagemember/externals/images/member/join.png);"><?php echo $this->translate('Request Member for Page');?></a>
				<?php endif; ?>
          
        <?php //CASE FOR SITEEVENT ?>
          
        <?php
           if ($this->resource_type == 'siteevent_event') :
        
               include APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_infotooltip_footer.tpl';                 endif;
        
        
        
        ?>

        <?php //FOR SHARE LINK.
         if ( !empty ($this->viewer_id) && ($this->resource_type != 'user') && ($this->resource_type != 'siteevent_event') && ($this->resource_type != 'forum') && ($this->resource_type != 'album') && !empty($info_values) && in_array("share",
          $info_values) ): ?>
					<?php if (!$flag) : ?>
						<div class="info_tip_content_bottom">
					<?php $flag = true; endif; ?>
          <a class="seaocore_share" href="javascript:void(0);" onclick="showSmoothBox('<?php echo
$this->escape($this->url(array('module'=> 'advancedactivity', 'controller' => 'index', 'action' =>
'share','type' => $this->resource_type, 'id' => $this->resource_id, 'format' => 'smoothbox'), 'default' ,
true)); ?>'); return false;" style="background-image:
url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/share.png);"><?php echo $this->translate("Share") ?></a>
        <?php endif; ?>		
			<?php if ($flag) : ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<script type="text/javascript">

function showSmoothBox(url)
{ 
  Smoothbox.open(url);
  parent.Smoothbox.close;
}
</script>
  <script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href ;
		Smoothbox.open(Obj_Url);
	}
</script>