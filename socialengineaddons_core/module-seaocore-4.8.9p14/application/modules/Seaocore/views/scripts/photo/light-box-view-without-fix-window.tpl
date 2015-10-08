<?php ?>
<script type="text/javascript">

  var photoLightbox = 1;

</script>
<div class="photo_lightbox_options">
  <a onclick = "closeSEALightBoxAlbum();" class="close" title="<?php echo $this->translate('Close'); ?>" ></a>
</div>
<?php if (empty($this->is_ajax_lightbox)) : ?>
  <div id="lightbox_communityads_hidden" style="display: none;" >
    <?php echo $this->content()->renderWidget("seaocore.lightbox-ads", array('limit' => 1)) ?>
  </div>
<?php endif; ?>




<?php
$showLink = false;
if (isset($this->params['type']) && !empty($this->params['type'])):
  if ($this->type_count > 1):
    $showLink = true;
  endif;
elseif ($this->album->count() > 1):
  $showLink = true;
endif;
?>

<?php if ($showLink): ?>
  <div class="photo_lightbox_options" id="seaocore_photo_scroll">
    <a class="pre" onclick="photopaginationSocialenginealbum('<?php echo $this->escape(Engine_Api::_()->getApi('PhotoInLightbox', 'seaocore')->getSocialEngineAddOnsLightBoxPhotoHref($this->prevPhoto, array_merge($this->params, array('offset' => $this->PrevOffset)))) ?>', '<?php echo $this->module_name ?>', '<?php echo $this->tab ?>')" title="<?php echo $this->translate('Previous'); ?>" ></a>
    <a class="nxt" onclick="photopaginationSocialenginealbum('<?php echo $this->escape(Engine_Api::_()->getApi('PhotoInLightbox', 'seaocore')->getSocialEngineAddOnsLightBoxPhotoHref($this->nextPhoto, array_merge($this->params, array('offset' => $this->NextOffset)))) ?>', '<?php echo $this->module_name ?>', '<?php echo $this->tab ?>')" title="<?php echo $this->translate('Next'); ?>" ></a>
  </div>
<?php endif; ?>   
<div class="photo_lightbox_photo_detail seaocore_lightbox_photo_detail" id="photo_sea_lightbox_photo_detail">

  <?php if ($this->module_name != 'album') : ?>
    <?php if (isset($this->params['type']) && !empty($this->params['type'])): ?>    
      <?php echo $this->translate('Photos of %1$s By %2$s', $this->htmlLink($this->album->getParent(), $this->album->getParent()->getTitle()), $this->htmlLink($this->photo->getOwner()->getHref(), $this->photo->getOwner()->getTitle())); ?>
    <?php else: ?>
      <?php echo $this->translate('Photos of %1$s By %2$s', $this->htmlLink($this->album->getParent(), $this->album->getParent()->getTitle()), $this->htmlLink($this->photo->getOwner()->getHref(), $this->photo->getOwner()->getTitle())); ?>
      |
      <?php
      echo $this->translate('Photo %1$s of %2$s', $this->locale()->toNumber($this->getPhotoIndex + 1), $this->locale()->toNumber($this->album->count()))
      ?>
    <?php endif; ?>  
  <?php else: ?>    
    <?php if (isset($this->params['type']) && !empty($this->params['type'])): ?>    
      <?php echo $this->translate('Photos of %1$s By %2$s', $this->htmlLink($this->album, $this->album->getTitle()), $this->album->getOwner()->__toString()); ?>
    <?php else: ?>
      <?php echo $this->translate('Photos of %1$s By %2$s', $this->htmlLink($this->album, $this->album->getTitle()), $this->album->getOwner()->__toString()); ?>
      |
      <?php echo $this->translate('Photo %1$s of %2$s', $this->locale()->toNumber($this->getPhotoIndex + 1), $this->locale()->toNumber($this->album->count())) ?>
    <?php endif; ?>  
  <?php endif; ?>  
</div> 

<div class="photo_lightbox_image_content seaocore_lightbox_image_content">
  <div id='media_image_div_seaocore' class="photo_lightbox_image_content_media">
    <a id='media_photo_next'  <?php if ($showLink): ?> onclick="photopaginationSocialenginealbum('<?php echo $this->escape(Engine_Api::_()->getApi('PhotoInLightbox', 'seaocore')->getSocialEngineAddOnsLightBoxPhotoHref($this->nextPhoto, array_merge($this->params, array('offset' => $this->NextOffset)))) ?>', '<?php echo $this->module_name ?>', '<?php echo $this->tab ?>')" <?php endif; ?> title="<?php echo $this->photo->getTitle(); ?>">
      <?php
      echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array(
          'id' => 'media_photo',
          'class' => "lightbox_photo"
      ));
      ?>     
    </a> 
  </div>
</div>

