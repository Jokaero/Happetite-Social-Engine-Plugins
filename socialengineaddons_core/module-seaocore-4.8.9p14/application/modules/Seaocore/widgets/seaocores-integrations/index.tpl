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
.seaocore_list_head{
	margin-bottom:10px;
}
ul.seaocore_browse *{
	font-family:arial;
}
ul.seaocore_browse > li{
  clear: both;
  overflow: hidden;
  padding-bottom: 15px;
}
ul.seaocore_browse > li + li{
  padding-top: 15px;
  border-top-width: 1px;
}
ul.seaocore_browse .seaocore_photo{
  float: left;
  overflow: hidden;
  margin-right:10px;
}
ul.seaocore_browse .seaocore_photo img{
  width: 100px;
  display: block;
}
ul.seaocore_browse .seaocore_info{
  overflow: hidden;
}
ul.seaocore_browse .seaocore_title h3{
  margin:0 0 5px;
  font-size:15px;
}
ul.seaocore_browse .seaocore_title h3 a{
  font-weight:bold;
}
ul.seaocore_browse .seaocore_title span{
	font-size:13px;
}
ul.seaocore_browse .seaocore_stat{
  font-size: .8em;
}
ul.seaocore_browse .seaocore_stat b{
	font-weight:bold;
}
ul.seaocore_browse .seaocore_desc{
  margin-top: 5px;
  clear: both;
}
ul.seaocore_browse .seaocore_options{
  float: right;
  overflow: hidden;
  width:200px;
  padding-left: 15px;
}
ul.seaocore_browse .seaocore_options a{
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
ul.seaocore_browse .seaocore_options a.seaocore_type_photo{
  background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/photo.png);
}
ul.seaocore_browse .seaocore_options a.seaocore_type_seaocore{
  background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/seaocore-icon.png);
}
ul.seaocore_browse .seaocore_options a.seaocore_type_se{
  background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/se-icon.png);
}
</style>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/scripts/slimbox.js"></script>
<link rel="stylesheet" href="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/styles/slimbox.css" type="text/css" media="screen" />
<h3>
	<?php echo $this->translate("SocialEngineAddOns Plugins Integration and Information") ?>
