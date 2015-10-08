<?php
    $sitestore = $this->subject();
    //GET VIEWER INFORMATION
    $this->allowStore = Engine_Api::_()->sitestore()->allowInThisStore($sitestore, "sitestoremember", 'smecreate');
    $this->cover_params = array('top' => 0, 'left' => 0);
    $this->sitestoreTags = $sitestore->tags()->getTagMaps();
    $this->resource_id = $resource_id = $sitestore->getIdentity();
    $this->resource_type = $resource_type = $sitestore->getType();
    $this->subcategory_name = '';
    $this->subsubcategory_name = '';
    $categoriesTable = Engine_Api::_()->getDbTable('categories', 'sitestore');
    $this->category_name = $categoriesTable->getCategory($sitestore->category_id)->category_name;
    if(isset($categoriesTable->getCategory($sitestore->subcategory_id)->category_name))
    $this->subcategory_name = $categoriesTable->getCategory($sitestore->subcategory_id)->category_name;
    if(isset($categoriesTable->getCategory($sitestore->subsubcategory_id)->category_name))
    $this->subsubcategory_name = $categoriesTable->getCategory($sitestore->subsubcategory_id)->category_name;
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
										<?php 			
											$href = Engine_Api::_()->seaocore()->getContentPhotoHref($this->subject());
										?>
										<?php if (empty($this->can_edit) && $href) : ?>
											<a href="<?php echo $href; ?>" class="thumbs_photo" data-linktype="photo-gallery">
										<?php endif; ?>
										<?php echo $this->itemPhoto($this->subject(), 'thumb.profile', '', array('align' => 'left', 'id' => 'content_profile_photo')); ?>
										<?php if (empty($this->can_edit) && $href) : ?></a><?php endif; ?>
									</tr>
								</table>
							</div>
						</div>
					</div>
				<?php endif;?>
        
				<?php if (in_array('title', $this->showContent) || in_array('category', $this->showContent) || in_array('subcategory', $this->showContent) || in_array('subsubcategory', $this->showContent) || in_array('badge', $this->showContent)): ?>
					<div class="seaocore_profile_cover_title">
						<?php if(in_array('title', $this->showContent)):?>
							<a href="<?php echo $sitestore->getHref(); ?>"><h2><?php echo $sitestore->getTitle(); ?></h2></a>
						<?php endif;?>
          <div class="seaocore_txt_light" style="font-size:12px;" >
						<?php if(in_array('category', $this->showContent)):?>
							<?php echo $this->htmlLink($this->url(array('category_id' => $sitestore->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->category_name)), 'sitestore_general_category'), $this->translate($this->category_name)) ?>
						<?php endif;?>
						<?php if(in_array('subcategory', $this->showContent) && isset(Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($sitestore->subcategory_id)->category_name)):?>
              <?php echo '&raquo;';?>  
							<?php echo $this->htmlLink($this->url(array('category_id' => $sitestore->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->category_name), 'subcategory_id' => $sitestore->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subcategory_name)), 'sitestore_general_subcategory'), $this->translate($this->subcategory_name)) ?>
						<?php endif;?>
						<?php if(in_array('subsubcategory', $this->showContent) && isset(Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($sitestore->subsubcategory_id)->category_name)):?>
              <?php echo '&raquo;';?> 
							<?php echo $this->htmlLink($this->url(array('category_id' => $sitestore->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->category_name), 'subcategory_id' => $sitestore->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subcategory_name),'subsubcategory_id' => $sitestore->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($this->subsubcategory_name)), 'sitestore_general_subsubcategory'),$this->translate($this->subsubcategory_name)) ?>
						<?php endif;?>
					</div>
