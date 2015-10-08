<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload-cover-photo.tpl 6590 2013-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$tablePrimaryFieldName = $this->tablePrimaryFieldName;
$moduleName = $this->moduleName;
$fieldName = $this->fieldName;
?>

<?php if ($this->status): ?>
  <script type="text/javascript">
    getCoverPhoto();
    function getCoverPhoto() {
      window.location.href= '<?php echo $this->subject()->getHref(); ?>';
    }
  </script>
<?php endif; ?>