<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<style type="text/css">
ul.seaocore_browse *
{
	font-family:arial;
}
ul.seaocore_browse > li
{
  clear: both;
  overflow: hidden;
  padding-bottom: 15px;
}
ul.seaocore_browse > li + li
{
  padding-top: 15px;
  border-top-width: 1px;
}
ul.seaocore_browse .seaocore_photo
{
  float: left;
  overflow: hidden;
}
ul.seaocore_browse .seaocore_photo img
{
  width: 100px;
  display: block;
}
ul.seaocore_browse .seaocore_info
{
  padding-left: 10px;
  overflow: hidden;
}
ul.seaocore_browse .seaocore_title h3
{
  margin: 0px;
  color:#5F93B4;
}
ul.seaocore_browse .seaocore_title h3 a
{
  color:#5F93B4;
  font-weight:bold;
}
ul.seaocore_browse .seaocore_stat
{
  font-size: .8em;
}
ul.seaocore_browse .seaocore_stat b
{
	font-weight:bold;
}
ul.seaocore_browse .seaocore_desc
{
  margin-top: 5px;
  clear: both;
}
ul.seaocore_browse .seaocore_options
{
  float: right;
  overflow: hidden;
  width:200px;
  padding-left: 15px;
}
ul.seaocore_browse .seaocore_options a
{
  clear: both;
  margin: 5px 0px 0px 0px;
  padding-top: 1px;
  height: 16px;
  font-size:11px;
  font-weight:bold;
  float:left;
  background-repeat:no-repeat;
  padding-left:22px;
  color:#5F93B4;
}
ul.seaocore_browse .seaocore_options a.seaocore_type_photo
{
  background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/photo.png);
}
ul.seaocore_browse .seaocore_options a.seaocore_type_seaocore
{
  background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/seaocore-icon.png);
}
ul.seaocore_browse .seaocore_options a.seaocore_type_se
{
  background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/se-icon.png);
}
</style>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/scripts/slimbox.js"></script>
<link rel="stylesheet" href="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/styles/slimbox.css" type="text/css" media="screen" />
<h3>
	<?php echo $this->translate("SocialEngineAddOns Plugins Information") ?>