</h3>
<p><?php echo $this->translate('This page lists all the plugins from SocialEngineAddOns that are integrable and compatible with this plugin. With this integration, you can enhance the functionality of this plugin, which in turn will increase traffic on your site by providing more features to the users of your site. To know more about our plugins on your site, please go to the "Plugins Information" section of "SocialEngineAddOns Core Plugin".<br />
The latest versions of these plugins are also available to you in your SocialEngineAddOns Client Area. Login into your SocialEngineAddOns Client Area here: <a href="http://www.socialengineaddons.com/user/login">http://www.socialengineaddons.com/user/login</a>.'); ?></p>
<br style="clear:both;" />


<?php $url = $this->layout()->staticBaseUrl; ?>
<?php if($this->isReview == "sitereview"): ?>
    <div class="importlisting_form">
      <div>
    <h3 class="seaocore_list_head">  Integration with Directory / Pages Plugin </h3>

        <ul class="seaocore_browse"><li>

        <div class="seaocore_info">
          <div class="seaocore_title">
            <h3><a target="_blank" href="http://www.socialengineaddons.com/socialengine-directory-pages-plugin">Directory / Pages - Multiple Listings and Products Showcase Extension</a> <span>(Price: <b>$19.00</b>)</span>  </h3>
          </div>

          <div class="seaocore_stat">

          </div>	


          <div class="seaocore_desc">
            <p>This is a great tool which integrates "<a target='blank' href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin'>Directory / Pages Plugin</a>" and "<a target='blank' href='http://www.socialengineaddons.com/socialengine-multiple-listing-types-plugin-listings-blogs-products-classifieds-reviews-ratings-pinboard-wishlists'>Multiple Listing Types Plugin</a>". It enables Page Admins to add / link / associate related Listings to their Pages. Such listings will be displayed on the Page profiles, thus making the Pages more informative and useful.</p>
          </div>    

     <br /><b>For Example:</b> A site having Hotels can use the "<a target='blank' href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin'>Directory / Pages Plugin</a>" for creating Pages of Hotels and "<a target='blank' href='http://www.socialengineaddons.com/socialengine-multiple-listing-types-plugin-listings-blogs-products-classifieds-reviews-ratings-pinboard-wishlists'>Multiple Listing Types Plugin</a>"  for creating Multiple Listing Types like: Food, Room Packages, Services Offered, etc. Hotel Page Admins will be able to associate / add their Listings to their Hotel Pages. These Listings will then be visible on the Main Hotel Pages in their respective Listing Type tabs.
          The profile of each associated listing type will also show the Hotel Page of the Hotel that offers that Listing.
          <div>
          To know more about this extension, please <a target='blank' href='http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-multiple-listings-products-showcase'>click here</a>.
    <br/><br/>

    <b>Note:</b> If you have "<a target='blank' href='http://www.socialengineaddons.com/socialengine-directory-businesses-plugin'>Directory / Businesses Plugin</a>" installed on your site, then you can also integrate "<a target='blank' href='http://www.socialengineaddons.com/businessextensions/socialengine-directory-businesses-multiple-listings-products-showcase'>Directory / Businesses - Multiple Listings and Products Showcase Extension</a>" with this plugin.


         </div>
        </div>
        </li>    
        </ul>
      </div>  
    </div>
<?php endif; ?>





<div class="importlisting_form">
<div>
<h3 class="seaocore_list_head">  Integration with Other Plugins </h3>
<div class="admin_search">
  <div class="clear">
    <div class="search">
      <form method="post" action="" class="global_form_box" enctype="" id="filter_form" name="form1">
        <div style="padding-top:5px;">
          <label class="" tag="" for="level_id">Integrable Plugins: </label>
        </div>
        <div>  
					<?php 
						if( $this->show_table == 1 ) {  $all_plugin = 'selected'; }
						else if( $this->show_table == 2 ) { $install_plugin = 'selected';  }
						else if( $this->show_table == 3 ) { $notinstall_plugin = 'selected'; }
						else if( $this->show_table == 4 ) { $intregertion_plugin = 'selected'; }
					?>
          <select id="level_id" name="level_id" onchange="document.form1.submit();">
            <!--<option label="" value="1" <?php //if( !empty($all_plugin) ) { echo $all_plugin; } ?>><?php //echo $this->translate('All Plugins'); ?> </option>-->
            <option label="" value="4" <?php if( !empty($intregertion_plugin) ) { echo $intregertion_plugin; } ?> ><?php echo $this->translate('All Integrable Plugins') ?></option>
            <option label="" value="2" <?php if( !empty($install_plugin) ) { echo $install_plugin; } ?> ><?php echo $this->translate('Integrable Plugins installed on your site') ?></option>
            <option label="" value="3" <?php if( !empty($notinstall_plugin) ) { echo $notinstall_plugin; } ?> ><?php echo $this->translate('Integrable Plugins not installed on your site') ?></option>
          </select>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="tabs" style="height:10px;margin-top:15px;"></div>

		<ul class='seaocore_browse'>
			<?php $product_id = 4; ?>

			<?php 
				// No plugin installed of 'SocialEngineAddOns' in the site then show the msg.
				if(!empty($install_plugin) && empty($this->channel)) {
					echo '<div class="tip"><span>Note: There are currently no integrable plugins installed on your site. To enhance the functionality of this plugin and provide more features to your users, please install integrable and compatible plugins on your site as they would provide enhanced and seamless experience to your users. To see the complete list of SocialEngineAddOns plugins, please visit: <a href="http://www.socialengineaddons.com/catalog/1/plugins" target="_blank">http://www.socialengineaddons.com/catalog/1/plugins</a></span></div>';
				}
				elseif(!empty($notinstall_plugin) && empty($this->channel)) {
					echo '<div class="tip"><span>Congratulations! You have installed all the integrable plugins on your site. Please <a href="http://www.socialengineaddons.com/contact-us" target="_blank">contact us</a> to share your experience and provide your valuable suggestions and feedback.</span></div>';
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
						<?php echo '<a href="http://demo.socialengineaddon.com" class="seaocore_type_seaocore" target="_blank">' . $this->translate('Demo') . '</a>'; ?>
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
</div></div>