<?php if ($this->canComment): ?>
  <div class="photo_lightbox_user_options seaocore_lightbox_user_options" id="photo_sea_lightbox_user_options">
    <a id="<?php echo $this->subject()->getType() ?>like_link" <?php if
  ($this->subject()->likes()->isLike($this->viewer())):
    ?>style="display: none;" <?php endif;
  ?>onclick="en4.seaocore.photolightbox.comments.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>');" href="javascript:void(0);" class="photo_lightbox_like"
       title="<?php echo $this->translate('Like This'); ?>"><?php echo $this->translate('Like'); ?></a>
    <a id="<?php echo $this->subject()->getType() ?>unlike_link" <?php if
     (!$this->subject()->likes()->isLike($this->viewer())):
    ?>style="display:none;" <?php endif; ?>
       onclick="en4.seaocore.photolightbox.comments.unlike('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>');" href="javascript:void(0);" class="photo_lightbox_unlike"
       title="<?php echo $this->translate('Unlike This'); ?>"><?php echo $this->translate('Unlike'); ?></a>
    <a href="javascript:void(0);" onclick="if ($('comment-form-open-li_<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>')) {
        $('comment-form-open-li_<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>').style.display = 'none';
      }
      $('comment-form_<?php echo $this->subject()->getType() . "_" . $this->subject()->getIdentity() ?>').style.display = '';
      $('comment-form_<?php echo $this->subject()->getType() . "_" . $this->subject()->getIdentity() ?>').body.focus();" class="photo_lightbox_comment" title="<?php echo $this->translate('Post
Comment'); ?>" ><?php echo $this->translate('Comments'); ?></a>
  </div>
<?php endif; ?>
<?php if ($this->canEdit): ?>
  <div class="photo_lightbox_user_right_options seaocore_lightbox_user_right_options"
       id="photo_sea_lightbox_user_right_options">
    <a class="icon_photos_lightbox_rotate_ccw"  onclick="$(this).set('class', 'icon_loading');
      en4.seaocore.photolightbox.rotate(<?php echo $this->photo->getIdentity() ?>, 90, '<?php echo
  $this->resource_type;
  ?>').addEvent('complete', function() {
        this.set('class',
                'icon_photos_lightbox_rotate_ccw')
      }.bind(this));
      loadingImageSeaocore();" title="<?php echo
  $this->translate("Rotate Left");
  ?>" ></a>
    <a class="icon_photos_lightbox_rotate_cw" onclick="$(this).set('class', 'icon_loading');
      en4.seaocore.photolightbox.rotate(<?php echo $this->photo->getIdentity() ?>, 270, '<?php echo
  $this->resource_type;
  ?>').addEvent('complete', function() {
        this.set('class',
                'icon_photos_lightbox_rotate_cw')
      }.bind(this));
      loadingImageSeaocore();" title="<?php echo
     $this->translate("Rotate Right");
  ?>" ></a>
    <a class="icon_photos_lightbox_flip_horizontal" onclick="$(this).set('class', 'icon_loading');
      en4.seaocore.photolightbox.flip(<?php echo $this->photo->getIdentity() ?>, 'horizontal', '<?php echo
     $this->resource_type;
  ?>').addEvent('complete', function() {
        this.set('class',
                'icon_photos_lightbox_flip_horizontal')
      }.bind(this));
      loadingImageSeaocore();" title="<?php echo
     $this->translate("Flip Horizontal");
  ?>" ></a>
    <a class="icon_photos_lightbox_flip_vertical"  onclick="$(this).set('class', 'icon_loading');
      en4.seaocore.photolightbox.flip(<?php echo $this->photo->getIdentity() ?>, 'vertical', '<?php echo
     $this->resource_type;
     ?>').addEvent('complete', function() {
        this.set('class',
                'icon_photos_lightbox_flip_vertical')
      }.bind(this));
      loadingImageSeaocore();" title="<?php echo
     $this->translate("Flip Vertical");
     ?>"></a>
  </div>
<?php endif; ?>
            <?php $viewer_id = $this->viewer()->getIdentity(); ?>
