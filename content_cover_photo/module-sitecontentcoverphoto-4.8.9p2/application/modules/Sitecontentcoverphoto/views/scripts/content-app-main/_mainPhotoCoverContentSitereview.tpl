<?php
	$sitereview = $this->subject();
	$this->cover_params = array('top' => 0, 'left' => 0);
	//POPULATE FORM
	$row = Engine_Api::_()->getDbTable('otherinfo', 'sitereview')->getOtherinfo($sitereview->listing_id);

	//POPULATE FORM
	$this->email = $row->email;
	$this->phone = $row->phone;
	$this->website = $row->website;
	$this->sitereviewTags = $sitereview->tags()->getTagMaps();
	$this->resource_id = $resource_id = $sitereview->getIdentity();
	$this->resource_type = $resource_type = $sitereview->getType();
	$this->subcategory_name = '';
	$this->subsubcategory_name = '';
	$categoriesTable = Engine_Api::_()->getDbTable('categories', 'sitereview');
	$this->category_name = $categoriesTable->getCategory($sitereview->category_id)->category_name;
	if(isset($categoriesTable->getCategory($sitereview->subcategory_id)->category_name))
	$this->subcategory_name = $categoriesTable->getCategory($sitereview->subcategory_id)->category_name;
	if(isset($categoriesTable->getCategory($sitereview->subsubcategory_id)->category_name))
	$this->subsubcategory_name = $categoriesTable->getCategory($sitereview->subsubcategory_id)->category_name;
