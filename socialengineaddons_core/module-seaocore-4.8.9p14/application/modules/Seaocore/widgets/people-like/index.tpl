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
<?php $this->headLink()->appendStylesheet($this->seaddonsBaseUrl(). '/application/modules/Seaocore/externals/styles/styles.css'); ?>
<h3>
<?php if ($this->like_count == 1) {
echo $this->translate("%s PERSON_LIKE_THIS", $this->like_count);
} else {
echo $this->translate("%s PEOPLE_LIKE_THIS", $this->like_count);
}
?>
</h3>
<ul class="seaocore_like_users_block">
	<?php	
		$container = 1;
		foreach( $this->results as $path_info ) {
		if ($container %3 == 1) : ?>
			<li>
		<?php endif;?>
			<div class="likes_member_seaocore">
				<div class="likes_member_thumb">
					<?php echo $this->htmlLink($path_info->getHref(), $this->itemPhoto($path_info, 'thumb.icon'), array('class' => 'item_photo','title' => $path_info->getTitle(), 'target' => '_parent')); ?>
				</div>
 				<div class="likes_member_name">
					<?php echo $this->htmlLink($path_info->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($path_info->getTitle(),19), array('title' => $path_info->getTitle(), 'target' => '_parent'));?>
				</div>
			</div>		
		<?php if ($container %3 == 0) : ?>
			</li>
	 	<?php endif;?>	
 		<?php $container++ ; } ?>
	<li>
		<div class="seaocore_like_users_block_links">
			<?php if( !empty($this->detail) )	{
				echo '<a class="smoothbox fright" href="' . $this->url(array('module' => 'seaocore', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $this->resource_type, 'resource_id' => $this->resource_id, 'call_status' => 'public'), 'default', true) . '">' . $this->translate('See all') . '</a>';
			}	?>
		</div>	
	</li>
</ul>