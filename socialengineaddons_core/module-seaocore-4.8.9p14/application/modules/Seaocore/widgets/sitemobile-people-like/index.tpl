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

<div class="sm-content-list" id="profile_pagelikes">
	<h4><?php echo $this->translate(array('%s Person Likes This', '%s People Like This', $this->paginator->getTotalItemCount()),$this->locale()->toNumber($this->paginator->getTotalItemCount())); ?></h4>
	<ul data-role="listview" data-icon="arrow-r">
		<?php foreach ($this->paginator as $paginator): ?>
      <?php $user = Engine_Api::_()->getItem('user', $paginator->poster_id); ?>
			<li>
				<a href="<?php echo $user->getHref(); ?>">
					<?php echo $this->itemPhoto($user, 'thumb.icon'); ?>
					<h3><?php echo $user->getTitle() ?></h3>
				</a> 
			</li>
		<?php endforeach; ?>
		<?php if ($this->paginator->count() > 1): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->paginator, $this->identity, "profile_pagelikes");
			?>
		<?php endif; ?>
	</ul>
</div>