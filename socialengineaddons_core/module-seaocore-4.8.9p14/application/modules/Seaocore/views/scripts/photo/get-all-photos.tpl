<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: light-box-view.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<ul class="sea_val_photos_thumbs">	  
  <?php foreach ($this->paginator as $photo): ?>
  	<li id="lb-all-thumbs-photo-<?php echo $photo->getIdentity() ?>" onclick="onclickPhotoThumb($(this)); photopaginationSocialenginealbum('<?php echo $this->escape($photo->getHref()) ?>','','<?php echo $photo->getPhotoUrl(); ?>');closeAllPhotoContener();" >     
      <a class="thumbs_photo"  onclick="onclickPhotoThumb($(this)); photopaginationSocialenginealbum('<?php echo $this->escape($photo->getHref()) ?>','','<?php echo $photo->getPhotoUrl(); ?>');closeAllPhotoContener();" >
        <span class="lightbox_thumb" style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);">
        	<?php if(isset ($photo->comment_count) && !empty($photo->comment_count)): ?>
		        <span class="sea_plcmc">
		        	<span class="sea_plcmc_b">
		        		<span class="sea_plcmc_bb"><?php echo $photo->comment_count; ?></span>
		        		<span class="sea_plcmc_bc"></span>
		        	</span>
		        	<span class="sea_plcmc_f">
		        		<span class="sea_plcmc_fb"><?php echo $photo->comment_count; ?></span>
		        		<span class="sea_plcmc_fc"></span>
		        	</span>
	        	</span>
	      	<?php endif ?>
	      	<?php if(isset ($photo->like_count) && !empty($photo->like_count)): ?>
        		<span class="sea_plcml">
		        	<span class="sea_plcml_f">
		        		<span class="sea_plcmc_fb"><?php echo $photo->like_count; ?></span>
		        	</span>
	        	</span>
	        <?php endif ?>
        </span>
      </a>       
    </li>     
  <?php endforeach; ?>	 
</ul>