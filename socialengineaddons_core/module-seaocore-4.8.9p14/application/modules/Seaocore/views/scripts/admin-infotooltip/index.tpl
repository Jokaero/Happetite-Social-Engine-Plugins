<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 8988 2011-06-15 01:35:25Z john $
 * @author     Sami
 */
?>
<h2>
  <?php echo $this->translate('SocialEngineAddOns Core Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render();  ?>
  </div>
<?php endif; ?>

<?php
  $pageURL ='http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin';
  $this->form->setTitle('Info Tooltip Settings');
  $this->form->setDescription($this->translate('Info Tooltips are interactive mouse-over tooltips for sources and entities in the site activity feeds from <a href="%s" target="_blank">"Advanced Activity Feeds / Wall Plugin"</a>. They contain information and quick action links for the entities, thus enabling easier interaction and good user experience on your website. Below, you can customize the Info Tooltips by choosing the Action Links and Information that should be shown in them.', $pageURL));
  $this->form->getDecorator('Description')->setOption('escape', false);
?>
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity') ) : ?>
<div class="seaocore_settings_form">
    <div class='settings'>
      <?php echo $this->form->render($this); ?>
    </div>
</div>
<?php else : ?>
<div class="tip">
  <span>  
<?php echo $this->translate('Your site does not have the <a href="%s" target="_blank">"Advanced Activity Feeds / Wall Plugin"</a> installed and enabled on it. The interactive Info Tooltips are available in that plugin. Thus, please install and enable that plugin on your website to get the Info Tooltips.', $pageURL); ?>
  </span>
</div>
<?php endif; ?>