</div>
				<?php endif;?>
			</div>
		<?php endif;?>
    <?php if(in_array('description', $this->showContent) || in_array('phone', $this->showContent) || in_array('website', $this->showContent) || in_array('email', $this->showContent) || in_array('sponsored', $this->showContent) || in_array('featured', $this->showContent)):?>
			<div class="ui-store-content">
        <?php if(!empty($sitestore->sponsored) || !empty($sitestore->featured)):?>
					<table cellpadding="2" cellspacing="0" style="width:100%">
						<tr>
							<?php if (in_array('sponsored', $this->showContent) && !empty($sitestore->sponsored)): ?>
								<td style="width:50%;">
									<div class="sm-sl" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.color', '#fc0505'); ?>;'>
										<?php echo $this->translate('SPONSORED'); ?>
									</div>
								</td>
							<?php endif; ?>
							<?php if (in_array('featured', $this->showContent) && !empty($sitestore->featured)): ?>
							<td style="width:50%;">
								<div class="sm-sl" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.featured.color', '#0cf523'); ?>;'>
									<?php echo $this->translate('FEATURED'); ?>
								</div>
							</td>
							<?php endif; ?>
						</tr>
					</table>
        <?php endif; ?>
        <?php if(in_array('description', $this->showContent)):?><br />
					<div>
						<?php echo $this->viewMore($sitestore->body, 200) ?>
					</div> 
        <?php endif;?>
				<?php if (!empty($sitestore->location) && in_array('location', $this->showContent)):?>
          <div class="siteuser_cover_profile_fields">
						<ul>
								<li>
									<span><?php echo $this->translate("Location")?>:</span>
									<span> <?php echo $this->htmlLink('https://maps.google.com/?q='.urlencode($sitestore->location), $sitestore->location, array('target' => 'blank')) ?> </span>
								</li>
            </ul>
          </div>
				<?php endif;?>
				<?php if (in_array('tags', $this->showContent) && count($this->sitestoreTags) > 0): $tagCount = 0; ?>
          <div class="siteuser_cover_profile_fields">
						<ul>
							<li>
								<span><?php echo $this->translate('Tags'); ?>:</span>
								<span><?php foreach ($this->sitestoreTags as $tag): ?>
									<?php if (!empty($tag->getTag()->text)): ?>
										<?php if (empty($tagCount)): ?>
											<a href='<?php echo $this->url(array('action' => 'index'), "sitestore_general"); ?>?tag=<?php echo $tag->getTag()->tag_id ?>&tag_name=<?php  echo $tag->getTag()->text ?>'>#<?php echo $tag->getTag()->text ?></a>
											<?php $tagCount++;
										else: ?>
											<a href='<?php echo $this->url(array('action' => 'index'), "sitestore_general"); ?>?tag_id=<?php echo $tag->getTag()->tag_id ?>'>#<?php echo $tag->getTag()->text ?></a>
										<?php endif; ?>
									<?php endif; ?>
								<?php endforeach; ?></span>
							</li>
            </ul>
          </div>
				<?php endif; ?>
				<?php if ($sitestore->price > 0 && in_array('price', $this->showContent)): ?>
          <div class="siteuser_cover_profile_fields">
						<ul>
							<li>
               <span><?php echo $this->translate('Price'); ?>:</span>
								<span>
                  <b>
									  <?php echo $this->locale()->toCurrency($sitestore->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')); ?>
								  </b>
                </span>
							</li> 
            </ul>
          </div>  
				<?php endif; ?>  
        <?php if(($sitestore->phone || $sitestore->email || $sitestore->website) && in_array('phone', $this->showContent) || in_array('website', $this->showContent) || in_array('email', $this->showContent)):?>
          <?php if(($sitestore->phone || $sitestore->email || $sitestore->website)):?>
						<div class="siteuser_cover_profile_fields">
							<h4>
								<span><?php echo $this->translate("Contact Information");?></span>
							</h4>
							<ul>
								<?php if(in_array('phone', $this->showContent) &&  $sitestore->phone):?>
									<li>
										<span><?php echo $this->translate("Phone")?>:</span>
										<span> <a href="tel:<?php echo $sitestore->phone?>"> <?php echo $sitestore->phone?> </a></span>
									</li>
								<?php endif;?>
								<?php if(in_array('email', $this->showContent) &&  $sitestore->email):?>
									<li>
										<span><?php echo $this->translate("Email")?>:</span>
										<span> <a href='mailto:<?php echo $sitestore->email ?>'><?php echo $this->translate('Email Me') ?></a> </span>
									</li>
								<?php endif;?>
								<?php if(in_array('website', $this->showContent) &&  $sitestore->email):?>
									<li>
										<span><?php echo $this->translate("Website")?>:</span>
										<?php if (strstr($sitestore->website, 'http://') || strstr($sitestore->website, 'https://')): ?>
										<span> <a href='<?php echo $sitestore->website ?>' target="_blank" title='<?php echo $sitestore->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a> </span>
										<?php else: ?>
										<span> 	<a href='http://<?php echo $sitestore->website ?>' target="_blank" title='<?php echo $sitestore->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a> </span>
										<?php endif; ?>
									</li>
								<?php endif;?>
							</ul>
						</div>	
          <?php endif;?>
        <?php endif;?>
			</div>
    <?php endif;?>	
    <?php if (in_array('likeButton', $this->showContent)): ?>
			<div class="seaocore_profile_cover_buttons">
				<table cellpadding="2" cellspacing="0">
					<tr>
            <?php if (in_array('likeButton', $this->showContent)):?>
							<td id="seaocore_like">
								<?php if(!empty($this->viewer_id)): ?>
									<?php $hasLike = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($this->resource_type, $this->resource_id); ?>
									<a href ="javascript://" onclick = "seaocore_content_type_likes_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');" data-role='button' data-icon='thumbs-down' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type; ?>_unlikes_<?php echo $this->resource_id;?>" style ='display:<?php echo $hasLike ?"block":"none"?>'>
										<i class="seaocore_like_thumbdown_icon"></i>
										<span><?php echo $this->translate('Unlike') ?></span>
									</a>
									<a href = "javascript://" onclick = "seaocore_content_type_likes_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');" data-role='button' data-icon='thumbs-up' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type; ?>_most_likes_<?php echo $this->resource_id;?>" style ='display:<?php echo empty($hasLike) ?"block":"none"?>'>
										<i class="seaocore_like_thumbup_icon"></i>
										<span><?php echo $this->translate('Like') ?></span>
									</a>
									<input type ="hidden" id = "<?php echo $this->resource_type; ?>_like_<?php echo $this->resource_id;?>" value = '<?php echo $hasLike ? $hasLike[0]['like_id'] :0; ?>' />
								<?php endif; ?>
							</td>
            <?php endif;?>
					</tr>
				</table>  
			</div>
    <?php endif;?>
  </div>