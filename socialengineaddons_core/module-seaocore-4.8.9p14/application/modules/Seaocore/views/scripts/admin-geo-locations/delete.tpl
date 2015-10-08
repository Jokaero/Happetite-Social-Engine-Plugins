<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 6590 2014-06-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
    <div>
        <h3><?php echo $this->translate('Delete Location ?'); ?></h3>
        <p>
            <?php echo $this->translate('Are you sure that you want to delete this Location ? It will not be recoverable after being deleted.'); ?>
        </p>
        <br />
        <p>
            <input type="hidden" name="confirm" value="<?php echo $this->locationcontent_id ?>"/>
            <button type='submit'><?php echo $this->translate('Delete'); ?></button>
            or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
        </p>
    </div>
</form>

<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>
