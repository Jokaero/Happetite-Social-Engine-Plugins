<?php
    $sitepage = $this->subject();
    //GET VIEWER INFORMATION
    $this->allowPage = Engine_Api::_()->sitepage()->allowInThisPage($sitepage, "sitepagemember", 'smecreate');
    $this->cover_params = array('top' => 0, 'left' => 0);

    if(Engine_Api::_()->hasModuleBootstrap('sitepagebadge') && isset($sitepage->badge_id))  {
			$this->sitepagebadges_value = Engine_Api::_()->getApi('settings', 'core')->sitepagebadge_badgeprofile_widgets;
			$this->sitepagebadge = Engine_Api::_()->getItem('sitepagebadge_badge', $sitepage->badge_id);
    }

    $this->sitepageTags = $sitepage->tags()->getTagMaps();
    $this->resource_id = $resource_id = $sitepage->getIdentity();
    $this->resource_type = $resource_type = $sitepage->getType();
    $this->follow_count = Engine_Api::_()->getDbtable('follows', 'seaocore')->numberOfFollow($resource_type, $resource_id);
    $this->subcategory_name = '';
    $this->subsubcategory_name = '';
    $categoriesTable = Engine_Api::_()->getDbTable('categories', 'sitepage');
    $this->category_name = $categoriesTable->getCategory($sitepage->category_id)->category_name;
    if(isset($categoriesTable->getCategory($sitepage->subcategory_id)->category_name))
    $this->subcategory_name = $categoriesTable->getCategory($sitepage->subcategory_id)->category_name;
    if(isset($categoriesTable->getCategory($sitepage->subsubcategory_id)->category_name))
    $this->subsubcategory_name = $categoriesTable->getCategory($sitepage->subsubcategory_id)->category_name;
?>


 <div class="seaocore_profile_cover_head_section <?php if (!in_array('mainPhoto', $this->showContent)): ?>seaocore_profile_photo_none<?php endif; ?>" id="siteuser_main_photo">
    <?php if (in_array('mainPhoto', $this->showContent) || in_array('title', $this->showContent) || in_array('category', $this->showContent)): ?>
			<div class="seaocore_profile_cover_head">
				<?php if (in_array('mainPhoto', $this->showContent)): ?>
					<div class="seaocore_profile_main_photo_wrapper">
						<div class='seaocore_profile_main_photo'>
							<div class="item_photo <?php if($this->sitecontentcoverphotoStrachMainPhoto):?> show_photo_box <?php endif; ?>">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr valign="middle">
                    <td>
										<?php 			
											$href = Engine_Api::_()->seaocore()->getContentPhotoHref($this->subject());
										?>
										<?php if (empty($this->can_edit) && $href) : ?>
											<a href="<?php echo $href; ?>" class="thumbs_photo" data-linktype="photo-gallery">
										<?php endif; ?>
										<?php echo $this->itemPhoto($this->subject(), 'thumb.profile', '', array('align' => 'left', 'id' => 'content_profile_photo')); ?>
										<?php if (empty($this->can_edit) && $href) : ?></a><?php endif; ?>
                    </td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				<?php endif;?>
        
        <?php if(in_array('badge', $this->showContent) && isset($this->sitepagebadges_value) && isset($this->sitepagebadge)):?>
					<?php if (empty($this->sitepagebadges_value) || $this->sitepagebadges_value == 2): ?>
						<?php $badgeTitle = $this->sitepagebadge->title; ?>
					<?php endif; ?>

					<?php if ($this->sitepagebadges_value == 1 || $this->sitepagebadges_value == 2): ?>
						<?php if (!empty($this->sitepagebadge->badge_main_id)) :?>
							<?php $main_path = Engine_Api::_()->storage()->get($this->sitepagebadge->badge_main_id, '')->getPhotoUrl(); ?>
							<?php if(!empty($main_path)) :?>
              <a data-transition="pop" href="#profile_badges_<?php echo $this->sitepagebadge->badge_main_id?>" data-rel="popup"><img src="<?php echo $main_path ?>" class="fright" style="height:50px;width:50px;" /></a>
							<?php endif;?>
						<?php endif; ?>
					<?php endif; ?>
        <?php endif; ?>
        
				<?php if (in_array('title', $this->showContent) || in_array('category', $this->showContent) || in_array('subcategory', $this->showContent) || in_array('subsubcategory', $this->showContent) || in_array('badge', $this->showContent)): ?>
					<div class="seaocore_profile_cover_title">
						<?php if(in_array('title', $this->showContent)):?>
							<a href="<?php echo $sitepage->getHref(); ?>"><h2><?php echo $sitepage->getTitle(); ?></h2></a>
						<?php endif;?>
          
          <div class="seaocore_txt_light" style="font-size:12px;" >
						<?php if(in_array('category', $this->showContent)):?>
							<?php echo $this->htmlLink($this->url(array('category_id' => $sitepage->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name)), 'sitepage_general_category'), $this->translate($this->category_name)) ?>
						<?php endif;?>
						<?php if(in_array('subcategory', $this->showContent) && isset(Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($sitepage->subcategory_id)->category_name)):?>
              <?php echo '&raquo;';?>  
							<?php echo $this->htmlLink($this->url(array('category_id' => $sitepage->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name), 'subcategory_id' => $sitepage->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->subcategory_name)), 'sitepage_general_subcategory'), $this->translate($this->subcategory_name)) ?>
						<?php endif;?>
						<?php if(in_array('subsubcategory', $this->showContent) && isset(Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($sitepage->subsubcategory_id)->category_name)):?>
              <?php echo '&raquo;';?> 
							<?php echo $this->htmlLink($this->url(array('category_id' => $sitepage->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name), 'subcategory_id' => $sitepage->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->subcategory_name),'subsubcategory_id' => $sitepage->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->subsubcategory_name)), 'sitepage_general_subsubcategory'),$this->translate($this->subsubcategory_name)) ?>
						<?php endif;?>
					</div>