?>
  <?php if(!empty($sitereview->sponsored) || !empty($sitereview->featured) || !empty($sitereview->newlabel) ):?>
    <div class="list-label-wrap">
      <?php if (in_array('newlabel', $this->showContent) && !empty($sitereview->newlabel)): ?>
        <span class="list-label list-label-new">
          <?php echo $this->translate('NEW'); ?>
        </span>
      <?php endif; ?>
      <?php if (in_array('sponsored', $this->showContent) && !empty($sitereview->sponsored)): ?>
        <span class="list-label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitereview.sponsored.color', '#fc0505'); ?>;'>
          <?php echo $this->translate('SPONSORED'); ?>
        </span>
      <?php endif; ?>
      <?php if (in_array('featured', $this->showContent) && !empty($sitereview->featured)): ?>
        <span class="list-label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitereview.featured.color', '#0cf523'); ?>;'>
          <?php echo $this->translate('FEATURED'); ?>
        </span>
      <?php endif; ?>
    </div>
  <?php endif; ?>

	<?php if (in_array('mainPhoto', $this->showContent) || in_array('title', $this->showContent) || in_array('category', $this->showContent) || in_array('subcategory', $this->showContent) || in_array('subsubcategory', $this->showContent)): ?>
		<div class="content_cover_head" id="siteuser_main_photo">
			<?php if (in_array('mainPhoto', $this->showContent)): ?>
				<div class="content_cover_main_photo_wrapper">
					<div class='content_cover_main_photo'>
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
				<div class="content_cover_title<?php if (!in_array('mainPhoto', $this->showContent)): ?> seaocore_profile_photo_none<?php endif; ?>">
					<?php if(in_array('title', $this->showContent)):?>
						<a href="<?php echo $sitereview->getHref(); ?>"><h2><?php echo $sitereview->getTitle(); ?></h2></a>
					<?php endif;?>
          <div class="f_small">
            <?php if(in_array('category', $this->showContent)):?>
              <?php echo $this->htmlLink($this->url(array('category_id' => $sitereview->category_id, 'categoryname' => Engine_Api::_()->getItem('sitereview_category', $sitereview->category_id)->getCategorySlug()), "sitereview_general_category_listtype_$sitereview->listingtype_id"), $this->translate($this->category_name)) ?>
            <?php endif;?>
            <?php if(in_array('subcategory', $this->showContent) && isset(Engine_Api::_()->getDbTable('categories', 'sitereview')->getCategory($sitereview->subcategory_id)->category_name)):?>
              <?php echo '&raquo;';?>  
              <?php echo $this->htmlLink($this->url(array('category_id' => $sitereview->category_id, 'categoryname' => Engine_Api::_()->getItem('sitereview_category', $sitereview->category_id)->getCategorySlug(), 'subcategory_id' => $sitereview->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitereview_category', $sitereview->subcategory_id)->getCategorySlug()), "sitereview_general_subcategory_listtype_$sitereview->listingtype_id"), $this->translate($this->subcategory_name)) ?>
            <?php endif;?>
            <?php if(in_array('subsubcategory', $this->showContent) && isset(Engine_Api::_()->getDbTable('categories', 'sitereview')->getCategory($sitereview->subsubcategory_id)->category_name)):?>
              <?php echo '&raquo;';?> 
              <?php echo $this->htmlLink($this->url(array('category_id' => $sitereview->category_id, 'categoryname' => Engine_Api::_()->getItem('sitereview_category', $sitereview->category_id)->getCategorySlug(), 'subcategory_id' => $sitereview->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitereview_category', $sitereview->subcategory_id)->getCategorySlug(),'subsubcategory_id' => $sitereview->subsubcategory_id, 'subsubcategoryname' =>  Engine_Api::_()->getItem('sitereview_category', $sitereview->subsubcategory_id)->getCategorySlug()), "sitereview_general_subsubcategory_listtype_$sitereview->listingtype_id"),$this->translate($this->subsubcategory_name)) ?>
            <?php endif;?>
            </div>
				</div>
			<?php endif;?>
		</div>
	<?php endif;?>

  <div class="content_cover_info o_hidden <?php if (!in_array('mainPhoto', $this->showContent)): ?>seaocore_profile_photo_none<?php endif; ?>">
    <?php if ($sitereview->price > 0 && in_array('price', $this->showContent)): ?>
      <p class="fright f_small">
        <b>
          <?php echo $this->locale()->toCurrency($sitereview->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')); ?>
        </b>
      </p>  
    <?php endif; ?>
    <?php if (!empty($sitereview->location) && in_array('location', $this->showContent)):?>
      <p class="t_light f_small">
        <i class="ui-icon-map-marker"></i>
        <?php echo $this->htmlLink('https://maps.google.com/?q='.urlencode($sitereview->location), $sitereview->location, array('target' => 'blank')) ?> </span>
      </p>
    <?php endif;?>
  </div>
  
  <div class="seaocore_cover_cont">
    <?php if (in_array('likeButton', $this->showContent) || in_array('reviewCreate', $this->showContent)): ?>
      <div class="seaocore_profile_cover_buttons">
        <table cellpadding="2" cellspacing="0">
          <tr>
            <?php if (in_array('likeButton', $this->showContent)):?>
              <td id="seaocore_like">
                <?php if(!empty($this->viewer_id)): ?>
                  <?php $hasLike = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($this->resource_type, $this->resource_id); ?>
                  <a href ="javascript://" onclick = "seaocore_content_type_likes_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type; ?>_unlikes_<?php echo $this->resource_id;?>" style ='display:<?php echo $hasLike ?"block":"none"?>'>
                    <i class="ui-icon ui-icon-thumbs-up-alt feed-unlike-btn"></i>
                    <span><?php echo $this->translate('Like') ?></span>
                  </a>
                  <a href = "javascript://" onclick = "seaocore_content_type_likes_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type; ?>_most_likes_<?php echo $this->resource_id;?>" style ='display:<?php echo empty($hasLike) ?"block":"none"?>'>
                    <i class="ui-icon ui-icon-thumbs-up-alt feed-like-btn"></i>
                    <span><?php echo $this->translate('Like') ?></span>
                  </a>
                  <input type ="hidden" id = "<?php echo $this->resource_type; ?>_like_<?php echo $this->resource_id;?>" value = '<?php echo $hasLike ? $hasLike[0]['like_id'] :0; ?>' />
                <?php endif; ?>
              </td>
            <?php endif;?>

            <?php if (in_array('reviewCreate', $this->showContent)):?>
              <?php $reviewButton = $this->content()->renderWidget("sitereview.review-button");?>
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

    <?php if(is_array($this->showContent) && (in_array('description', $this->showContent) || in_array('phone', $this->showContent) || in_array('website', $this->showContent) || in_array('email', $this->showContent) || in_array('tags', $this->showContent))):?>
			<div class="ui-page-content sm-widget-block content_cover_profile_fields">
        <h4><?php echo $this->translate('Details'); ?></h4>
        <ul>
          <?php if(in_array('description', $this->showContent)):?>
            <?php if(Engine_Api::_()->getDbTable('otherinfo', 'sitereview')->getColumnValue($sitereview->getIdentity(), 'about')):?>
              <li><?php echo Engine_Api::_()->getDbTable('otherinfo', 'sitereview')->getColumnValue($sitereview->getIdentity(), 'about') ?></li>
            <?php elseif(strip_tags($sitereview->body)):?>
              <li><?php echo $this->viewMore(strip_tags($sitereview->body), 300, 5000) ?></li>
            <?php endif;?>
          <?php endif; ?>
          <?php if (in_array('tags', $this->showContent) && count($this->sitereviewTags) > 0): $tagCount = 0; ?>
            <li>
              <span class="t_light"><?php echo $this->translate('Tags'); ?>:</span>
              <span><?php foreach ($this->sitereviewTags as $tag): ?>
                <?php if (!empty($tag->getTag()->text)): ?>
                  <?php if (empty($tagCount)): ?>
                    <a href='<?php echo $this->url(array('action' => 'index'), "sitereview_general_listtype_$sitereview->listingtype_id"); ?>?tag=<?php echo $tag->getTag()->tag_id ?>&tag_name=<?php  echo $tag->getTag()->text ?>'>#<?php echo $tag->getTag()->text ?></a>
                    <?php $tagCount++;
                  else: ?>
                    <a href='<?php echo $this->url(array('action' => 'index'), "sitereview_general_listtype_$sitereview->listingtype_id"); ?>?tag_id=<?php echo $tag->getTag()->tag_id ?>'>#<?php echo $tag->getTag()->text ?></a>
                  <?php endif; ?>
                <?php endif; ?>
              <?php endforeach; ?></span>
            </li>
          <?php endif; ?>
        </ul>
        <?php if(($this->phone || $this->email || $this->website) && in_array('phone', $this->showContent) || in_array('website', $this->showContent) || in_array('email', $this->showContent)):?>
          <?php if(($this->phone || $this->email || $this->website)):?>
            <h4><?php echo $this->translate("Contact Information");?></h4>
            <ul>
              <?php if(in_array('phone', $this->showContent) &&  $this->phone):?>
                <li>
                  <span class="t_light"><?php echo $this->translate("Phone")?>:</span>
                  <span> <a href="tel:<?php echo $this->phone?>"> <?php echo $sitepage->phone?> </a></span>
                </li>
              <?php endif;?>
              <?php if(in_array('email', $this->showContent) &&  $this->email):?>
                <li>
                  <span class="t_light"><?php echo $this->translate("Email")?>:</span>
                  <span> <a href='mailto:<?php echo $this->email ?>'><?php echo $this->translate('Email Me') ?></a> </span>
                </li>
              <?php endif;?>
              <?php if(in_array('website', $this->showContent) &&  $this->email):?>
                <li>
                  <span class="t_light"><?php echo $this->translate("Website")?>:</span>
                  <?php if (strstr($this->website, 'http://') || strstr($this->website, 'https://')): ?>
                  <span> <a href='<?php echo $this->website ?>' target="_blank" title='<?php echo $this->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a> </span>
                  <?php else: ?>
                  <span> 	<a href='http://<?php echo $this->website ?>' target="_blank" title='<?php echo $this->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a> </span>
                  <?php endif; ?>
                </li>
              <?php endif;?>
            </ul>	
          <?php endif;?>
        <?php endif;?>
      </div>
    <?php endif;?>	
  </div>
