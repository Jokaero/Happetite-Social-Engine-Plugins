<?php
	$sitestoreproduct= $this->subject();
	$this->cover_params = array('top' => 0, 'left' => 0);
	//POPULATE FORM
	$row = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getOtherinfo($sitestoreproduct->product_id);

	//POPULATE FORM
	$this->email = $row->email;
	$this->phone = $row->phone;
	$this->website = $row->website;
	$this->sitestoreproductTags = $sitestoreproduct->tags()->getTagMaps();
	$this->resource_id = $resource_id = $sitestoreproduct->getIdentity();
	$this->resource_type = $resource_type = $sitestoreproduct->getType();
	$this->subcategory_name = '';
	$this->subsubcategory_name = '';
	$categoriesTable = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct');
	$this->category_name = $categoriesTable->getCategory($sitestoreproduct->category_id)->category_name;
	if(isset($categoriesTable->getCategory($sitestoreproduct->subcategory_id)->category_name))
	$this->subcategory_name = $categoriesTable->getCategory($sitestoreproduct->subcategory_id)->category_name;
	if(isset($categoriesTable->getCategory($sitestoreproduct->subsubcategory_id)->category_name))
	$this->subsubcategory_name = $categoriesTable->getCategory($sitestoreproduct->subsubcategory_id)->category_name;
?>

