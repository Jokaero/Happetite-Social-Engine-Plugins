<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload-cover-photo.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if ($this->status): ?>
  <script type="text/javascript">
		getCoverPhoto();
    function getCoverPhoto() {

       $.mobile.changePage('<?php echo $this->user->getHref();?>';);

    }
	</script>
<?php endif; ?>