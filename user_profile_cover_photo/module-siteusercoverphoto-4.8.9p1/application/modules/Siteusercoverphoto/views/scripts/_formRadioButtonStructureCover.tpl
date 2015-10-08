<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formRadioButtonStructureCover.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$check_1 = 'unchecked';
$check_0 = 'unchecked';
$leftExtendablelink = 'https://lh4.googleusercontent.com/-l2-mfl2aD5w/UbXjB46rAGI/AAAAAAAAAZU/Cy1eP1S0zUI/s912/reightside.jpg';
$rightExtendablelink = 'https://lh4.googleusercontent.com/-FDf-oPWMh7k/UbXkmTKVcPI/AAAAAAAAAZ0/XrTr1db9NTU/s912/left.jpg';
$leftExtendable = "<a href='$leftExtendablelink' target='_blank'>" . $this->translate('Left Extended Column') . "</a>";
$rightExtendable = "<a href='$rightExtendablelink' target='_blank'>" . $this->translate('Right Extended Column') . "</a>";
?>
<div id="siteusercoverphoto_layout-wrapper" class="form-wrapper">
  <div id="siteusercoverphoto_layout-label" class="form-label">
    <label class="optional" for="siteusercoverphoto_layout"><?php echo $this->translate('User Cover Photo Column'); ?></label>
  </div>
  <div id="siteusercoverphoto_layout-element" class="form-element">
    <p class="description"><?php echo $this->translate('Select a column in which user cover photo is to be placed on Member Profile pages on your site.'); ?></p>
    <ul class="form-options-wrapper">
      <?php
      echo '<li><div><input ' . $check_1 . ' id="siteusercoverphoto_layout-1" name="siteusercoverphoto_profile_layout" type="radio" value="0" ></div>' . $leftExtendable . '</li>';
      echo '<li><div><input ' . $check_0 . ' id="siteusercoverphoto_layout-0" name="siteusercoverphoto_profile_layout" type="radio" value="1"></div>' . $rightExtendable . '</li>';
      ?>
    </ul>
  </div>
</div>