<div class="seaocore_profile_cover_head_section <?php if (!in_array('mainPhoto', $this->showContent)): ?>seaocore_profile_photo_none<?php endif; ?>" id="siteuser_main_photo">
	<?php if (in_array('mainPhoto', $this->showContent) || in_array('title', $this->showContent) || in_array('category', $this->showContent) || in_array('subcategory', $this->showContent) || in_array('subsubcategory', $this->showContent)): ?>
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

			<?php if (in_array('title', $this->showContent) || in_array('category', $this->showContent) || in_array('subcategory', $this->showContent) || in_array('subsubcategory', $this->showContent)): ?>
				<div class="seaocore_profile_cover_title">
					<?php if(in_array('title', $this->showContent)):?>
						<a href="<?php echo $sitestoreproduct->getHref(); ?>"><h2><?php echo $sitestoreproduct->getTitle(); ?></h2></a>
					<?php endif;?>
				<div class="seaocore_txt_light" style="font-size:12px;" >
					<?php if(in_array('category', $this->showContent)):?>
						<?php echo $this->htmlLink($this->url(array('category_id' => $sitestoreproduct->category_id, 'categoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $sitestoreproduct->category_id)->getCategorySlug()), "sitestoreproduct_general_category"), $this->translate($this->category_name)) ?>
					<?php endif;?>
					<?php if(in_array('subcategory', $this->showContent) && isset(Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getCategory($sitestoreproduct->subcategory_id)->category_name)):?>
						<?php echo '&raquo;';?>  
						<?php echo $this->htmlLink($this->url(array('category_id' => $sitestoreproduct->category_id, 'categoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $sitestoreproduct->category_id)->getCategorySlug(), 'subcategory_id' => $sitestoreproduct->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $sitestoreproduct->subcategory_id)->getCategorySlug()), "sitestoreproduct_general_subcategory"), $this->translate($this->subcategory_name)) ?>
					<?php endif;?>
					<?php if(in_array('subsubcategory', $this->showContent) && isset(Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getCategory($sitestoreproduct->subsubcategory_id)->category_name)):?>
						<?php echo '&raquo;';?> 
						<?php echo $this->htmlLink($this->url(array('category_id' => $sitestoreproduct->category_id, 'categoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $sitestoreproduct->category_id)->getCategorySlug(), 'subcategory_id' => $sitestoreproduct->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitestoreproduct_category', $sitestoreproduct->subcategory_id)->getCategorySlug(),'subsubcategory_id' => $sitestoreproduct->subsubcategory_id, 'subsubcategoryname' =>  Engine_Api::_()->getItem('sitestoreproduct_category', $sitestoreproduct->subsubcategory_id)->getCategorySlug()), "sitestoreproduct_general_subsubcategory"),$this->translate($this->subsubcategory_name)) ?>
					<?php endif;?>
					</div>
				</div>
			<?php endif;?>
		</div>
	<?php endif;?>
	<?php if(in_array('description', $this->showContent) || in_array('phone', $this->showContent) || in_array('website', $this->showContent) || in_array('email', $this->showContent) || in_array('sponsored', $this->showContent) || in_array('featured', $this->showContent) || in_array('new', $this->newlabel) || in_array('tags', $this->showContent) || in_array('location', $this->showContent) || in_array('price', $this->showContent)):?>
		<div class="ui-group-content">
			<?php if (in_array('tags', $this->showContent) && count($this->sitestoreproductTags) > 0): $tagCount = 0; ?>
				<div class="siteuser_cover_profile_fields">
					<ul>
						<li>
							<span><?php echo $this->translate('Tags'); ?>:</span>
							<span><?php foreach ($this->sitestoreproductTags as $tag): ?>
								<?php if (!empty($tag->getTag()->text)): ?>
									<?php if (empty($tagCount)): ?>
										<a href='<?php echo $this->url(array('action' => 'index'), "sitestoreproduct_general"); ?>?tag=<?php echo $tag->getTag()->tag_id ?>&tag_name=<?php  echo $tag->getTag()->text ?>'>#<?php echo $tag->getTag()->text ?></a>
										<?php $tagCount++;
									else: ?>
										<a href='<?php echo $this->url(array('action' => 'index'), "sitestoreproduct_general"); ?>?tag_id=<?php echo $tag->getTag()->tag_id ?>'>#<?php echo $tag->getTag()->text ?></a>
									<?php endif; ?>
								<?php endif; ?>
							<?php endforeach; ?></span>
						</li>
					</ul>
				</div>
			<?php endif; ?>
			<?php if (!empty($sitestoreproduct->location) && in_array('location', $this->showContent)):?>
				<div class="siteuser_cover_profile_fields">
					<ul>
							<li>
								<span><?php echo $this->translate("Location")?>:</span>
								<span> <?php echo $this->htmlLink('https://maps.google.com/?q='.urlencode($sitestoreproduct->location), $sitestoreproduct->location, array('target' => 'blank')) ?> </span>
							</li>
					</ul>
				</div>
			<?php endif;?>
			<?php if ($sitestoreproduct->price > 0 && in_array('price', $this->showContent)): ?>
				<div class="siteuser_cover_profile_fields">
					<ul>
						<li>
							<span><?php echo $this->translate('Price'); ?>:</span>
							<span>
								<b>
									<?php echo $this->locale()->toCurrency($sitestoreproduct->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')); ?>
								</b>
							</span>
						</li> 
					</ul>
				</div>  
			<?php endif; ?> 

			<?php if(($this->phone || $this->email || $this->website) && in_array('phone', $this->showContent) || in_array('website', $this->showContent) || in_array('email', $this->showContent)):?>
				<?php if(($this->phone || $this->email || $this->website)):?>
					<div class="siteuser_cover_profile_fields">
						<h4>
							<span><?php echo $this->translate("Contact Information");?></span>
						</h4>
						<ul>
							<?php if(in_array('phone', $this->showContent) &&  $this->phone):?>
								<li>
									<span><?php echo $this->translate("Phone")?>:</span>
									<span> <a href="tel:<?php echo $this->phone?>"> <?php echo $sitepage->phone?> </a></span>
								</li>
							<?php endif;?>
							<?php if(in_array('email', $this->showContent) &&  $this->email):?>
								<li>
									<span><?php echo $this->translate("Email")?>:</span>
									<span> <a href='mailto:<?php echo $this->email ?>'><?php echo $this->translate('Email Me') ?></a> </span>
								</li>
							<?php endif;?>
							<?php if(in_array('website', $this->showContent) &&  $this->email):?>
								<li>
									<span><?php echo $this->translate("Website")?>:</span>
									<?php if (strstr($this->website, 'http://') || strstr($this->website, 'https://')): ?>
									<span> <a href='<?php echo $this->website ?>' target="_blank" title='<?php echo $this->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a> </span>
									<?php else: ?>
									<span> 	<a href='http://<?php echo $this->website ?>' target="_blank" title='<?php echo $this->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a> </span>
									<?php endif; ?>
								</li>
							<?php endif;?>
						</ul>
					</div>	
				<?php endif;?>
			<?php endif;?>

			<?php if(in_array('description', $this->showContent)):?>
				<?php if(Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getColumnValue($sitestoreproduct->getIdentity(), 'about')):?>
						<div><?php echo Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getColumnValue($sitestoreproduct->getIdentity(), 'about') ?></div>
				<?php elseif(strip_tags($sitestoreproduct->body)):?>
						<div><?php echo $this->viewMore(strip_tags($sitestoreproduct->body), 300, 5000) ?></div>
				<?php endif;?>
			<?php endif; ?>
		</div>
	<?php endif;?>	
	<?php if(!empty($sitestoreproduct->sponsored) || !empty($sitestoreproduct->featured) || !empty($sitestoreproduct->newlabel) ):?>
		<table cellpadding="2" cellspacing="0" style="width:100%">
			<tr>
				<?php if (in_array('newlabel', $this->showContent) && !empty($sitestoreproduct->newlabel)): ?>
					<td style="width:33.33%;">
						<div class="sm-sl" style='background-color:orange'>
							<?php echo $this->translate('NEW'); ?>
						</div>
					</td>
				<?php endif; ?>
				<?php if (in_array('sponsored', $this->showContent) && !empty($sitestoreproduct->sponsored)): ?>
					<td style="width:33.33%;">
						<div class="sm-sl" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.sponsored.color', '#fc0505'); ?>;'>
							<?php echo $this->translate('SPONSORED'); ?>
						</div>
					</td>
				<?php endif; ?>
				<?php if (in_array('featured', $this->showContent) && !empty($sitestoreproduct->featured)): ?>
					<td style="width:33.33%;">
						<div class="sm-sl" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.featured.color', '#0cf523'); ?>;'>
							<?php echo $this->translate('FEATURED'); ?>
						</div>
					</td>
				<?php endif; ?>
			</tr>
		</table>
	<?php endif; ?>
	<?php if (in_array('likeButton', $this->showContent) || in_array('reviewCreate', $this->showContent)): ?>
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

					<?php if (in_array('reviewCreate', $this->showContent)):?>
						<?php $reviewButton = $this->content()->renderWidget("sitestoreproduct.review-button");?>
						<?php $reviewButtonLength = strlen($reviewButton);?>
						<?php if($reviewButtonLength > 13):?>
							<td>
								<?php echo $reviewButton; ?>
							</td>
						<?php endif; ?>
					<?php endif; ?>
				</tr>
			</table>  
		</div>
	<?php endif;?>
</div>