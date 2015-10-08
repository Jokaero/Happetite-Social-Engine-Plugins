<?php 
    $sitegroup = $this->subject();
    //GET VIEWER INFORMATION
    $this->allowGroup = Engine_Api::_()->sitegroup()->allowInThisGroup($sitegroup, "sitegroupmember", 'smecreate');
    $this->cover_params = array('top' => 0, 'left' => 0);

    if(Engine_Api::_()->hasModuleBootstrap('sitegroupbadge') && isset($sitegroup->badge_id))  {
			$this->sitegroupbadges_value = Engine_Api::_()->getApi('settings', 'core')->sitegroupbadge_badgeprofile_widgets;
			$this->sitegroupbadge = Engine_Api::_()->getItem('sitegroupbadge_badge', $sitegroup->badge_id);
    }

    $this->sitegroupTags = $sitegroup->tags()->getTagMaps();
    $this->resource_id = $resource_id = $sitegroup->getIdentity();
    $this->resource_type = $resource_type = $sitegroup->getType();
    $this->follow_count = Engine_Api::_()->getDbtable('follows', 'seaocore')->numberOfFollow($resource_type, $resource_id);
    $this->subcategory_name = '';
    $this->subsubcategory_name = '';
    $categoriesTable = Engine_Api::_()->getDbTable('categories', 'sitegroup');
    $this->category_name = $categoriesTable->getCategory($sitegroup->category_id)->category_name;
    if(isset($categoriesTable->getCategory($sitegroup->subcategory_id)->category_name))
    $this->subcategory_name = $categoriesTable->getCategory($sitegroup->subcategory_id)->category_name;
    if(isset($categoriesTable->getCategory($sitegroup->subsubcategory_id)->category_name))
    $this->subsubcategory_name = $categoriesTable->getCategory($sitegroup->subsubcategory_id)->category_name;
?>

<?php if(!empty($sitegroup->sponsored) || !empty($sitegroup->featured)):?>
  <div class="list-label-wrap">
    <?php if (in_array('sponsored', $this->showContent) && !empty($sitegroup->sponsored)): ?>
      <span class="list-label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.color', '#fc0505'); ?>;'>
        <?php echo $this->translate('SPONSORED'); ?>
      </span>
    <?php endif; ?>
    <?php if (in_array('featured', $this->showContent) && !empty($sitegroup->featured)): ?>
      <span class="list-label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.featured.color', '#0cf523'); ?>;'>
        <?php echo $this->translate('FEATURED'); ?>
      </span>
    <?php endif; ?>
  </div>
