<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if(!$this->identity): $this->identity= rand(1000000000, 9999999999); endif;?>

<div class="cmad_ad_clm">
	<?php include APPLICATION_PATH . '/application/modules/Communityad/views/scripts/_adsDisplay.tpl';?>
</div>