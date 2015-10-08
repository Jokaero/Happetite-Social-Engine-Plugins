<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitefollow
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
	seaocore_content_type = '<?php echo $this->resource_type; ?>';
</script>
<?php 
	$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');

	$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/follow.js'); 
?>
<?php if ($this->viewer_id): ?>
		<?php $isFollow = $this->subject->follows()->isFollow($this->viewer); ?>
		<div class="seaocore_follow_button_wrap fleft button seaocore_follow_button_active" id="<?php echo $this->resource_type ?>_unfollows_<?php echo $this->resource_id;?>" style =' display:<?php echo $isFollow ?"inline-block":"none"?>' >
			<a class="seaocore_follow_button seaocore_follow_button_following" href="javascript:void(0);">
				<i class="following"></i>
				<span><?php echo $this->translate('Following') ?></span>
			</a>
			
			<a class="seaocore_follow_button seaocore_follow_button_unfollow" href="javascript:void(0);" onclick = "seaocore_content_type_follows('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');">
				<i class="unfollow"></i>
				<span><?php echo $this->translate('Unfollow') ?></span>
			</a>
			
		</div>
		<div class="seaocore_follow_button_wrap fleft" id="<?php echo $this->resource_type ?>_most_follows_<?php echo $this->resource_id;?>" style ='display:<?php echo empty($isFollow) ?"inline-block":"none"?>'>
			<a class="seaocore_follow_button" href="javascript:void(0);" onclick = "seaocore_content_type_follows('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');">
				<i class="follow"></i>
				<span><?php echo $this->translate('Follow') ?></span>
			</a>
		</div>
		<input type ="hidden" id = "<?php echo $this->resource_type; ?>_follow_<?php echo $this->resource_id;?>" value = '<?php echo $isFollow ? $isFollow :0; ?>' />
		<div class="seaocore_follower_count fleft"  id= "<?php echo $this->resource_type ?>_num_of_follow_<?php echo $this->resource_id;?>">
			<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'follow', 'action'=>'get-followers', 'resource_type'	=> $this->resource_type, 'resource_id' => $this->resource_id, 'format' => 'smoothbox', 'call_status' => 'public'), 'default'	, true)); ?>'); return false;" ><?php echo $this->translate(array('%s follower', '%s followers', $this->follow_count),$this->locale()->toNumber($this->follow_count)); ?></a>			
		</div>
		
<?php endif; ?>
<script type="text/javascript">
	function showSmoothBox() {
		Smoothbox.open('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'follow', 'action'=>'get-followers', 'resource_type'	=> $this->resource_type, 'resource_id' => $this->resource_id, 'format' => 'smoothbox', 'call_status' => 'public'), 'default'	, true)); ?>');
	}
</script>