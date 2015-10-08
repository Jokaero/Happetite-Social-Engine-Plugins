<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: social-share.tpl 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
    $this->form->setTitle('Social Share Code');
    $this->form->setDescription("Personalize the code for social sharing buttons by adding your own code generated from: <a href='http://www.addthis.com/' target='_blank'>http://www.addthis.com/</a>. If you do not want to show these buttons, then you can simply empty this field.");
    $this->form->getDecorator('Description')->setOption('escape', false);
?>

<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<?php if( @$this->closeSmoothbox ): ?>
	<script type="text/javascript">
  	TB_close();
	</script>
<?php endif; ?>

<style type="text/css">
.form-label{display:none;}
</style>    
