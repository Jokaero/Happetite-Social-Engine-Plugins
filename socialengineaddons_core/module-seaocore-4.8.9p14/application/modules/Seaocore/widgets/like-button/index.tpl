<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Seaocore
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>

<script type="text/javascript">
	var seaocore_content_type = '<?php echo $this->resource_type; ?>';
	var seaocore_like_url = en4.core.baseUrl + 'seaocore/like/like';
</script>

<?php if(!empty($this->viewer_id)): ?>
	<?php $hasLike = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($this->resource_type, $this->resource_id); ?>
	<div class="seaocore_like_button" id="<?php echo $this->resource_type; ?>_unlikes_<?php echo $this->resource_id;?>" style ='display:<?php echo $hasLike ?"inline-block":"none"?>' >
		<a href = "javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');">
			<i class="seaocore_like_thumbdown_icon"></i>
			<span><?php echo $this->translate('Unlike') ?></span>
		</a>
	</div>
	<div class="seaocore_like_button" id="<?php echo $this->resource_type; ?>_most_likes_<?php echo $this->resource_id;?>" style ='display:<?php echo empty($hasLike) ?"inline-block":"none"?>'>
		<a href = "javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');">
			<i class="seaocore_like_thumbup_icon"></i>
			<span><?php echo $this->translate('Like') ?></span>
		</a>
	</div>
	<input type ="hidden" id = "<?php echo $this->resource_type; ?>_like_<?php echo $this->resource_id;?>" value = '<?php echo $hasLike ? $hasLike[0]['like_id'] :0; ?>' />
<?php endif; ?>