<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: uploads.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
if (window.parent.$('file_upload')) {
window.parent.$('uccess_fileupload_parent_sugg').style.display = 'block';
window.parent.$('file_upload').value = '<?php echo $this->filename;?>';
window.parent.$('success_fileupload').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Your file has been uploaded successfully. Please click on the button below to invite contacts from it.'));?>';
}
parent.Smoothbox.close();
</script>