</div>
				<?php endif;?>
			</div>
		<?php endif;?>
         <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')):?>
      <?php if (($sitepage->rating > 0)): ?>
        <?php
        $currentRatingValue = $sitepage->rating;
        $difference = $currentRatingValue - (int) $currentRatingValue;
        if ($difference < .5) {
          $finalRatingValue = (int) $currentRatingValue;
        } else {
          $finalRatingValue = (int) $currentRatingValue + .5;
        }
        ?>
        <p>  
          <span class="list_rating_star" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
            <?php for ($x = 1; $x <= $sitepage->rating; $x++): ?>
              <span class="rating_star_generic rating_star" ></span>
            <?php endfor; ?>
            <?php if ((round($sitepage->rating) - $sitepage->rating) > 0): ?>
              <span class="rating_star_generic rating_star_half" ></span>
            <?php endif; ?>
          </span>
        </p>
      <?php endif; ?>
    <?php endif; ?>
    <?php if(in_array('description', $this->showContent) || in_array('phone', $this->showContent) || in_array('website', $this->showContent) || in_array('email', $this->showContent) || in_array('sponsored', $this->showContent) || in_array('featured', $this->showContent)):?>
			<div class="ui-page-content">
        <?php if(!empty($sitepage->sponsored) || !empty($sitepage->featured)):?>
					<table cellpadding="2" cellspacing="0" style="width:100%">
						<tr>
							<?php if (in_array('sponsored', $this->showContent) && !empty($sitepage->sponsored)): ?>
								<td style="width:50%;">
									<div class="sm-sl" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.color', '#fc0505'); ?>;'>
										<?php echo $this->translate('SPONSORED'); ?>
									</div>
								</td>
							<?php endif; ?>
							<?php if (in_array('featured', $this->showContent) && !empty($sitepage->featured)): ?>
							<td style="width:50%;">
								<div class="sm-sl" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.featured.color', '#0cf523'); ?>;'>
									<?php echo $this->translate('FEATURED'); ?>
								</div>
							</td>
							<?php endif; ?>
						</tr>
					</table>
        <?php endif; ?>
        <?php if(in_array('description', $this->showContent)):?><br />
					<?php if(isset(Engine_Api::_()->getDbtable('writes', 'sitepage')->writeContent($sitepage->page_id)->text)):?>
					<div>
						<?php echo $this->viewMore(htmlspecialchars_decode(nl2br(Engine_Api::_()->getDbtable('writes', 'sitepage')->writeContent($sitepage->page_id)->text), ENT_QUOTES), 200) ?>
					</div> 
					<?php else:?>
					<div>
						<?php echo $this->viewMore($sitepage->body, 200) ?>
					</div> 
					<?php endif;?>
        <?php endif;?>
     <div class="siteuser_cover_profile_fields">    
      <ul>
      <?php if (is_array($this->showContent) && in_array('modifiedDate', $this->showContent)):?>
				<li>
					<?php echo $this->translate('Last updated %s', $this->timestamp($sitepage->modified_date)) ?>
				</li>       
      <?php endif;?>

      <?php 

        $statistics = '';

        if(is_array($this->showContent) &&  in_array('commentCount', $this->showContent)) {
          $statistics .= $this->translate(array('%s comment', '%s comments', $sitepage->comment_count), $this->locale()->toNumber($sitepage->comment_count)).' - ';
        }

        if(is_array($this->showContent) && in_array('viewCount', $this->showContent)) {
          $statistics .= $this->translate(array('%s view', '%s views', $sitepage->view_count), $this->locale()->toNumber($sitepage->view_count)).' - ';
        }

        if(is_array($this->showContent) &&  in_array('likeCount', $this->showContent)) {
          $statistics .= $this->translate(array('%s like', '%s likes', $sitepage->like_count), $this->locale()->toNumber($sitepage->like_count)).' - ';
        }                 

        if(is_array($this->showContent) && in_array('followerCount', $this->showContent) &&  isset($sitepage->follow_count)) {
          $statistics .= $this->translate(array('%s follower', '%s followers', $sitepage->follow_count), $this->locale()->toNumber($sitepage->follow_count)).' - ';
        }       

        if(is_array($this->showContent) && in_array('memberCount', $this->showContent) && isset($sitepage->member_count)) {
				 $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1);
			   if ($sitepage->member_title && $memberTitle) : 
				 if ($sitepage->member_count == 1) :  $statistics .=  $sitepage->member_count . ' member'.' - '; else: 	 $statistics .=  $sitepage->member_count . ' ' .  $sitepage->member_title.' - '; endif; 
				 else : 
					$statistics .= $this->translate(array('%s member', '%s members', $sitepage->member_count), $this->locale()->toNumber($sitepage->member_count)).' - ';
				 endif;
        }       

        $statistics = trim($statistics);
        $statistics = rtrim($statistics, '-');

      ?>
      <li><?php echo $statistics; ?></li>
    </ul>
     </div>
				<?php if (!empty($sitepage->location) && in_array('location', $this->showContent)):?>
          <div class="siteuser_cover_profile_fields">
						<ul>
								<li>
									<span><?php echo $this->translate("Location")?>:</span>
									<span> <?php echo $this->htmlLink('https://maps.google.com/?q='.urlencode($sitepage->location), $sitepage->location, array('target' => 'blank')) ?> </span>
								</li>
            </ul>
          </div>
				<?php endif;?>
				<?php if (in_array('tags', $this->showContent) && count($this->sitepageTags) > 0): $tagCount = 0; ?>
          <div class="siteuser_cover_profile_fields">
						<ul>
							<li>
								<span><?php echo $this->translate('Tags'); ?>:</span>
								<span><?php foreach ($this->sitepageTags as $tag): ?>
									<?php if (!empty($tag->getTag()->text)): ?>
										<?php if (empty($tagCount)): ?>
                      <a href='<?php echo $this->url(array('action' => 'index', 'tag' => $tag->getTag()->tag_id, 'tag_name' => Engine_Api::_()->seaocore()->getSlug($tag->getTag()->text, 225)), "sitepage_general"); ?>'>#<?php echo $tag->getTag()->text ?></a>
											<?php $tagCount++;
										else: ?>
											<a href='<?php echo $this->url(array('action' => 'index', 'tag' => $tag->getTag()->tag_id, 'tag_name' => Engine_Api::_()->seaocore()->getSlug($tag->getTag()->text, 225)), "sitepage_general"); ?>'>#<?php echo $tag->getTag()->text ?></a>
										<?php endif; ?>
									<?php endif; ?>
								<?php endforeach; ?></span>
							</li>
            </ul>
          </div>
				<?php endif; ?>
				<?php if ($sitepage->price > 0 && in_array('price', $this->showContent)): ?>
          <div class="siteuser_cover_profile_fields">
						<ul>
							<li>
               <span><?php echo $this->translate('Price'); ?>:</span>
								<span>
                  <b>
									  <?php echo $this->locale()->toCurrency($sitepage->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')); ?>
								  </b>
                </span>
							</li> 
            </ul>
          </div>  
				<?php endif; ?> 
        <?php if(($sitepage->phone || $sitepage->email || $sitepage->website) && in_array('phone', $this->showContent) || in_array('website', $this->showContent) || in_array('email', $this->showContent)):?>
          <?php if(($sitepage->phone || $sitepage->email || $sitepage->website)):?>
						<div class="siteuser_cover_profile_fields">
							<h4>
								<span><?php echo $this->translate("Contact Information");?></span>
							</h4>
							<ul>
								<?php if(in_array('phone', $this->showContent) &&  $sitepage->phone):?>
									<li>
										<span><?php echo $this->translate("Phone")?>:</span>
										<span> <a href="tel:<?php echo $sitepage->phone?>"> <?php echo $sitepage->phone?> </a></span>
									</li>
								<?php endif;?>
								<?php if(in_array('email', $this->showContent) &&  $sitepage->email):?>
									<li>
										<span><?php echo $this->translate("Email")?>:</span>
										<span> <a href='mailto:<?php echo $sitepage->email ?>'><?php echo $this->translate('Email Me') ?></a> </span>
									</li>
								<?php endif;?>
								<?php if(in_array('website', $this->showContent) &&  $sitepage->email):?>
									<li>
										<span><?php echo $this->translate("Website")?>:</span>
										<?php if (strstr($sitepage->website, 'http://') || strstr($sitepage->website, 'https://')): ?>
										<span> <a href='<?php echo $sitepage->website ?>' target="_blank" title='<?php echo $sitepage->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a> </span>
										<?php else: ?>
										<span> 	<a href='http://<?php echo $sitepage->website ?>' target="_blank" title='<?php echo $sitepage->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a> </span>
										<?php endif; ?>
									</li>
								<?php endif;?>
							</ul>
						</div>	
          <?php endif;?>
        <?php endif;?>
			</div>
    <?php endif;?>	
    <?php if (in_array('likeButton', $this->showContent) || in_array('followButton', $this->showContent) || in_array('joinButton', $this->showContent) || in_array('addButton', $this->showContent) || in_array('leaveButton', $this->showContent)): ?>
			<div class="seaocore_profile_cover_buttons">
				<table cellpadding="2" cellspacing="0">
					<tr>
            <?php if (in_array('likeButton', $this->showContent)):?>
							<td id="seaocore_like">
								<?php if(!empty($this->viewer_id)): ?>
									<?php $hasLike = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($this->resource_type, $this->resource_id); ?>
									<a href ="javascript://" onclick = "seaocore_content_type_likes_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type; ?>_unlikes_<?php echo $this->resource_id;?>" style ='display:<?php echo $hasLike ?"block":"none"?>'>
										<i class="ui-icon-thumbs-down-alt"></i>
										<span><?php echo $this->translate('Unlike') ?></span>
									</a>
									<a href = "javascript://" onclick = "seaocore_content_type_likes_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type; ?>_most_likes_<?php echo $this->resource_id;?>" style ='display:<?php echo empty($hasLike) ?"block":"none"?>'>
										<i class="ui-icon-thumbs-up-alt"></i>
										<span><?php echo $this->translate('Like') ?></span>
									</a>
									<input type ="hidden" id = "<?php echo $this->resource_type; ?>_like_<?php echo $this->resource_id;?>" value = '<?php echo $hasLike ? $hasLike[0]['like_id'] :0; ?>' />
								<?php endif; ?>
							</td>
            <?php endif;?>
            <?php if (in_array('followButton', $this->showContent)):?>
							<?php if($this->viewer_id != $sitepage->getOwner()->getIdentity()):?>
								<td id="seaocore_follow">
									<?php if ($this->viewer_id): ?>
										<?php $isFollow = $sitepage->follows()->isFollow($this->viewer()); ?>
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
            <?php if(Engine_Api::_()->hasModuleBootstrap('sitepagemember') && !empty($this->viewer_id)):?>
              <?php if (in_array('joinButton', $this->showContent)):?>
								<?php $joinMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($this->viewer_id, $sitepage->page_id);
								if (empty($joinMembers) && $this->viewer_id != $sitepage->owner_id && !empty($this->allowPage)): ?>
									<?php if (!empty($this->viewer_id)) : ?>
										<?php if (!empty($sitepage->member_approval)): ?>
											<td>
												<a href="<?php echo $this->escape($this->url(array( 'action' => 'join', 'page_id' => $sitepage->page_id), 'sitepage_profilepagemember', true)); ?>" class="smoothbox" data-role='button' data-icon='false' data-inset='false' data-mini='true' data-corners='false' data-shadow='true'>
                          <i class="ui-icon-ok"></i>
                          <span><?php echo $this->translate("Join Page"); ?></span>
												</a>
											</td>
										<?php else: ?>
											<td>
												<a href='<?php echo $this->escape($this->url(array( 'action' => 'request', 'page_id' => $sitepage->page_id), 'sitepage_profilepagemember', true)); ?>' class="smoothbox" data-role='button' data-icon='false' data-inset='false' data-mini='true' data-corners='false' data-shadow='true'>
                          <i class="ui-icon-ok"></i>
                          <span><?php echo $this->translate("Join Page"); ?></span></a>
											</td>
										<?php endif; ?>
									<?php endif; ?>
								<?php endif; ?>
								<?php $joinMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($this->viewer_id, $sitepage->page_id, 'Cancel');
								if (!empty($joinMembers) && $this->viewer_id != $sitepage->owner_id && !empty($this->allowPage)): ?>
									<td>
										<a href="<?php echo $this->escape($this->url(array( 'action' => 'cancel', 'page_id' => $sitepage->page_id), 'sitepage_profilepagemember', true)); ?>" class="smoothbox" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true'>
                      <i class="ui-icon-delete"></i>
										<span><?php echo $this->translate("Cancel Membership Request"); ?></span>
										</a>
									</td>
								<?php endif;?>
              <?php endif; ?>

							<?php $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($this->viewer_id, $sitepage->page_id, $params = "Leave");
							if (!empty($hasMembers) && in_array('leaveButton', $this->showContent) && $this->viewer_id != $sitepage->owner_id && Engine_Api::_()->sitepage()->allowInThisPage($sitepage, "sitepagemember", 'smecreate')): ?>
							<td>
								<?php if ($this->viewer_id) : ?>
									<a href="<?php echo $this->escape($this->url(array( 'action' => 'leave', 'page_id' => $sitepage->page_id), 'sitepage_profilepagemember', true)); ?>" class="smoothbox"   data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true'>
                    <i class="ui-icon-delete"></i>
                    <span><?php echo $this->translate("Leave Page"); ?></span>
                  </a>
								<?php endif; ?>
							</td>
							<?php endif; ?>

              <?php if (in_array('addButton', $this->showContent)):?>
								<?php $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($this->viewer_id, $sitepage->page_id, $params = 'Invite'); ?>
								<?php if (!empty($hasMembers) && !empty($this->can_edit)) : ?>
									<td>
										<a data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' class="smoothbox" href="<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'page_id' => $sitepage->page_id), 'sitepage_profilepagemember', true)); ?>">
                      <i class="ui-icon-plus"></i>
                      <span><?php echo $this->translate("Add People"); ?></span>
                    </a>	
									</td>
									<?php elseif (!empty($hasMembers) && empty($sitepage->member_invite)): ?>
									<td>
										<a data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' class="smoothbox" href='<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'page_id' => $sitepage->page_id), 'sitepage_profilepagemember', true)); ?>'>
                      <i class="ui-icon-plus"></i>
                      <span><?php echo $this->translate("Add People"); ?></span>
                    </a>
									</td>
								<?php endif; ?>
              <?php endif; ?>
             <?php endif; ?> 
					</tr>
				</table>  
			</div>
    <?php endif;?>  
  </div>