<div class="photo_lightbox_content" > 
  <div id="photo_sea_lightbox_text">
    <div class="photo_lightbox_content_left">
      <div class="photo_detail">
            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.title', 0)): ?>
                <?php if ($this->canEdit || !empty($this->photo->title)): ?>
            <div class="photo_lightbox_photo_description widthfull" id="link_seaocore_title" style="display:block;">
              <div>
                  <?php if ($this->canEdit): ?>
                  <span class="lightbox_photo_description_edit_icon">
                    <a href="javascript:void(0);" onclick="showeditTitle()" title=" <?php echo $this->translate('Edit this title'); ?> "></a>
                  </span>
    <?php endif; ?>
                <span id="seaocore_title" class="lightbox_photo_description lightbox_photo_title">
            <?php if (!empty($this->photo->title)): ?>
      <?php echo $this->photo->getTitle() ?>
    <?php elseif ($this->canEdit): ?>
                    <a href="javascript:void(0);" onclick="showeditTitle()" >  <?php echo $this->translate('Add a title'); ?> </a>
    <?php endif; ?>
                </span>
              </div>
            </div>
  <?php endif; ?>
          <div class="photo_lightbox_photo_description" >
            <div id="edit_seaocore_title" style="display: none;">
              <input type="text"  name="edit_title" id="editor_seaocore_title" title="<?php echo $this->translate('Add a title'); ?>" value="<?php echo $this->photo->title; ?>"/>
              <div>
                <button name="save" onclick="saveeditTitle()"><?php echo $this->translate('Save'); ?></button>
                <button name="cancel" onclick="showeditTitle();"><?php echo $this->translate('Cancel'); ?></button>
              </div>
            </div>
            <div id="seaocore_title_loading" style="display: none;" >
              <center><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/icons/loader.gif' /></center>
            </div>
          </div>
        <?php endif; ?>
        <span class="owner">
        <?php
        echo $this->translate('By %1$s', $this->htmlLink($this->photo->getOwner()->getHref(), $this->photo->getOwner()->getTitle()));
        ?>
          | <?php echo $this->timestamp($this->photo->modified_date) ?>
        </span>
      </div>
      <div class="photo_options">
        <?php if ($this->canTag): ?>
          <a href='javascript:void(0);' onclick='taggerInstanceSeaocore.begin();'><?php echo $this->translate('Tag This Photo'); ?></a>
        <?php endif; ?>          
        <?php if ($this->canEdit): ?>
          <?php
          $photo_array = array();
          if (!empty($this->photo))
            $photo_array = $this->photo->toarray();
          if ($this->module_name == 'sitepagenote') {
            echo $this->htmlLink(array('route' => $this->editRoute, 'controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitepagenote->note_id, 'page_id' => $this->sitepage->page_id), $this->translate('Edit'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitepagenote->note_id, 'page_id' => $this->sitepage->page_id), $this->editRoute, true)) . "'); return false;"));
          } else if ($this->module_name == 'sitebusinessnote') {
            echo $this->htmlLink(array('route' => $this->editRoute, 'controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitebusinessnote->note_id, 'business_id' => $this->sitebusiness->business_id), $this->translate('Edit'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitebusinessnote->note_id, 'business_id' => $this->sitebusiness->business_id), $this->editRoute, true)) . "'); return false;"));
          } else if ($this->module_name == 'sitegroupnote') {
            echo $this->htmlLink(array('route' => $this->editRoute, 'controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitegroupnote->note_id, 'group_id' => $this->sitegroup->group_id), $this->translate('Edit'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitegroupnote->note_id, 'group_id' => $this->sitegroup->group_id), $this->editRoute, true)) . "'); return false;"));
          } else if ($this->module_name == 'sitestorenote') {
            echo $this->htmlLink(array('route' => $this->editRoute, 'controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitestorenote->note_id, 'store_id' => $this->sitestore->store_id), $this->translate('Edit'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitestorenote->note_id, 'store_id' => $this->sitestore->store_id), $this->editRoute, true)) . "'); return false;"));
          } else if ($this->module_name == 'sitepage') {
            echo $this->htmlLink(array('route' => $this->editRoute, 'controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'page_id' => $this->sitepage->page_id,), $this->translate('Edit'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'page_id' => $this->sitepage->page_id,), $this->editRoute, true)) . "'); return false;"));
          } else if ($this->module_name == 'sitebusiness') {
            echo $this->htmlLink(array('route' => $this->editRoute, 'controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'business_id' => $this->sitebusiness->business_id), $this->translate('Edit'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'business_id' => $this->sitebusiness->business_id,), $this->editRoute, true)) . "'); return false;"));
          } else if ($this->module_name == 'sitegroup') {
            echo $this->htmlLink(array('route' => $this->editRoute, 'controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'group_id' => $this->sitegroup->group_id,), $this->translate('Edit'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'group_id' => $this->sitegroup->group_id,), $this->editRoute, true)) . "'); return false;"));
          } else if ($this->module_name == 'sitestore') {
            echo $this->htmlLink(array('route' => $this->editRoute, 'controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'store_id' => $this->sitestore->store_id,), $this->translate('Edit'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'store_id' => $this->sitestore->store_id,), $this->editRoute, true)) . "'); return false;"));
          } else {
            if (array_key_exists('collection_id', $photo_array)) {
              echo $this->htmlLink(array('route' => $this->editRoute, 'controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity()), $this->translate('Edit'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity()), $this->editRoute, true)) . "'); return false;"));
            } else {
              echo $this->htmlLink(array('route' => $this->editRoute, 'controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity()), $this->translate('Edit'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->editAction, 'album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity()), $this->editRoute, true)) . "'); return false;"));
            }
          }
          ?>         
        <?php endif; ?>          
        <?php if ($this->canDelete): ?>
          <?php
          $photo_array = array();
          if (!empty($this->photo))
            $photo_array = $this->photo->toarray();
          if ($this->module_name == 'sitepagenote') {
            echo $this->htmlLink(array('route' => $this->deleteRoute, 'controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitepagenote->note_id, 'page_id' => $this->sitepage->page_id, 'owner_id' => $this->photo->user_id,), $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitepagenote->note_id, 'page_id' => $this->sitepage->page_id, 'owner_id' => $this->photo->user_id,), $this->deleteRoute, true)) . "'); return false;"));
          } else if ($this->module_name == 'sitebusinessnote') {
            echo $this->htmlLink(array('route' => $this->deleteRoute, 'controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitebusinessnote->note_id, 'business_id' => $this->sitebusiness->business_id, 'owner_id' => $this->photo->user_id,), $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitebusinessnote->note_id, 'business_id' => $this->sitebusiness->business_id, 'owner_id' => $this->photo->user_id,), $this->deleteRoute, true)) . "'); return false;"));
          } else if ($this->module_name == 'sitegroupnote') {
            echo $this->htmlLink(array('route' => $this->deleteRoute, 'controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitegroupnote->note_id, 'group_id' => $this->sitegroup->group_id, 'owner_id' => $this->photo->user_id,), $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitegroupnote->note_id, 'group_id' => $this->sitegroup->group_id, 'owner_id' => $this->photo->user_id,), $this->deleteRoute, true)) . "'); return false;"));
          } else if ($this->module_name == 'sitestorenote') {
            echo $this->htmlLink(array('route' => $this->deleteRoute, 'controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitestorenote->note_id, 'store_id' => $this->sitestore->store_id, 'owner_id' => $this->photo->user_id,), $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitestorenote->note_id, 'store_id' => $this->sitestore->store_id, 'owner_id' => $this->photo->user_id,), $this->deleteRoute, true)) . "'); return false;"));
          } else if ($this->module_name == 'sitepage') {
            echo $this->htmlLink(array('route' => $this->deleteRoute, 'controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'page_id' => $this->sitepage->page_id,), $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'page_id' => $this->sitepage->page_id,), $this->deleteRoute, true)) . "'); return false;"));
          } else if ($this->module_name == 'sitebusiness') {
            echo $this->htmlLink(array('route' => $this->deleteRoute, 'controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'business_id' => $this->sitebusiness->business_id,), $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'business_id' => $this->sitebusiness->business_id,), $this->deleteRoute, true)) . "'); return false;"));
          } else if ($this->module_name == 'sitegroup') {
            echo $this->htmlLink(array('route' => $this->deleteRoute, 'controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'group_id' => $this->sitegroup->group_id,), $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'group_id' => $this->sitegroup->group_id,), $this->deleteRoute, true)) . "'); return false;"));
          } else if ($this->module_name == 'sitestore') {
            echo $this->htmlLink(array('route' => $this->deleteRoute, 'controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'store_id' => $this->sitestore->store_id,), $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'store_id' => $this->sitestore->store_id,), $this->deleteRoute, true)) . "'); return false;"));
          } else {
            if (array_key_exists('collection_id', $photo_array)) {
              echo $this->htmlLink(array('route' => $this->deleteRoute, 'controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity()), $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity()), $this->deleteRoute, true)) . "'); return false;"));
            } else {
              echo $this->htmlLink(array('route' => $this->deleteRoute, 'controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity()), $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity()), $this->deleteRoute, true)) . "'); return false;"));
            }
          }
          ?>
        <?php endif; ?>
        <?php if (!empty($viewer_id)): ?>
          <?php if (!in_array($this->module_name, array('sitebusinessnote', 'sitepagenote', 'sitegroupnote', 'sitestorenote'))): ?>
            <?php if (!in_array($this->module_name, array('sitebusiness', 'sitepage', 'sitegroup', 'sitestore'))): ?>
                <?php if (SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO): ?>        
                <a href="<?php echo $this->url(array('controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'user_extended', true); ?>" onclick="showSmoothBox('<?php echo $this->escape($this->url(Array('controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'user_extended', true)); ?>');
            return false;" > <?php echo $this->translate("Make Profile Photo") ?></a>
              <?php endif; ?>
              <?php else: ?>
                <?php if ($this->canEdit): ?>  
        <?php if (SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO): ?>  
                  <?php if ($this->module_name == 'sitepage'): ?>   
                    <a href="javascript:void(0);" onclick="showSmoothbox('<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'photo', 'action' => 'make-page-profile-photo', 'photo' => $this->photo->getGuid(), 'page_id' => $this->sitepage->page_id, 'format' => 'smoothbox'), 'sitepage_imagephoto_specific', true); ?>', 'profilephoto');">
                      <?php echo $this->translate('Make Page Profile Photo'); ?>
                    </a>
          <?php elseif ($this->module_name == 'sitegroup'): ?>   
                    <a href="javascript:void(0);" onclick="showSmoothbox('<?php echo $this->url(array('module' => 'sitegroup', 'controller' => 'photo', 'action' => 'make-group-profile-photo', 'photo' => $this->photo->getGuid(), 'group_id' => $this->sitegroup->group_id, 'format' => 'smoothbox'), 'sitegroup_imagephoto_specific', true); ?>', 'profilephoto');">
                    <?php echo $this->translate('Make Group Profile Photo'); ?>
                    </a> <?php elseif ($this->module_name == 'sitegroup'): ?>  

                  <?php elseif ($this->module_name == 'sitestore'): ?>   
                    <a href="javascript:void(0);" onclick="showSmoothbox('<?php echo $this->url(array('module' => 'sitestore', 'controller' => 'photo', 'action' => 'make-store-profile-photo', 'photo' => $this->photo->getGuid(), 'store_id' => $this->sitestore->store_id, 'format' => 'smoothbox'), 'sitestore_imagephoto_specific', true); ?>', 'profilephoto');">
                    <?php echo $this->translate('Make Store Profile Photo'); ?>
                    </a> <?php elseif ($this->module_name == 'sitestore'): ?>   

                  <?php else: ?>
                    <a href="javascript:void(0);" onclick="showSmoothbox('<?php echo $this->url(array('module' => 'sitebusiness', 'controller' => 'photo', 'action' => 'make-business-profile-photo', 'photo' => $this->photo->getGuid(), 'business_id' => $this->sitebusiness->business_id, 'format' => 'smoothbox'), 'sitebusiness_imagephoto_specific', true); ?>', 'profilephoto');">
                    <?php echo $this->translate('Make Business Profile Photo'); ?>
                    </a>
                    <?php endif; ?>
                  <?php endif; ?>
                <?php endif; ?> 
            <?php endif; ?>
          <?php endif; ?>             
        <?php endif; ?>                  
          <?php if (!empty($viewer_id)): ?>
            <?php if (SEA_PHOTOLIGHTBOX_SHARE): ?>
            <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $this->resource_type, 'id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), 'default', true)); ?>');
        return false;" >
            <?php echo $this->translate("Share") ?>
            </a>
          <?php endif; ?>
          <?php if (SEA_PHOTOLIGHTBOX_REPORT): ?>
            <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo ($this->url(array('module' => 'core', 'controller' => 'report', 'action' => 'create', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'default', true)); ?>');
        return false;" >
            <?php echo $this->translate("Report") ?>
            </a>      
          <?php endif; ?>
        <?php endif; ?>
        <?php if (SITEALBUM_ENABLED): ?>        
          <?php if ($this->canMakeFeatured && $this->allowView): ?>           
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Make Photo of the Day'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => 'add-photo-of-day', 'photo_id' => $this->subject()->getIdentity()), 'sitealbum_extended', true)) . "'); return false;")) ?>
            <a href="javascript:void(0);"  onclick='featuredPhoto();'><span id="featured_sitealbum_photo" <?php if ($this->subject()->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Featured"); ?>" ><?php echo $this->translate("Make Featured"); ?> </span> <span id="un_featured_sitealbum_photo" <?php if (!$this->subject()->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Un-Featured"); ?>" > <?php echo $this->translate("Make Un-featured"); ?> </span></a>           
          <?php endif; ?>   
        <?php endif; ?>
        <?php if ($this->photo): ?>
          <?php if ($this->allowFeatured): ?>
            <?php if ($this->module_name == 'sitepage'): ?>
              <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Make Photo of the Day'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('module' => 'sitepage', 'controller' => 'photo', 'action' => 'add-photo-of-day', 'photo_id' => $this->photo->photo_id), 'default', true)) . "'); return false;")) ?>
              <a href="javascript:void(0);"  onclick='featuredpagealbumPhoto("<?php echo $this->photo->photo_id; ?>");'><span id="featured_sitepagealbum_photo" <?php if ($this->photo->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Featured"); ?>" ><?php echo $this->translate("Make Featured"); ?> </span> <span id="un_featured_sitepagealbum_photo" <?php if (!$this->photo->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Un-Featured"); ?>" > <?php echo $this->translate("Make Un-featured"); ?> </span></a>     
            <?php elseif ($this->module_name == 'sitebusiness'): ?>  
              <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Make Photo of the Day'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('module' => 'sitebusiness', 'controller' => 'photo', 'action' => 'add-photo-of-day', 'photo_id' => $this->photo->photo_id), 'default', true)) . "'); return false;")) ?>
              <a href="javascript:void(0);"  onclick='featuredbusinessalbumPhoto("<?php echo $this->photo->photo_id; ?>");'><span id="featured_sitebusinessalbum_photo" <?php if ($this->photo->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Featured"); ?>" ><?php echo $this->translate("Make Featured"); ?> </span> <span id="un_featured_sitebusinessalbum_photo" <?php if (!$this->photo->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Un-Featured"); ?>" > <?php echo $this->translate("Make Un-featured"); ?> </span></a>
            <?php elseif ($this->module_name == 'sitegroup'): ?>
              <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Make Photo of the Day'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('module' => 'sitegroup', 'controller' => 'photo', 'action' => 'add-photo-of-day', 'photo_id' => $this->photo->photo_id), 'default', true)) . "'); return false;")) ?>
              <a href="javascript:void(0);"  onclick='featuredgroupalbumPhoto("<?php echo $this->photo->photo_id; ?>");'><span id="featured_sitegroupalbum_photo" <?php if ($this->photo->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Featured"); ?>" ><?php echo $this->translate("Make Featured"); ?> </span> <span id="un_featured_sitegroupalbum_photo" <?php if (!$this->photo->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Un-Featured"); ?>" > <?php echo $this->translate("Make Un-featured"); ?> </span></a> 
    <?php endif; ?>
  <?php endif; ?>
      <?php endif; ?>
      <?php if (SEA_PHOTOLIGHTBOX_DOWNLOAD): ?>
          <iframe src="about:blank" style="display:none" name="downloadframe"></iframe>
          <a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'core', 'action' => 'download'), 'default', true); ?><?php echo '?path=' . urlencode($this->photo->getPhotoUrl()) . '&file_id=' . $this->photo->file_id ?>" target='downloadframe'><?php echo $this->translate('Download') ?></a>
          <?php endif; ?>          
      </div>
    </div>

    <div class="photo_lightbox_content_middle">
            <?php if ($this->canEdit || !empty($this->photo->description)): ?>
        <div class="photo_lightbox_photo_description widthfull" id="link_seaocore_description" style="display:block;">
          <div>
              <?php if ($this->canEdit): ?>
              <span class="lightbox_photo_description_edit_icon">
                <a href="javascript:void(0);" onclick="showeditDescription()" title=" <?php echo $this->translate('Edit this caption'); ?> "></a>
              </span>
  <?php endif; ?>
            <span id="seaocore_description" class="lightbox_photo_description">
        <?php if (!empty($this->photo->description)): ?>
    <?php echo $this->photo->getDescription() ?>
  <?php elseif ($this->canEdit): ?>
                <a href="javascript:void(0);" onclick="showeditDescription()" >  <?php echo $this->translate('Add a caption'); ?> </a>
  <?php endif; ?>
            </span>
          </div>
        </div>
<?php endif; ?>
      <div class="photo_lightbox_photo_description"  >
        <div id="edit_seaocore_description" style="display: none;">
          <textarea rows="2" cols="10"  name="edit_description" id="editor_seaocore_description" title="<?php echo $this->translate('Add a caption'); ?>" ><?php echo $this->photo->description; ?></textarea>
          <div>
            <button name="save" onclick="saveeditDescription()"><?php echo $this->translate('Save'); ?></button>
            <button name="cancel" onclick="showeditDescription();"><?php echo $this->translate('Cancel'); ?></button>
          </div>
        </div>
        <div id="seaocore_description_loading" style="display: none;" >
          <center><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/icons/loader.gif' /></center>
        </div>

      </div>
        <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) : ?>
        <div class="seaotagcheckinshowlocation">
  <?php
  //RENDER LOCAION WIDGET
  echo $this->content()->renderWidget("sitetagcheckin.location-sitetagcheckin");
  ?>
        </div>
<?php endif; ?>
      <div class="photo_lightbox_photo_tags" id="media_tags" style="display: none;">
<?php echo $this->translate('In this photo:'); ?>
      </div>
      <div id="photo_view_comment" >
        <?php //echo $this->action("list", "comment", "seaocore", array("type" => $this->resource_type, "id" => $this->photo->getIdentity())); ?>
        <?php  include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listComment.tpl'; ?>
      </div>
    </div>

    <div class="photo_lightbox_content_right" id="community_sea_ads">     
    </div>

  </div>

</div>

<script type="text/javascript">
  var taggerInstanceSeaocore;
  if (window.parent.defaultLoad)
    window.parent.defaultLoad = false;
  en4.core.runonce.add(function() {
    var descEls = $$('.albums_viewmedia_info_caption');
    if (descEls.length > 0) {
      descEls[0].enableLinks();
    }
    taggerInstanceSeaocore = new Tagger('media_photo_next', {
      'title': '<?php echo $this->string()->escapeJavascript($this->translate('Tag This Photo')); ?>',
      'description': '<?php echo $this->string()->escapeJavascript($this->translate('Type a tag or select a name from the list.')); ?>',
      'createRequestOptions': {
        'url': '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'add'), 'default', true) ?>',
        'data': {
          'subject': '<?php echo $this->subject()->getGuid() ?>'
        }
      },
      'deleteRequestOptions': {
        'url': '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'remove'), 'default', true) ?>',
        'data': {
          'subject': '<?php echo $this->subject()->getGuid() ?>'
        }
      },
      'cropOptions': {
        'container': $('media_photo_next')
      },
      'tagListElement': 'media_tags',
      'existingTags': <?php echo $this->action('retrieve', 'tag', 'core', array('sendNow' => false)) ?>,
      'suggestProto': 'request.json',
      'suggestParam': "<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest', 'includeSelf' => true), 'default', true) ?>",
      'guid': <?php echo ( $this->viewer()->getIdentity() ? "'" . $this->viewer()->getGuid() . "'" : 'false' ) ?>,
      'enableCreate': <?php echo ( $this->canTag ? 'true' : 'false') ?>,
      'enableDelete': <?php echo ( $this->canUntagGlobal ? 'true' : 'false') ?>
    });

    var onclickNext = $('media_photo_next').getProperty('onclick');
    taggerInstanceSeaocore.addEvents({
      'onBegin': function() {
        $('media_photo_next').setProperty('onclick', '');
      },
      'onEnd': function() {
        $('media_photo_next').setProperty('onclick', onclickNext);
      }
    });

  });

  function saveeditDescription()
  {
    var photo_id = '<?php echo $this->photo->getIdentity(); ?>';
    var str = document.getElementById('editor_seaocore_description').value.replace('/\n/g', '<br />');
    var str_temp = document.getElementById('editor_seaocore_description').value;
    var resourcetype = '<?php echo $this->resource_type; ?>';
    if (document.getElementById('seaocore_description_loading'))
      document.getElementById('seaocore_description_loading').style.display = "";
    document.getElementById('edit_seaocore_description').style.display = "none";
    en4.core.request.send(new Request.HTML({
      url: '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'photo', 'action' => 'edit-description'), 'default', true) ?>',
      data: {
        format: 'html',
        text_string: str_temp,
        photo_id: photo_id,
        resource_type: resourcetype
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if (str == '')
          str_temp = '<a href="javascript:void(0);" onclick="showeditDescription()" ><?php echo $this->string()->escapeJavascript($this->translate('Add a caption')); ?> </a>';
        document.getElementById('seaocore_description').innerHTML = str_temp;
        showeditDescription();
      }
    }));
  }

  function saveeditTitle()
  {
    var photo_id = '<?php echo $this->photo->getIdentity(); ?>';
    var str = document.getElementById('editor_seaocore_title').value.replace('/\n/g', '<br />');
    var str_temp = document.getElementById('editor_seaocore_title').value;
    var resourcetype = '<?php echo $this->resource_type; ?>';
    if (document.getElementById('seaocore_title_loading'))
      document.getElementById('seaocore_title_loading').style.display = "";
    document.getElementById('edit_seaocore_title').style.display = "none";
    en4.core.request.send(new Request.HTML({
      url: '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'photo', 'action' => 'edit-title'), 'default', true) ?>',
      data: {
        format: 'html',
        text_string: str_temp,
        photo_id: photo_id,
        resource_type: resourcetype
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if (str == '')
          str_temp = '<a href="javascript:void(0);" onclick="showeditTitle()" ><?php echo $this->string()->escapeJavascript($this->translate('Add a title')); ?> </a>';
        document.getElementById('seaocore_title').innerHTML = str_temp;
        showeditTitle();
      }
    }));
  }

  window.addEvent('keyup', function(e) {
    if (e.target.get('tag') == 'html' ||
            e.target.get('tag') == 'body' ||
            e.target.get('tag') == 'a') {
      if (e.key == 'right') {
        photopaginationSocialenginealbum(getNextPhotoSocialenginealbum(), '<?php echo $this->module_name ?>', '<?php echo $this->tab ?>');
      } else if (e.key == 'left') {
        photopaginationSocialenginealbum(getPrevPhotoSocialenginealbum(), '<?php echo $this->module_name ?>', '<?php echo $this->tab ?>');
      }
    }

    if (e.key == 'esc') {
      closeSEALightBoxAlbum();
    }
  });

  function getPrevPhotoSocialenginealbum() {
    return '<?php echo $this->escape(Engine_Api::_()->getApi('PhotoInLightbox', 'seaocore')->getSocialEngineAddOnsLightBoxPhotoHref($this->prevPhoto, array_merge($this->params, array('offset' => $this->PrevOffset)))) ?>';
  }

  function getNextPhotoSocialenginealbum() {
    return '<?php echo $this->escape(Engine_Api::_()->getApi('PhotoInLightbox', 'seaocore')->getSocialEngineAddOnsLightBoxPhotoHref($this->nextPhoto, array_merge($this->params, array('offset' => $this->NextOffset)))) ?>';
  }


  function showSmoothbox(photo_id, action, type, id, url)
  {

    var tab = "<?php echo $this->tab; ?>";

    if (action == 'profilephoto') {
      var url = photo_id;
      Smoothbox.open(url);
      parent.Smoothbox.close;
    }
    else if (action == 'report') {
      Smoothbox.open(en4.core.baseUrl + 'core/report/create/subject/' + photo_id + '/tab/' + tab + '/format/smoothbox');
      parent.Smoothbox.close;
    }
    else if (action == 'share') {
      Smoothbox.open(en4.core.baseUrl + 'activity/index/share/type/' + type + '/id/' + id + '/tab/' + tab + '/format/smoothbox');
      parent.Smoothbox.close;
    }
    else if (action == 'edit') {
      Smoothbox.open(url);
      parent.Smoothbox.close;
    }
    else if (action == 'delete') {
      Smoothbox.open(url);
      parent.Smoothbox.close;
    }
  }
  ;

</script>

<script type="text/javascript">
  function featuredPhoto()
  {
    en4.core.request.send(new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + 'sitealbum/photo/featured',
      'data': {
        format: 'html',
        'subject': '<?php echo $this->subject()->getGuid() ?>'
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if ($('featured_sitealbum_photo').style.display == 'none') {
          $('featured_sitealbum_photo').style.display = "";
          $('un_featured_sitealbum_photo').style.display = "none";
        } else {
          $('un_featured_sitealbum_photo').style.display = "";
          $('featured_sitealbum_photo').style.display = "none";
        }
      }
    }), true);

    return false;
  }

  function featuredpagealbumPhoto(photo_id)
  {
    en4.core.request.send(new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + 'sitepage/photo/featured',
      'data': {
        format: 'html',
        'photo_id': photo_id
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if ($('featured_sitepagealbum_photo').style.display == 'none') {
          $('featured_sitepagealbum_photo').style.display = "";
          $('un_featured_sitepagealbum_photo').style.display = "none";
        } else {
          $('un_featured_sitepagealbum_photo').style.display = "";
          $('featured_sitepagealbum_photo').style.display = "none";
        }
      }
    }), true);

    return false;
  }

  function featuredbusinessalbumPhoto(photo_id)
  {
    en4.core.request.send(new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + 'sitebusiness/photo/featured',
      'data': {
        format: 'html',
        'photo_id': photo_id
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if ($('featured_sitebusinessalbum_photo').style.display == 'none') {
          $('featured_sitebusinessalbum_photo').style.display = "";
          $('un_featured_sitebusinessalbum_photo').style.display = "none";
        } else {
          $('un_featured_sitebusinessalbum_photo').style.display = "";
          $('featured_sitebusinessalbum_photo').style.display = "none";
        }
      }
    }), true);

    return false;
  }
  function featuredgroupalbumPhoto(photo_id)
  {
    en4.core.request.send(new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + 'sitegroup/photo/featured',
      'data': {
        format: 'html',
        'photo_id': photo_id
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if ($('featured_sitegroupalbum_photo').style.display == 'none') {
          $('featured_sitegroupalbum_photo').style.display = "";
          $('un_featured_sitegroupalbum_photo').style.display = "none";
        } else {
          $('un_featured_sitegroupalbum_photo').style.display = "";
          $('featured_sitegroupalbum_photo').style.display = "none";
        }
      }
    }), true);

    return false;
  }
  /*  
   EDIT THE TITLE
   */
  function showeditTitle() {
    if (document.getElementById('edit_seaocore_title')) {
      if (document.getElementById('link_seaocore_title').style.display == "block") {
        document.getElementById('link_seaocore_title').style.display = "none";
        document.getElementById('edit_seaocore_title').style.display = "block";
        $('editor_seaocore_title').focus();
      } else {
        document.getElementById('link_seaocore_title').style.display = "block";
        document.getElementById('edit_seaocore_title').style.display = "none";
      }

      if (document.getElementById('seaocore_title_loading'))
        document.getElementById('seaocore_title_loading').style.display = "none";
    }
  }


  /*  
   FUNCTION FOR ROTATING AND FLIPING THE IMAGES
   */
  en4.seaocore.photolightbox = {
    rotate: function(photo_id, angle, resourcetype) {
      request = new Request.JSON({
        url: en4.core.baseUrl + 'seaocore/photo/rotate',
        data: {
          format: 'json',
          photo_id: photo_id,
          angle: angle,
          resource_type: resourcetype
        },
        onComplete: function(response) {
          // Check status
          if ($type(response) == 'object' &&
                  $type(response.status) &&
                  response.status == false) {
            en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
            return;
          } else if ($type(response) != 'object' ||
                  !$type(response.status)) {
            en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
            return;
          }

          // Ok, let's refresh the page I guess
          $('canReloadSeaocore').value = 1;
          $('media_photo').src = response.href;
          $('media_photo').style.marginTop = "0px";
        }
      });
      request.send();
      return request;
    },
    flip: function(photo_id, direction, resourcetype) {
      request = new Request.JSON({
        url: en4.core.baseUrl + 'seaocore/photo/flip',
        data: {
          format: 'json',
          photo_id: photo_id,
          direction: direction,
          resource_type: resourcetype
        },
        onComplete: function(response) {
          // Check status
          if ($type(response) == 'object' &&
                  $type(response.status) &&
                  response.status == false) {
            en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
            return;
          } else if ($type(response) != 'object' ||
                  !$type(response.status)) {
            en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
            return;
          }

          // Ok, let's refresh the page I guess     
          $('canReloadSeaocore').value = 1;
          $('media_photo').src = response.href;
          $('media_photo').style.marginTop = "0px";
        }
      });
      request.send();
      return request;
    }
  };

  en4.seaocore.photolightbox.comments = {
    like: function(type, id, comment_id) {
      en4.core.request.send(new Request.JSON({
        url: en4.core.baseUrl + 'core/comment/like',
        data: {
          format: 'json',
          type: type,
          id: id,
          comment_id: 0
        },
        onSuccess: function(responseJSON) {
          if ($type(responseJSON) == 'object' && $type(responseJSON.status)) {
            if ($(type + 'like_link'))
              $(type + 'like_link').style.display = "none";
            if ($(type + 'unlike_link'))
              $(type + 'unlike_link').style.display = "inline-block";
          }
        }
      }), {
        'element': $('comments')
      }, true);
    },
    unlike: function(type, id, comment_id) {
      en4.core.request.send(new Request.JSON({
        url: en4.core.baseUrl + 'core/comment/unlike',
        data: {
          format: 'json',
          type: type,
          id: id,
          comment_id: comment_id
        },
        onSuccess: function(responseJSON) {
          if ($type(responseJSON) == 'object' && $type(responseJSON.status)) {
            if ($(type + 'unlike_link'))
              $(type + 'unlike_link').style.display = "none";
            if ($(type + 'like_link'))
              $(type + 'like_link').style.display = "inline-block";
          }
        }
      }), {
        'element': $('comments')
      }, true);
    }


  };
</script>