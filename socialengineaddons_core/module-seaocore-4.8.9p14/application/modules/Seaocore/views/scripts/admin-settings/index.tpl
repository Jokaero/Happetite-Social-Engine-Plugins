<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: news.tpl 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
 $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl.'application/modules/Seaocore/externals/scripts/mooRainbow.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl.'application/modules/Seaocore/externals/styles/mooRainbow.css');
?>
<h2>
  <?php echo $this->translate('SocialEngineAddOns Core Plugin') ?>
</h2>
<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<div class="seaocore_settings_form">
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>