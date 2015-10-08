<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-main-photo.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$sitealbum = $this->subject();
//GET VIEWER INFORMATION
$this->cover_params = array('top' => 0, 'left' => 0);
$this->resource_id = $resource_id = $sitealbum->getIdentity();
$this->resource_type = $resource_type = $sitealbum->getType();
$this->subcategory_name = '';
$this->subsubcategory_name = '';
$categoriesTable = Engine_Api::_()->getDbTable('categories', 'sitealbum');
$this->category_name = $categoriesTable->getCategory($sitealbum->category_id)->category_name;
if (isset($categoriesTable->getCategory($sitealbum->subcategory_id)->category_name))
  $this->subcategory_name = $categoriesTable->getCategory($sitealbum->subcategory_id)->category_name;
?>

<div class="seaocore_profile_cover_head_section <?php if (!in_array('mainPhoto', $this->showContent)): ?>seaocore_profile_photo_none<?php endif; ?>" id="siteuser_main_photo">
  <?php if (in_array('mainPhoto', $this->showContent) || in_array('title', $this->showContent)): ?>
    <div class="seaocore_profile_cover_head">
      <?php if (in_array('mainPhoto', $this->showContent)): ?>
        <div class="seaocore_profile_main_photo_wrapper">
          <div class='seaocore_profile_main_photo'>
            <div class="item_photo <?php if ($this->sitecontentcoverphotoStrachMainPhoto): ?> show_photo_box <?php endif; ?>">
              <table border="0" cellpadding="0" cellspacing="0">
                <tr valign="middle">
                  <td>
                    <?php
                    $href = Engine_Api::_()->seaocore()->getContentPhotoHref($this->subject());
                    ?>
                    <?php if (empty($this->can_edit) && $href) : ?>
                      <a href="<?php echo $href; ?>" class="thumbs_photo" data-linktype="photo-gallery">
                      <?php endif; ?>
                      <?php echo $this->itemPhoto($this->subject(), 'thumb.profile', '', array('align' => 'left', 'id' => 'content_profile_photo')); ?>
                      <?php if (empty($this->can_edit) && $href) : ?></a><?php endif; ?>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      <?php endif; ?>
      <?php if (in_array('title', $this->showContent)): ?>
        <div class="seaocore_profile_cover_title">
          <?php if (in_array('title', $this->showContent)): ?>
            <h2 style="color:<?php echo $this->fontcolor; ?>"><?php echo $sitealbum->getTitle(); ?></h2>
          <?php endif; ?>

          <div class="seaocore_txt_light" style="font-size:12px;" >
            <?php if (isset(Engine_Api::_()->getDbTable('categories', 'sitealbum')->getCategory($sitealbum->subcategory_id)->category_name)): ?>
              <?php
              $category_name = Engine_Api::_()->getDbTable('categories', 'sitealbum')->getCategory($sitealbum->category_id)->category_name;
              $subCategory_name = Engine_Api::_()->getDbTable('categories', 'sitealbum')->getCategory($sitealbum->subcategory_id)->category_name;
              ?> 
              <?php echo $this->htmlLink($this->url(array('category_id' => $sitealbum->category_id, 'categoryname' => $category_name, 'category_id' => $sitealbum->category_id, 'categoryname' => $category_name), 'sitealbum_general_category'), $this->translate($this->category_name)) ?>
              <?php echo '&raquo;'; ?>  
              <?php echo $this->htmlLink($this->url(array('category_id' => $sitealbum->category_id, 'categoryname' => $category_name, 'subcategory_id' => $sitealbum->subcategory_id, 'subcategoryname' => $subCategory_name), 'sitealbum_general_subcategory'), $this->translate($this->subcategory_name)) ?>
            <?php endif; ?>
          </div>         
        </div>
      <?php endif; ?>

    </div>
  <?php endif; ?>
  <div class="ui-page-content">
    <?php if (!empty($sitealbum->sponsored) || !empty($sitealbum->featured)): ?>
      <table cellpadding="2" cellspacing="0" style="width:100%">
        <tr>
          <?php if (!empty($sitealbum->sponsored)): ?>
            <td style="width:50%;">
              <div class="sm-sl" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.sponsored.color', '#fc0505'); ?>;'>
                <?php echo $this->translate('SPONSORED'); ?>
              </div>
            </td>
          <?php endif; ?>
          <?php if (!empty($sitealbum->featured)): ?>
            <td style="width:50%;">
              <div class="sm-sl" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.featured.color', '#0cf523'); ?>;'>
                <?php echo $this->translate('FEATURED'); ?>
              </div>
            </td>
          <?php endif; ?>
        </tr>
      </table>
    <?php endif; ?>
    <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1) && isset($this->subject->location) && !empty($this->subject->location) && !empty($this->showContent) && in_array('location', $this->showContent)) : ?> 
      <div class="siteuser_cover_profile_fields">
        <ul>
          <li>
            <span><?php echo $this->translate("Location") ?>:</span>
            <span> <?php echo $this->htmlLink('https://maps.google.com/?q=' . urlencode($this->subject()->location), $sitealbum->location, array('target' => 'blank')) ?> </span>
          </li>
        </ul>
      </div>
    <?php endif; ?>
  </div>

  <div class="siteuser_cover_profile_fields">
    <ul>
      <li>
        <?php if (!empty($this->showContent) && in_array('owner', $this->showContent)): ?>
          <?php if (!empty($this->cover_photo_preview)): ?>
            <?php echo $this->translate("Owner Name"); ?>
          <?php else: ?>
            <?php echo $this->translate('By %1$s', $this->subject->getOwner()->__toString()); ?>
          <?php endif; ?>
        <?php endif; ?>
      </li> 
    </ul>
  </div>
  <div class="siteuser_cover_profile_fields">
    <ul>
      <li>
        <?php
        if (!empty($this->showContent) && in_array('creationDate', $this->showContent)) {
          echo '<div class="seao_listings_stats"><i class="seao_icon_strip seao_icon seao_icon_time" title="' . $this->translate("Creation Date") . '"></i><div class="o_hidden">' . $this->timestamp($this->subject->creation_date) . '</div></div>';
        }
        ?>
      </li> 
    </ul>
  </div>
  <div class="siteuser_cover_profile_fields">
    <ul>
      <li>
        <?php
        if (!empty($this->showContent) && in_array('updateDate', $this->showContent)) {
          echo '<div class="seao_listings_stats"><i class="seao_icon_strip seao_icon seao_icon_date" title="' . $this->translate('Update Date') . '"></i><div class="o_hidden">' . $this->translate('Updated about %1$s', $this->timestamp($this->subject->modified_date)) . '</div></div>';
        }
        ?>
      </li> 
    </ul>
  </div>

  <?php if ((in_array('viewCount', $this->showContent) || in_array('likeCount', $this->showContent) || in_array('commentCount', $this->showContent) || in_array('totalPhotos', $this->showContent))): ?>
    <div class="siteuser_cover_profile_fields">
      <ul>
        <li>
          <?php
          $statistics = '';

          if (is_array($this->showContent) && in_array('commentCount', $this->showContent)) {
            $statistics .= $this->translate(array('%s comment', '%s comments', $sitealbum->comment_count), $this->locale()->toNumber($sitealbum->comment_count)) . ' - ';
          }

          if (is_array($this->showContent) && in_array('viewCount', $this->showContent)) {
            $statistics .= $this->translate(array('%s view', '%s views', $sitealbum->view_count), $this->locale()->toNumber($sitealbum->view_count)) . ' - ';
          }

          if (is_array($this->showContent) && in_array('likeCount', $this->showContent)) {
            $statistics .= $this->translate(array('%s like', '%s likes', $sitealbum->like_count), $this->locale()->toNumber($sitealbum->like_count)) . ' - ';
          }

          if (is_array($this->showContent) && in_array('totalPhotos', $this->showContent) && isset($this->subject()->photos_count)) {
            $statistics .= $this->translate(array('%s Photo', '%s Photos', $sitealbum->photos_count), $this->locale()->toNumber($sitealbum->photos_count)) . ' - ';
          }

          $statistics = trim($statistics);
          $statistics = rtrim($statistics, '-');
          ?>
          <?php echo $statistics; ?>
        </li> 
      </ul>
    </div>
  <?php endif; ?>



  <div class="siteuser_cover_profile_fields">
    <ul>
      <li>
        <?php if (!empty($this->showContent) && in_array('description', $this->showContent)): ?>
          <div><?php echo $this->viewMore(strip_tags($sitealbum->description), 30, 400) ?></div>
        <?php endif; ?>
      </li> 
    </ul>
  </div>

  <?php if (!empty($this->showContent) && in_array('rating', $this->showContent)): ?>
    <?php echo $this->content()->renderWidget("sitealbum.user-ratings"); ?>     
  <?php endif; ?>

  <div class="seaocore_profile_cover_buttons">
    <table cellpadding="2" cellspacing="0">
      <tr>
        <td id="seaocore_like">
          <?php if (!empty($this->viewer_id)): ?>
            <?php $hasLike = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($this->resource_type, $this->resource_id); ?>
            <a href ="javascript://" onclick = "seaocore_content_type_likes_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type; ?>_unlikes_<?php echo $this->resource_id; ?>" style ='display:<?php echo $hasLike ? "block" : "none" ?>'>
              <i class="ui-icon-thumbs-down-alt"></i>
              <span><?php echo $this->translate('Unlike') ?></span>
            </a>
            <a href = "javascript://" onclick = "seaocore_content_type_likes_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type; ?>_most_likes_<?php echo $this->resource_id; ?>" style ='display:<?php echo empty($hasLike) ? "block" : "none" ?>'>
              <i class="ui-icon-thumbs-up-alt"></i>
              <span><?php echo $this->translate('Like') ?></span>
            </a>
            <input type ="hidden" id = "<?php echo $this->resource_type; ?>_like_<?php echo $this->resource_id; ?>" value = '<?php echo $hasLike ? $hasLike[0]['like_id'] : 0; ?>' />
          <?php endif; ?>
        </td>
      </tr>
    </table>  
  </div>
</div>

<style>
  .seaocore_profile_coverinfo_status, .seaocore_profile_coverinfo_status a, .seaocore_profile_coverinfo_status div, .seaocore_profile_coverinfo_statistics, .seaocore_profile_coverinfo_statistics div{
    color:<?php echo $this->fontcolor; ?> !important;
  }
</style>