</h3>
<br />
<div class="admin_search">
  <div class="clear">
    <div class="search">
      <form method="post" action="" class="global_form_box" enctype="" id="filter_form" name="form1">
        <div style="padding-top:5px;">
          <label class="" tag="" for="level_id">Plugins :</label>
        </div>
        <div>  
					<?php 
						if( $this->show_table == 1 ) {  $all_plugin = 'selected'; }
						else if( $this->show_table == 2 ) { $install_plugin = 'selected';  }
						else if( $this->show_table == 3 ) { $notinstall_plugin = 'selected'; }
					?>
          <select id="level_id" name="level_id" onchange="document.form1.submit();">
            <option label="" value="1" <?php if( !empty($all_plugin) ) { echo $all_plugin; } ?>><?php echo $this->translate('All Plugins'); ?> </option>
            <option label="" value="2" <?php if( !empty($install_plugin) ) { echo $install_plugin; } ?> ><?php echo $this->translate('Plugins installed on your site') ?></option>
            <option label="" value="3" <?php if( !empty($notinstall_plugin) ) { echo $notinstall_plugin; } ?> ><?php echo $this->translate('Plugins not installed on your site') ?></option>
          </select>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="tabs" style="height:10px;"></div>

		<ul class='seaocore_browse'>
			<?php $product_id = 1; ?>

			<?php 
				// No plugin installed of 'SocialEngineAddOns' in the site then show the msg.
				if( !empty($install_plugin) ) {
					if( empty($this->channel) ) {
				echo '<div class="tip"><span>No plugins by SocialEngineAddOns were found on your site. Click <a href="http://www.socialengineaddons.com/catalog/1/plugins" target="_blank">here</a> to view and purchase them.</span></div>';
					}
				}  
				?>
				<?php foreach( $this->channel as $item ): ?>
					<?php if( !empty($item) ) {
					if( empty($item['running_version']) ) {
						$should_do = '<a href="'.$item['link'].'" class="seaocore_type_seaocore" target="_blank">' . $this->translate('Purchase and Download') . '</a>';
						$running_version = 0;
					} else {
							$running_version = $item['running_version'];
							$product_version = $item['product_version'];
							$versionInfo = strcasecmp($running_version, $product_version);
							if( $versionInfo < 0 ) {
							$should_do = '<a href="http://www.socialengineaddons.com/user" class="seaocore_type_seaocore" target="_blank">' . $this->translate('Download Latest Version') . '</a>';
						} else {
							$should_do = '<a href="'.$item['link'].'" class="seaocore_type_seaocore" target="_blank">' . $this->translate('View') . '</a>';
						}
						$running_version = $item['running_version'];
					}
					?>
        <li>
          <div class="seaocore_photo">
          <?php echo '<a rel="lightbox-atomium_'.$product_id.'" title="'.$item['title'].'" href="'.$item['image'][0].'"><img src="'.$item['image'][0].'" width="50" /></a>';
					$check_image = 0;
					foreach( $item['image'] as $image ) {
						if ( !empty($check_image) ) {
							echo '<a rel="lightbox-atomium_'.$product_id.'" title="'.$item['title'].'" href="'.$image.'"></a>';
						} $check_image ++;
					}
				?>
          </div>
          <div class="seaocore_options">
						<?php
							if ( !empty($item['image']) ) {
								echo '<a class="seaocore_type_photo" rel="lightbox-atomium_'.$product_id.'" title="'.$item['title'].'" href="'.$item['image'][0].'"> ' . $this->translate('Photos') . ' </a>';
								$check_image = 0;
								foreach( $item['image'] as $image ) {
									if ( !empty($check_image) ) {
										echo '<a rel="lightbox-atomium_'.$product_id.'" title="'.$item['title'].'" href="'.$image.'" style="display:none;"></a>';
									} $check_image ++;
								}
							}
					  ?>
						<?php echo '<a href="http://demo.socialengineaddons.com" class="seaocore_type_seaocore" target="_blank">' . $this->translate('Demo') . '</a>'; ?>
          	<?php if ( !empty($should_do) ) { echo $should_do; } ?>
						<?php echo '<a href="'.$item['socialengine_url'].'" class="seaocore_type_se" target="_blank">' . $this->translate('SocialEngine Plugin Page') . '</a>'; ?>
          </div>
          <div class="seaocore_info">
            <div class="seaocore_title">
              <h3><a href="<?php echo $item['link'] ?>" target="_blank"><?php echo $item['title'] ?></a></h3>
            </div>
				    <div class="seaocore_stat">
							<?php 
								if (!empty($item['product_version']) && !empty($running_version)) {
									$show_label = Zend_Registry::get('Zend_Translate')->_('Current Product Version: <b>%s</b>');
									$show_label = sprintf($show_label, $item['product_version']);
									echo $show_label; 
								} ?>
				    </div>
            <div class="seaocore_stat">
							<?php
								if (!empty($running_version)) {
									$show_label = Zend_Registry::get('Zend_Translate')->_('Running Version: <b>%s</b>');
									$show_label = sprintf($show_label, $running_version);
									echo $show_label; 
								} ?>
            </div>
            <div class="seaocore_stat">
							<?php
								if (!empty($item['key'])) {
									$show_label = Zend_Registry::get('Zend_Translate')->_('Key: <b>%s</b>');
									$show_label = sprintf($show_label, $item['key']);
									echo $show_label; 
								} ?>
            </div>
            <div class="seaocore_stat">
							<?php
								if (!empty($item['price'])) {
									$show_label = Zend_Registry::get('Zend_Translate')->_('Price: <b>%s</b>');
									$show_label = sprintf($show_label, $item['price']);
									echo $show_label; 
								} ?>
            </div>	
            
            <div class="seaocore_desc">
              <?php echo $item['description'] . '<a href="' . $item['link'] . '" target="_blank">More >></a>'; ?>
            </div>
          </div>
        </li>
			<?php  } $product_id++; ?>
			<?php endforeach; ?>  
    </ul>
