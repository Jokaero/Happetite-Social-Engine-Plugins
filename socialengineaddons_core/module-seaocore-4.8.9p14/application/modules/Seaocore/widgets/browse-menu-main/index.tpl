<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2015-05-15 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="seao_browse_btn">
    <?php $key = 0; ?>
    <a href=""><?php echo $this->translate('Browse'); ?></a>
    <div class="seao_menu_toggle" id="seao_flip" onclick="showSlideIn();">≡</div>
    <ul class='navigation' id="seao_flop_toggle">
        <?php foreach ($this->browsenavigation as $nav): ?>

            <?php if (isset($nav->show_to_guest) && empty($nav->show_to_guest) && !$this->viewer()->getIdentity()): ?>
                <?php continue; ?>
            <?php endif; ?>

            <?php if ($key < $this->max): ?>
                <li 
                <?php
                if ($nav->active): echo "class='active'";
                endif;
                ?> >
                    <?php if ($nav->action): ?>
                        <a class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array('action' => $nav->action), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                    <?php else :  ?>
                        <a class= "<?php echo $nav->class ?>" href='<?php echo $nav->getHref() ?>'><?php echo $this->translate($nav->label); ?></a>
                <?php endif; ?>
                </li>
            <?php else: ?>
                <?php break; ?>
            <?php endif; ?>
            <?php $key++ ?>
<?php endforeach; ?>
    </ul>
</div>

<script type="text/javascript">
//var myFx = new Fx.Slide('seao_flop_toggle').hide();
    function showSlideIn() {
        if($('seao_flop_toggle').style.display == '' || $('seao_flop_toggle').style.display == 'none') {
            $('seao_flop_toggle').style.display = 'block';
        } else {
            $('seao_flop_toggle').style.display = 'none';
        }
    }
</script>