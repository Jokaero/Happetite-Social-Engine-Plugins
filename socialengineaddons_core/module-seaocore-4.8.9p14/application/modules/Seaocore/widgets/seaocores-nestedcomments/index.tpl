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
<?php 
	$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js');
?>
<?php echo $this->action("list", "nestedcomment", "seaocore", array("type" => $this->subject()->getType(), "id" => $this->subject()->getIdentity())); ?>

<script type="text/javascript">
var seaocore_content_type = '<?php echo $this->subject->getType(); ?>';
</script>