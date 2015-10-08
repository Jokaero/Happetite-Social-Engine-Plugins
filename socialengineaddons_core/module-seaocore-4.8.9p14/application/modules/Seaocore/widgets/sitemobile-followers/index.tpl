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

<script type="text/javascript">
var followersSitemobile = function(call_status) {

	var urlRequest = sm4.core.baseUrl + 'core/widget/index/content_id/' + <?php echo $this->identity?>;
	if($.mobile.activePage.find("#global_page_sitepage-index-view")) {
		urlRequest = sm4.core.baseUrl + 'sitepage/mobile-widget/index/content_id/' + <?php echo $this->identity?>;
	} else if($.mobile.activePage.find("#global_page_sitebusiness-index-view")) {
		urlRequest = sm4.core.baseUrl + 'sitebusiness/mobile-widget/index/content_id/' + <?php echo $this->identity?>;
	} else if($.mobile.activePage.find("#global_page_sitegroup-index-view")) {
		urlRequest = sm4.core.baseUrl + 'sitegroup/mobile-widget/index/content_id/' + <?php echo $this->identity?>;
	}
	sm4.core.request.send({
		type: "GET", 
		dataType: "html", 
		url : urlRequest,
		data: {
			'subject': sm4.core.subject.guid != '' ? sm4.core.subject.guid : $.mobile.activePage.attr("data-subject"),  
			'format':'html',
			'call_status' : call_status,
      'isajax' : 1
		}
		},{
			'element' : $.mobile.activePage.find("#profile_pagefollwers"),
			'showLoading': true
		}
	);
};
</script>

<?php if(empty($this->isajax)):?>
	<div class="sm-content-list">
		<select name="auth_view" onchange="followersSitemobile($(this).val());" >
			<option value="public" <?php if($this->call_status == 'public'):?> selected="selected" <?php endif;?>><?php echo $this->translate("All") ?></option> 
			<option value="friend" <?php if($this->call_status == 'friend'):?> selected="selected" <?php endif;?> ><?php echo $this->translate("Friends") ?></option> 
		</select>
<?php endif;?>

<?php if($this->paginator->getTotalItemCount() > 0): ?>
	<ul id="profile_pagefollwers" data-role="listview" data-icon="arrow-r">
		<?php foreach ($this->paginator as $user): ?>
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
							$this->paginator, $this->identity, "profile_pagefollwers", array('call_status' => $this->call_status ));
			?>
		<?php endif; ?>
	</ul>
<?php else:?>
	<div class="tip">
		<span>
			<?php echo $this->translate("No such members following this.");?> 
		</span>
	</div>
<?php endif; ?>

<?php if(empty($this->isajax)):?>
	</div>
<?php endif;?>