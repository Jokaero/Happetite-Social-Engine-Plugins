<?php
$siteevent = $this->subject();
//GET VIEWER INFORMATION
//    $this->allowPage = Engine_Api::_()->siteevent()->allowInThisPage($siteevent, "siteeventmember", 'smecreate');
$this->cover_params = array('top' => 0, 'left' => 0);

//    $this->siteeventTags = $siteevent->tags()->getTagMaps();
$this->resource_id = $resource_id = $siteevent->getIdentity();
$this->resource_type = $resource_type = $siteevent->getType();
$this->follow_count = Engine_Api::_()->getDbtable('follows', 'seaocore')->numberOfFollow($resource_type, $resource_id);
$this->subcategory_name = '';
$this->subsubcategory_name = '';
$categoriesTable = Engine_Api::_()->getDbTable('categories', 'siteevent');
$this->category_name = $categoriesTable->getCategory($siteevent->category_id)->category_name;
if (isset($categoriesTable->getCategory($siteevent->subcategory_id)->category_name))
  $this->subcategory_name = $categoriesTable->getCategory($siteevent->subcategory_id)->category_name;
if (isset($categoriesTable->getCategory($siteevent->subsubcategory_id)->category_name))
  $this->subsubcategory_name = $categoriesTable->getCategory($siteevent->subsubcategory_id)->category_name;