<?php endif; ?>

  <?php if (in_array('mainPhoto', $this->showContent) || in_array('title', $this->showContent) || in_array('category', $this->showContent)): ?>
    <div class="content_cover_head" id="siteuser_main_photo">
      <?php if (in_array('mainPhoto', $this->showContent)): ?>
        <div class="content_cover_main_photo_wrapper">
          <div class='content_cover_main_photo'>
            <div class="item_photo<?php if($this->sitecontentcoverphotoStrachMainPhoto):?> show_photo_box<?php endif; ?>">
              <table border="0" cellpadding="0" cellspacing="0">
                <tr valign="middle">
                  <td>
                    <?php 			
                      $href = Engine_Api::_()->seaocore()->getContentPhotoHref($sitegroup);
                    ?>
                    <?php if (empty($this->can_edit) && $href) : ?>
                      <a href="<?php echo $href; ?>" class="thumbs_photo" data-linktype="photo-gallery">
                    <?php endif; ?>
                    <?php echo $this->itemPhoto($sitegroup, 'thumb.profile', '', array('align' => 'left', 'id' => 'content_profile_photo')); ?>
                    <?php if (empty($this->can_edit) && $href) : ?></a><?php endif; ?>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      <?php endif;?>

      <?php if(in_array('badge', $this->showContent) && isset($this->sitegroupbadges_value) && isset($this->sitegroupbadge)):?>
        <?php if (empty($this->sitegroupbadges_value) || $this->sitegroupbadges_value == 2): ?>
        <?php endif; ?>

        <?php if ($this->sitegroupbadges_value == 1 || $this->sitegroupbadges_value == 2): ?>
          <?php if (!empty($this->sitegroupbadge->badge_main_id)) :?>
            <?php $main_path = Engine_Api::_()->storage()->get($this->sitegroupbadge->badge_main_id, '')->getPhotoUrl(); ?>
            <?php if(!empty($main_path)) :?>
            <a data-transition="pop" href="#profile_badges_<?php echo $this->sitegroupbadge->badge_main_id?>" data-rel="popup"><img src="<?php echo $main_path ?>" class="badge-img" /></a>
            <?php endif;?>
          <?php endif; ?>
        <?php endif; ?>
      <?php endif; ?>

      <?php if (in_array('title', $this->showContent) || in_array('category', $this->showContent) || in_array('subcategory', $this->showContent) || in_array('subsubcategory', $this->showContent) || in_array('badge', $this->showContent)): ?>
        <div class="content_cover_title<?php if (!in_array('mainPhoto', $this->showContent)): ?> seaocore_profile_photo_none<?php endif; ?>">
          <?php if(in_array('title', $this->showContent)):?>
            <a href="<?php echo $sitegroup->getHref(); ?>"><h2><?php echo $sitegroup->getTitle(); ?></h2></a>
          <?php endif;?>
          <div class="f_small">
            <?php if(in_array('category', $this->showContent) && $sitegroup->category_id):?>
              <?php echo $this->htmlLink($this->url(array('category_id' => $sitegroup->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->category_name)), 'sitegroup_general_category'), $this->translate($this->category_name)) ?>
            <?php endif;?>
            <?php if(in_array('subcategory', $this->showContent) && isset(Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($sitegroup->subcategory_id)->category_name)):?>
              <?php echo '&raquo;';?>  
              <?php echo $this->htmlLink($this->url(array('category_id' => $sitegroup->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->category_name), 'subcategory_id' => $sitegroup->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->subcategory_name)), 'sitegroup_general_subcategory'), $this->translate($this->subcategory_name)) ?>
            <?php endif;?>
            <?php if(in_array('subsubcategory', $this->showContent) && isset(Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($sitegroup->subsubcategory_id)->category_name)):?>
              <?php echo '&raquo;';?> 
              <?php echo $this->htmlLink($this->url(array('category_id' => $sitegroup->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->category_name), 'subcategory_id' => $sitegroup->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->subcategory_name),'subsubcategory_id' => $sitegroup->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->subsubcategory_name)), 'sitegroup_general_subsubcategory'),$this->translate($this->subsubcategory_name)) ?>
            <?php endif;?>
          </div>
        </div>
      <?php endif;?>
    </div>
  <?php endif;?>
  <div class="content_cover_info o_hidden <?php if (!in_array('mainPhoto', $this->showContent)): ?>seaocore_profile_photo_none<?php endif; ?>">
    <?php if ($sitegroup->price > 0 && in_array('price', $this->showContent)): ?>
      <p class="fright f_small">
        <b>
          <?php echo $this->locale()->toCurrency($sitegroup->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')); ?>
        </b>  
      </p>  
    <?php endif; ?>
    <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')):?>
      <?php if (($sitegroup->rating > 0)): ?>
        <?php
        $currentRatingValue = $sitegroup->rating;
        $difference = $currentRatingValue - (int) $currentRatingValue;
        if ($difference < .5) {
          $finalRatingValue = (int) $currentRatingValue;
        } else {
          $finalRatingValue = (int) $currentRatingValue + .5;
        }
        ?>
        <p>  
          <span class="list_rating_star" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
            <?php for ($x = 1; $x <= $sitegroup->rating; $x++): ?>
              <span class="rating_star_generic rating_star" ></span>
            <?php endfor; ?>
            <?php if ((round($sitegroup->rating) - $sitegroup->rating) > 0): ?>
              <span class="rating_star_generic rating_star_half" ></span>
            <?php endif; ?>
          </span>
        </p>
      <?php endif; ?>
    <?php endif; ?>
        
    <?php if (!empty($sitegroup->location) && in_array('location', $this->showContent)):?>
      <p class="t_light f_small">
        <i class="ui-icon-map-marker"></i>
        <?php echo $this->htmlLink('https://maps.google.com/?q='.urlencode($sitegroup->location), $sitegroup->location, array('target' => 'blank')) ?>
      </p>
    <?php endif;?>
  </div>

  <div class="seaocore_cover_cont">
    <?php if (in_array('likeButton', $this->showContent) || in_array('followButton', $this->showContent) || in_array('joinButton', $this->showContent) || in_array('addButton', $this->showContent) || in_array('leaveButton', $this->showContent)): ?>
    <div class="seaocore_profile_cover_buttons">
      <table cellpadding="2" cellspacing="0">
        <tr>
          <?php if (in_array('likeButton', $this->showContent)):?>
            <td id="seaocore_like">
              <?php if(!empty($this->viewer_id)): ?>
                <?php $hasLike = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($this->resource_type, $this->resource_id); ?>
                <a href ="javascript://" onclick = "seaocore_content_type_likes_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>','unlike');" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type; ?>_unlikes_<?php echo $this->resource_id;?>" style ='display:<?php echo $hasLike ?"block":"none"?>'>
                  <i class="ui-icon ui-icon-thumbs-up-alt feed-unlike-btn"></i>
                  <span><?php echo $this->translate('Like') ?></span>
                </a>
                <a href = "javascript://" onclick = "seaocore_content_type_likes_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>','like');" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type; ?>_most_likes_<?php echo $this->resource_id;?>" style ='display:<?php echo empty($hasLike) ?"block":"none"?>'>
                  <i class="ui-icon ui-icon-thumbs-up-alt feed-like-btn"></i>
                  <span><?php echo $this->translate('Like') ?></span>
                </a>
                <input type ="hidden" id = "<?php echo $this->resource_type; ?>_like_<?php echo $this->resource_id;?>" value = '<?php echo $hasLike ? $hasLike[0]['like_id'] :0; ?>' />
              <?php endif; ?>
            </td>
          <?php endif;?>
          <?php if (in_array('followButton', $this->showContent)):?>
            <?php if($this->viewer_id != $sitegroup->getOwner()->getIdentity()):?>
              <td id="seaocore_follow">
                <?php if ($this->viewer_id): ?>
                  <?php $isFollow = $sitegroup->follows()->isFollow($this->viewer()); ?>
                    <a href="javascript://" onclick = "seaocore_resource_type_follows_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type ?>_unfollows_<?php echo $this->resource_id;?>" style =' display:<?php echo $isFollow ?"block":"none"?>'>
                      <i class="ui-icon-delete"></i>
                      <span><?php echo $this->translate('Unfollow') ?></span>
                    </a>
                    <a href="javascript://" onclick = "seaocore_resource_type_follows_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type ?>_most_follows_<?php echo $this->resource_id;?>" style ='display:<?php echo empty($isFollow) ?"block":"none"?>'>
                      <i class="ui-icon-plus"></i>
                      <span><?php echo $this->translate('Follow') ?></span>
                    </a>
                    <input type ="hidden" id = "<?php echo $this->resource_type; ?>_follow_<?php echo $this->resource_id;?>" value = '<?php echo $isFollow ? $isFollow :0; ?>' />
                <?php endif; ?>
              </td>
            <?php endif; ?>
          <?php endif;?>
              
             <!--JOIN /LEAVE BUTTON WILL BE SHOWN ONLY IF SITEGROUP MEMBER EXIST HERE OTHERWISE DISPLAY MESSAGE & SHARE BUTTON-->                <?php if(Engine_Api::_()->hasModuleBootstrap('sitegroupmember') && !empty($this->viewer_id)):?>   
              <?php if (in_array('joinButton', $this->showContent)):?>
								<?php $joinMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($this->viewer_id, $sitegroup->group_id);
								if (empty($joinMembers) && $this->viewer_id != $sitegroup->owner_id && !empty($this->allowGroup)): ?>
									<?php if (!empty($this->viewer_id)) : ?>
										<?php if (!empty($sitegroup->member_approval)): ?>
											<td>
                      <a href="<?php echo $this->escape($this->url(array( 'action' => 'join', 'group_id' => $sitegroup->group_id), 'sitegroup_profilegroupmember', true)); ?>" class="smoothbox" data-role='button' data-icon='false' data-inset='false' data-mini='true' data-corners='false' data-shadow='true'>
                        <i class="ui-icon-ok"></i>
												<span><?php echo $this->translate("Join"); ?></span>
												</a>
											</td>
										<?php else: ?>
											<td>
												<a href='<?php echo $this->escape($this->url(array( 'action' => 'request', 'group_id' => $sitegroup->group_id), 'sitegroup_profilegroupmember', true)); ?>' class="smoothbox" data-role='button' data-icon='false' data-inset='false' data-mini='true' data-corners='false' data-shadow='true'>
                        <i class="ui-icon-ok"></i>
                        <span><?php echo $this->translate("Join"); ?></span>
                      </a>
											</td>
										<?php endif; ?>
									<?php endif; ?>
								<?php endif; ?>
								<?php $joinMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($this->viewer_id, $sitegroup->group_id, 'Cancel');
								if (!empty($joinMembers) && $this->viewer_id != $sitegroup->owner_id && !empty($this->allowGroup)): ?>
									<td>
                  <a href="<?php echo $this->escape($this->url(array( 'action' => 'cancel', 'group_id' => $sitegroup->group_id), 'sitegroup_profilegroupmember', true)); ?>" class="smoothbox" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true'>
                    <i class="ui-icon-delete"></i>
										<span><?php echo $this->translate("Cancel Request"); ?></span>
										</a>
									</td>
								<?php endif;?>
              <?php endif; ?>

							<?php $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($this->viewer_id, $sitegroup->group_id, $params = "Leave");
							if (!empty($hasMembers) && in_array('leaveButton', $this->showContent) && $this->viewer_id != $sitegroup->owner_id && Engine_Api::_()->sitegroup()->allowInThisGroup($sitegroup, "sitegroupmember", 'smecreate')): ?>
							<td>
								<?php if ($this->viewer_id) : ?>
                <a href="<?php echo $this->escape($this->url(array( 'action' => 'leave', 'group_id' => $sitegroup->group_id), 'sitegroup_profilegroupmember', true)); ?>" class="smoothbox" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true'>
                  <i class="ui-icon-delete"></i>
                  <span><?php echo $this->translate("Leave"); ?></span>
                </a>
								<?php endif; ?>
							</td>
							<?php endif; ?>

              <?php if (in_array('addButton', $this->showContent)):?>
								<?php $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($this->viewer_id, $sitegroup->group_id, $params = 'Invite'); ?>
								<?php if (!empty($hasMembers) && !empty($this->can_edit)) : ?>
									<td>
                  <a data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' class="smoothbox" href="<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'group_id' => $sitegroup->group_id), 'sitegroup_profilegroupmember', true)); ?>">
                    <i class="ui-icon-plus"></i>
                    <span><?php echo $this->translate("Add People"); ?></span>
                  </a>
									</td>
									<?php elseif (!empty($hasMembers) && empty($sitegroup->member_invite)): ?>
									<td>
                  <a data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' class="smoothbox" href='<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'group_id' => $sitegroup->group_id), 'sitegroup_profilegroupmember', true)); ?>'>
                    <i class="ui-icon-plus"></i>
                    <span><?php echo $this->translate("Add People"); ?></span>
                  </a>
									</td>
								<?php endif; ?>
              <?php endif; ?>
             <?php else:?>
                <?php if($this->viewer_id && $this->can_share):?>
                  <td>                    
										<a data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' class="smoothbox" href="<?php echo $this->escape($this->url( array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $this->resource_type, 'id' => $this->resource_id, 'format' => 'smoothbox'), 'default', true)); ?>">
                       <i class="ui-icon-share-alt"></i>
                      <span><?php echo $this->translate("share"); ?></span>
                    </a>	
									</td>
                  <?php endif; ?>
                  <?php if ($sitegroup->owner_id != $this->viewer_id && $this->viewer_id && $this->showMessageOwner != 'none'):?>
                  <td>
                  <a data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' class="smoothbox" href="<?php echo $this->escape($this->url( array('action' => 'message-owner', 'group_id' => $this->resource_id, 'format' => 'smoothbox'), 'sitegroup_profilegroup', true)); ?>">
                       <i class="ui-icon-envelope-alt"></i>
                      <span><?php echo $this->translate("Message"); ?></span>
                    </a>
                    </td>
                  <?php endif; ?>
                  <?php endif; ?>    
        </tr>
      </table>  
    </div>
  <?php endif;?>

  <?php if(in_array('description', $this->showContent) || in_array('phone', $this->showContent) || in_array('website', $this->showContent) || in_array('email', $this->showContent)):?>
    <div class="ui-group-content sm-widget-block content_cover_profile_fields">
      <h4><?php echo $this->translate('Details'); ?></h4>
      <ul>
        <?php if(in_array('description', $this->showContent)):?>
          <?php if(isset(Engine_Api::_()->getDbtable('writes', 'sitegroup')->writeContent($sitegroup->group_id)->text)):?>
            <li>
              <?php echo $this->viewMore(htmlspecialchars_decode(nl2br(Engine_Api::_()->getDbtable('writes', 'sitegroup')->writeContent($sitegroup->group_id)->text), ENT_QUOTES), 200) ?>
            </li>
          <?php else:?>
            <li>
              <?php echo $this->viewMore($sitegroup->body, 200) ?>
            </li>
          <?php endif;?>
        <?php endif;?>
      </ul>
      <ul>
      <?php if (is_array($this->showContent) && in_array('modifiedDate', $this->showContent)):?>
				<li>
					<?php echo $this->translate('Last updated %s', $this->timestamp($sitegroup->modified_date)) ?>
				</li>       
      <?php endif;?>

      <?php 

        $statistics = '';

        if(is_array($this->showContent) &&  in_array('commentCount', $this->showContent)) {
          $statistics .= $this->translate(array('%s comment', '%s comments', $sitegroup->comment_count), $this->locale()->toNumber($sitegroup->comment_count)).' - ';
        }

        if(is_array($this->showContent) && in_array('viewCount', $this->showContent)) {
          $statistics .= $this->translate(array('%s view', '%s views', $sitegroup->view_count), $this->locale()->toNumber($sitegroup->view_count)).' - ';
        }

        if(is_array($this->showContent) &&  in_array('likeCount', $this->showContent)) {
          $statistics .= $this->translate(array('%s like', '%s likes', $sitegroup->like_count), $this->locale()->toNumber($sitegroup->like_count)).' - ';
        }                 

        if(is_array($this->showContent) && in_array('followerCount', $this->showContent) &&  isset($sitegroup->follow_count)) {
          $statistics .= $this->translate(array('%s follower', '%s followers', $sitegroup->follow_count), $this->locale()->toNumber($sitegroup->follow_count)).' - ';
        }       

        if(is_array($this->showContent) && in_array('memberCount', $this->showContent) && isset($sitegroup->member_count)) {
				 $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.member.title' , 1);
			   if ($sitegroup->member_title && $memberTitle) : 
				 if ($sitegroup->member_count == 1) :  $statistics .=  $sitegroup->member_count . ' member'.' - '; else: 	 $statistics .=  $sitegroup->member_count . ' ' .  $sitegroup->member_title.' - '; endif; 
				 else : 
					$statistics .= $this->translate(array('%s member', '%s members', $sitegroup->member_count), $this->locale()->toNumber($sitegroup->member_count)).' - ';
				 endif;
        }       

        $statistics = trim($statistics);
        $statistics = rtrim($statistics, '-');

      ?>
      <li><?php echo $statistics; ?></li>
      <?php if (in_array('tags', $this->showContent) && count($this->sitegroupTags) > 0): $tagCount = 0; ?>
      <li>
        <span class="t_light"><?php echo $this->translate('Tags'); ?>:</span>
        <span><?php foreach ($this->sitegroupTags as $tag): ?>
          <?php if (!empty($tag->getTag()->text)): ?>
            <?php if (empty($tagCount)): ?>
              <a href='<?php echo $this->url(array('action' => 'index', 'tag' => $tag->getTag()->tag_id, 'tag_name' => Engine_Api::_()->seaocore()->getSlug($tag->getTag()->text, 225)), "sitegroup_general"); ?>'>#<?php echo $tag->getTag()->text ?></a>
              <?php $tagCount++;
            else: ?>
              <a href='<?php echo $this->url(array('action' => 'index', 'tag' => $tag->getTag()->tag_id, 'tag_name' => Engine_Api::_()->seaocore()->getSlug($tag->getTag()->text, 225)), "sitegroup_general"); ?>'>#<?php echo $tag->getTag()->text ?></a>
            <?php endif; ?>
          <?php endif; ?>
        <?php endforeach; ?></span>
      </li>
    <?php endif; ?>
    </ul>
      <?php if(($sitegroup->phone || $sitegroup->email || $sitegroup->website) && in_array('phone', $this->showContent) || in_array('website', $this->showContent) || in_array('email', $this->showContent)):?>
        <?php if(($sitegroup->phone || $sitegroup->email || $sitegroup->website)):?>
          <h4>
            <span><?php echo $this->translate("Contact Information");?></span>
          </h4>
          <ul>
            <?php if(in_array('phone', $this->showContent) &&  $sitegroup->phone):?>
              <li>
                <span class="t_light"><?php echo $this->translate("Phone")?>:</span>
                <span> <a href="tel:<?php echo $sitegroup->phone?>"> <?php echo $sitegroup->phone?> </a></span>
              </li>
            <?php endif;?>
            <?php if(in_array('email', $this->showContent) &&  $sitegroup->email):?>
              <li>
                <span class="t_light"><?php echo $this->translate("Email")?>:</span>
                <span> <a href='mailto:<?php echo $sitegroup->email ?>'><?php echo $this->translate('Email Me') ?></a> </span>
              </li>
            <?php endif;?>
            <?php if(in_array('website', $this->showContent) &&  $sitegroup->email):?>
              <li>
                <span class="t_light"><?php echo $this->translate("Website")?>:</span>
                <?php if (strstr($sitegroup->website, 'http://') || strstr($sitegroup->website, 'https://')): ?>
                  <span> <a href='<?php echo $sitegroup->website ?>' target="_blank" title='<?php echo $sitegroup->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a> </span>
                <?php else: ?>
                  <span> 	<a href='http://<?php echo $sitegroup->website ?>' target="_blank" title='<?php echo $sitegroup->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a> </span>
                <?php endif; ?>
              </li>
            <?php endif;?>
          </ul>
        <?php endif;?>
      <?php endif;?>
    </div>
  <?php endif;?>
</div>