?>

 <?php if (!empty($siteevent->sponsored) || !empty($siteevent->featured)): ?>
  <div class="list-label-wrap">
    <?php if (!empty($siteevent->sponsored) && in_array('sponsored', $this->showContent)): ?>
      <div class="list-label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsored.color', '#fc0505'); ?>;'>
        <?php echo $this->translate('SPONSORED'); ?>
      </div>
  <?php endif; ?>
  <?php if (!empty($siteevent->featured) && in_array('featured', $this->showContent)): ?>
      <div class="list-label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.featured.color', '#0cf523'); ?>;'>
        <?php echo $this->translate('FEATURED'); ?>
      </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php if (in_array('mainPhoto', $this->showContent) || in_array('title', $this->showContent)): ?>
    <div class="content_cover_head" id="siteuser_main_photo">
      <?php if (in_array('mainPhoto', $this->showContent)): ?>
        <div class="content_cover_main_photo_wrapper">
          <div class='content_cover_main_photo'>
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
      <!--//IF EVENT REPEAT MODULE EXIST THEN SHOW EVENT REPEAT INFO WIDGET-->
      <?php
      $siteeventrepeat = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat');
      if ($siteeventrepeat) {
        $showrepeatinfo = is_array($this->showContent) && in_array('showrepeatinfo', $this->showContent) ? true : false;
        echo $this->content()->renderWidget("siteeventrepeat.event-profile-repeateventdate", array("showrepeatinfo" => $showrepeatinfo));
      }
      ?>
      <?php if (in_array('title', $this->showContent)): ?>
        <div class="content_cover_title<?php if (!in_array('mainPhoto', $this->showContent)): ?> seaocore_profile_photo_none<?php endif; ?>">
          <?php if (in_array('title', $this->showContent)): ?>
            <h2 style="color:<?php echo $this->fontcolor; ?>"><?php echo $siteevent->getTitle(); ?></h2>
          <?php endif; ?>
           <div class="f_small">
            <?php if (isset(Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategory($siteevent->subcategory_id)->category_name)):?>
              <?php 
              $category_name = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategory($siteevent->category_id)->category_name;
              $subCategory_name = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategory($siteevent->subcategory_id)->category_name;
              ?> 
              <?php echo $this->htmlLink($this->url(array('category_id' => $siteevent->category_id, 'categoryname' => $category_name, 'category_id' => $siteevent->category_id, 'categoryname' => $category_name ), 'siteevent_general_category'), $this->translate($this->category_name)) ?>
              <?php echo '&raquo;'; ?>  
              <?php echo $this->htmlLink($this->url(array('category_id' => $siteevent->category_id, 'categoryname' => $category_name, 'subcategory_id' => $siteevent->subcategory_id, 'subcategoryname' => $subCategory_name ), 'siteevent_general_subcategory'), $this->translate($this->subcategory_name)) ?>
            <?php endif; ?>
            <?php if (isset(Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategory($siteevent->subsubcategory_id)->category_name)): ?>
              <?php echo '&raquo;'; ?> 
              <?php echo $this->htmlLink($this->url(array('category_id' => $siteevent->category_id, 'categoryname' => $category_name, 'subcategory_id' => $siteevent->subcategory_id, 'subcategoryname' => $subCategory_name, 'subsubcategory_id' => $siteevent->subsubcategory_id, 'subsubcategoryname' => $subCategory_name), 'siteevent_general_subsubcategory'), $this->translate($this->subsubcategory_name)) ?>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
 
  <div class="content_cover_info o_hidden <?php if (!in_array('mainPhoto', $this->showContent)): ?>seaocore_profile_photo_none<?php endif; ?>" >
    <?php if (is_array($this->showContent)): ?>
    <p class="fright f_small">
      <b>
       <?php if ($this->subject()->price > 0): ?>
         <?php echo $this->locale()->toCurrency($this->subject()->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')); ?>
       <?php else: ?>  
         <?php echo $this->translate("FREE"); ?>
       <?php endif; ?>
      </b>
    </p>
   <?php endif; ?>
  <?php if (in_array('hostName', $this->showContent)): ?>
      <p class="t_light f_small o_hidden">
        <?php echo $this->translate('Hosted by'); ?>: 
        <?php echo $this->htmlLink($this->subject()->getHost()->getHref(), $this->subject()->getHost()->getTitle()); ?>                </p>
  <?php endif; ?>
   <?php if(!empty($siteevent->rating_editor)): ?>
      <p class="t_light f_small"><?php echo $this->translate("Editor Rating: ").$this->showRatingStarSiteeventSM($siteevent->rating_editor, 'editor', 'small-star'); ?>    </p>
  <?php endif; ?>
  <?php if(!empty($siteevent->rating_users)): ?>
      <p class="t_light f_small"><?php echo $this->translate("User Ratings: ").$this->showRatingStarSiteeventSM($siteevent->rating_users, 'user', 'small-star'); ?>       </p>
  <?php endif; ?>

  <?php if (in_array('followCount', $this->showContent) || in_array('likeCount', $this->showContent)): ?>
    <div class="siteuser_cover_profile_fields">
      <ul>
        <li>
          <?php
          $statistics = '';
      
          if (is_array($this->showContent) && in_array('likeCount', $this->showContent)) {
            $statistics .= $this->translate(array('%s like', '%s likes', $siteevent->like_count), $this->locale()->toNumber($siteevent->like_count)) . ' - ';
          }

          if (is_array($this->showContent) && in_array('followCount', $this->showContent) && isset($this->subject()->follow_count)) {
            $statistics .= $this->translate(array('%s follow', '%s follows', $siteevent->follow_count), $this->locale()->toNumber($siteevent->follow_count)) . ' - ';
          }

          $statistics = trim($statistics);
          $statistics = rtrim($statistics, '-');
          ?>
          <?php echo $statistics; ?>
        </li> 
      </ul>
    </div>
  <?php endif; ?>
  </div>

  <div class="seaocore_cover_cont">
    <div class="seaocore_profile_cover_buttons">
      <table cellpadding="2" cellspacing="0">
        <tr>
          <?php        
          $viewer = Engine_Api::_()->user()->getViewer();
          $subject = Engine_Api::_()->core()->getSubject();
          $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
          $requestUrl = $this->url(Array('module' => 'siteevent', 'controller' => 'member', 'action' => 'request', 'event_id' => $siteevent->getIdentity(), 'occurrence_id' => $occurrence_id, 'format' => 'smoothbox',), 'siteevent_extended', 'true');
          $joinUrl = $this->url(Array('module' => 'siteevent', 'controller' => 'member', 'action' => 'join', 'event_id' => $siteevent->getIdentity(), 'occurrence_id' => $occurrence_id, 'format' => 'smoothbox',), 'siteevent_extended', 'true');
          $leaveUrl = $this->url(Array('module' => 'siteevent', 'controller' => 'member', 'action' => 'leave', 'event_id' => $siteevent->getIdentity(), 'occurrence_id' => $occurrence_id), 'siteevent_extended', 'true');
          $row = $subject->membership()->getRow($viewer);
          if (null === $row):
            if ($subject->membership()->isResourceApprovalRequired()) :
              ?>
              <td>
                <a href="<?php echo $requestUrl; ?>" data-role='button' >
                  <i class="ui-icon-plus"></i>
                  <span><?php echo $this->translate('Request') ?></span>
                </a>
              </td>
            <?php else: ?>
              <?php if (in_array('joinButton', $this->showContent)): ?>
              <td>
                <a href="<?php echo $joinUrl; ?>" data-role='button' >
                  <i class="ui-icon-ok"></i>
                  <span><?php echo $this->translate('Join') ?></span>
                </a>
              </td>
              <?php endif; ?>
            <?php endif; ?>
          <?php elseif ($row->active): ?>
            <td>
              <a href="<?php echo $leaveUrl; ?>" data-role='button' >
                <i class="ui-icon-delete"></i>
                <span><?php echo $this->translate('Leave') ?></span>
              </a>
            </td> 
          <?php endif; ?>
          <?php    
            if (Engine_Api::_()->core()->getSubject()->authorization()->isAllowed($viewer, 'invite')):
          $inviteUrl = $this->url(Array('module' => 'siteevent', 'controller' => 'member', 'action' => 'invite', 'event_id' => $siteevent->getIdentity(), 'occurrence_id' => $occurrence_id, 'format' => 'smoothbox',), 'siteevent_extended', 'true');
?>
             <?php if (in_array('inviteGuest', $this->showContent)): ?>
            <td>
              <a href="<?php echo $inviteUrl; ?>" data-role='button' >
                <i class="ui-icon-plus"></i>
                <span><?php echo $this->translate('Invite') ?></span>
              </a>
            </td>
             <?php endif; ?>
          <?php endif; ?>
          <?php if (!empty($this->viewer_id) && in_array('likeButton', $this->showContent)): ?>
            <td id="seaocore_like">
              <?php $hasLike = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($this->resource_type, $this->resource_id); ?>
              <a href ="javascript://" onclick = "seaocore_content_type_likes_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type; ?>_unlikes_<?php echo $this->resource_id; ?>" style ='display:<?php echo $hasLike ? "block" : "none" ?>'>
                <i class="ui-icon ui-icon-thumbs-up-alt feed-unlike-btn"></i>
                <span><?php echo $this->translate('Like') ?></span>
              </a>
              <a href = "javascript://" onclick = "seaocore_content_type_likes_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type; ?>_most_likes_<?php echo $this->resource_id; ?>" style ='display:<?php echo empty($hasLike) ? "block" : "none" ?>'>
                <i class="ui-icon ui-icon-thumbs-up-alt feed-like-btn"></i>
                <span><?php echo $this->translate('Like') ?></span>
              </a>
              <input type ="hidden" id = "<?php echo $this->resource_type; ?>_like_<?php echo $this->resource_id; ?>" value = '<?php echo $hasLike ? $hasLike[0]['like_id'] : 0; ?>' />
            </td>
          <?php endif; ?>
          <?php if ($this->viewer_id != $siteevent->getOwner()->getIdentity() && in_array('followButton', $this->showContent)): ?>
            <?php if ($this->viewer_id): ?>
              <td id="seaocore_follow">
                <?php $isFollow = $siteevent->follows()->isFollow($this->viewer()); ?>
                <a href="javascript://" onclick = "seaocore_resource_type_follows_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type ?>_unfollows_<?php echo $this->resource_id; ?>" style =' display:<?php echo $isFollow ? "block" : "none" ?>'>
                  <i class="ui-icon-delete"></i>
                  <span><?php echo $this->translate('Unfollow') ?></span>
                </a>
                <a href="javascript://" onclick = "seaocore_resource_type_follows_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type ?>_most_follows_<?php echo $this->resource_id; ?>" style ='display:<?php echo empty($isFollow) ? "block" : "none" ?>'>
                  <i class="ui-icon-plus"></i>
                  <span><?php echo $this->translate('Follow') ?></span>
                </a>
                <input type ="hidden" id = "<?php echo $this->resource_type; ?>_follow_<?php echo $this->resource_id; ?>" value = '<?php echo $isFollow ? $isFollow : 0; ?>' />
              </td>
            <?php endif; ?>
          <?php endif; ?>
        </tr>
      </table>  
    </div>
    
    <div class="place-host-info">
      <ul data-inset="false" data-role="listview" class="ui-listview" data-thumb="false">
        <li>
          <span class="icon"><i class="ui-icon-time"> </i></span>
          <p class="details">
            <?php if (in_array('startDate', $this->showContent) || in_array('endDate', $this->showContent)) : ?>
              <?php $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null; ?>
              <?php $dateTimeInfo = array(); ?>
              <?php $dateTimeInfo['occurrence_id'] = $occurrence_id; ?>
              <?php $dateTimeInfo['showStartDateTime'] = in_array('startDate', $this->showContent); ?>
              <?php $dateTimeInfo['showEndDateTime'] = in_array('endDate', $this->showContent); ?>
              <?php $dateTimeInfo['showDateTimeLabel'] = true; ?>
              <?php
              $showrepeatinfo = true;
              if ($siteeventrepeat) {
                $showrepeatinfo = is_array($this->showContent) && in_array('showrepeatinfo', $this->showContent) ? false : true;
              }
              ?>
              <?php $dateTimeInfo['showMultipleText'] = $showrepeatinfo; ?>
              <?php $this->eventDateTimeSMApp($this->subject(), $dateTimeInfo); ?> 
            <?php endif; ?>
          </p>
        </li>
        
        <?php if (is_array($this->showContent) && in_array('location', $this->showContent) && $this->subject()->location): ?>
          <li>
            <span class="icon"><i class="ui-icon-map-marker"> </i></span>
            <p class="details">
              <?php echo $this->translate("Venue") ?>: 
              <?php echo $this->subject()->venue_name; ?> 
              <span><?php echo $this->htmlLink('https://maps.google.com/?q=' . urlencode($this->subject()->location), $siteevent->location,array('target' => 'blank')) ?></span>
            </p>
          </li>
        <?php endif; ?>
          
        <?php if (in_array('ledBy', $this->showContent)): ?>
          <li>
            <span class="icon"><i class="ui-icon-user"> </i></span>
            <p class="details"><?php echo $this->translate('led by'); ?>
                <?php
              $ledBys = $this->subject()->getLedBys();
              if (!empty($ledBys)) :
                echo $this->subject()->getLedBys();
              endif;
              ?>
            </p>
          </li> 
        <?php endif; ?> 
       </ul>
     </div>
    <div class="attending-probability">
      <div>
        <ul>
          <li>
            <span><?php echo $this->locale()->toNumber($siteevent->getAttendingCount()) ?></span>
            <span><?php echo $this->translate('attending'); ?></span>
          </li>
          <li>
            <span><?php echo $this->locale()->toNumber($siteevent->getMaybeCount()) ?></span>
            <span><?php echo $this->translate('maybe </br>attending'); ?></span>
          </li>
          <li>
            <span><?php echo $this->locale()->toNumber($siteevent->getNotAttendingCount()) ?></span>
            <span><?php echo $this->translate('not </br>attending'); ?></span>
          </li>
          <li>
            <span><?php echo $this->locale()->toNumber($siteevent->getAwaitingReplyCount()) ?></span>
            <span><?php echo $this->translate('awaiting </br>reply'); ?></span>
          </li>
        </ul>
      </div>
    </div>
     
    <div class="ui-page-content sm-widget-block content_cover_profile_fields">
      <h4><?php echo $this->translate('Details'); ?></h4>
      <ul>
        <?php if (!empty($this->showContent) && in_array('description', $this->showContent)): ?>
        <?php if (!empty($siteevent->body)): ?>
          <li><?php echo nl2br($siteevent->body) ?></li>
        <?php endif ?>
        <?php if (!empty($siteevent->category_id)): ?>
          <li>
            <span class="t_light"><?php echo $this->translate('Category:') ?></span>
            <span>
              <?php 
              $category_name = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategory($siteevent->category_id)->category_name;
              echo $this->htmlLink($this->url(array('category_id' => $siteevent->category_id, 'categoryname' => $category_name, 'category_id' => $siteevent->category_id, 'categoryname' => $category_name ), 'siteevent_general_category'), $this->translate($this->category_name)) ?>
            </span>
          </li>
        <?php endif ?>
          
        <?php if (count($this->siteeventTags) > 0): $tagCount = 0; ?>
          <li>
            <span><?php echo $this->translate('Tags'); ?>:</span>
            <span>
              <?php foreach ($this->siteeventTags as $tag): ?>
                <?php if (!empty($tag->getTag()->text)): ?>
                    <?php if (empty($tagCount)): ?>
                        <a href='<?php echo $this->url(array('action' => 'index', 'tag' => $tag->getTag()->tag_id, 'tag_name' => Engine_Api::_()->seaocore()->getSlug($tag->getTag()->text, 225)), "siteevent_general"); ?>'>#<?php echo $tag->getTag()->text ?></a>
                        <?php
                        $tagCount++;
                    else:
                        ?>
                        <a href='<?php echo $this->url(array('action' => 'index', 'tag' => $tag->getTag()->tag_id, 'tag_name' => Engine_Api::_()->seaocore()->getSlug($tag->getTag()->text, 225)), "siteevent_general"); ?>'>#<?php echo $tag->getTag()->text ?></a>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
            </span>
          </li>
        <?php endif; ?>
          
        <?php if (is_array($this->showContent)): ?>
          <li>
              <?php echo $this->translate('Last updated %s', $this->timestamp($siteevent->modified_date)) ?>
          </li>
          <li>
            <?php echo $this->translate(array('%s comment,', '%s comments', $siteevent->comment_count), $this->locale()->toNumber($siteevent->comment_count));
            ?>  
            <?php
            echo $this->translate(array('%s view,', '%s views', $siteevent->view_count), $this->locale()->toNumber($siteevent->view_count));
            ?>

            <?php echo $this->translate(array('%s like,', '%s likes,', $siteevent->like_count), $this->locale()->toNumber($siteevent->like_count));
            ?>  <?php
            echo $this->translate(array('%s follower', '%s followers', $siteevent->follow_count), $this->locale()->toNumber($siteevent->follow_count));
            ?> 
          </li>
        <?php endif; ?>
      </ul>
            
      <?php if ($this->profile_like_button != 2): ?>
        <?php
        $row = Engine_Api::_()->getDbTable('otherinfo', 'siteevent')->getOtherinfo($this->subject()->event_id);
        ?>
        <?php if (($this->show_phone || $this->show_email || $this->show_website)): ?>
          <?php if(!empty($row->phone) || !empty($row->email) || !empty($row->website)): ?>
            <h4>
              <span><?php echo $this->translate("Contact Information"); ?></span>
            </h4>
            <ul>
              <?php if ($this->show_phone && !empty($row->phone)): ?>
                <li>
                  <span class="t_light"><?php echo $this->translate("Phone") ?>:</span>
                  <span> <a href="tel:<?php echo $row->phone ?>"> <?php echo $row->phone ?> </a></span>
                </li>
              <?php endif; ?>
              <?php if ($this->show_email && !empty($row->email)): ?>
                <li>
                  <span class="t_light"><?php echo $this->translate("Email") ?>:</span>
                  <span>  <a href='mailto:<?php echo $row->email ?>'><?php echo $this->translate('Email Me') ?></a></span>
                </li>
              <?php endif; ?>
              <?php if ($this->show_website && !empty($row->website)): ?>
                <li>
                <span class="t_light"><?php echo $this->translate("Website") ?>:</span>
                  <?php if (strstr($row->website, 'http://') || strstr($row->website, 'https://')): ?>
                    <span> <a href='<?php echo $row->website ?>' target="_blank" title='<?php echo $row->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a> </span>
                  <?php else: ?>
                    <span> 	<a href='http://<?php echo $row->website ?>' target="_blank" title='<?php echo $row->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a> </span>
                  <?php endif; ?>
                </li>             
              <?php endif; ?>
            </ul>
          <?php endif; ?>
        <?php endif; ?>        
      <?php endif; ?>
      <?php endif; ?>
    </ul>
  </div>
</div>