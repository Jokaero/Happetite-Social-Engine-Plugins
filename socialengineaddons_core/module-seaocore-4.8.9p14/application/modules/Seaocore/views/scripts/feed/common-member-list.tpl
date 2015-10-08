<?php
$info_values =
     Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.action.link',
				array("poke" => "poke", "share" => "share", "message" => "message", "addfriend" => "addfriend",
				"suggestion" => "suggestion")); ?>
				
<?php //if(!empty ($this->showViewMore)): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
    hideViewMoreLink();
    });
    function getNextPageViewMoreResults(){
        return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
    }
    function hideViewMoreLink(){
        if($('friend_pops_view_more'))
            $('friend_pops_view_more').style.display = '<?php echo ( $this->paginator->count() ==
$this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>';
    }

    function viewMoreFriend()
    {
    var resouce_id = '<?php echo $this->resouce_id; ?>';
     var resouce_type = '<?php echo $this->resouce_type; ?>';
    document.getElementById('friend_pops_view_more').style.display ='none';
    document.getElementById('friends_pops_loding_image').style.display ='';
    en4.core.request.send(new Request.HTML({
        method : 'post',
        'url' : en4.core.baseUrl + 'seaocore/feed/common-member-list/resource_id/' + resouce_id
         + '/resouce_type/' + resouce_type ,
        'data' : {
            format : 'html',
            showViewMore : 1,
            page: getNextPageViewMoreResults()
        },
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {

            document.getElementById('results_friend').innerHTML =
document.getElementById('results_friend').innerHTML + responseHTML;
            document.getElementById('friend_pops_view_more').destroy();
            document.getElementById('friends_pops_loding_image').style.display ='none';
        }
    }));

    return false;

  }
  </script>
<?php //endif; ?>

<?php if (empty($this->showViewMore)): ?>
  <div class="seaocore_members_popup seaocore_members_popup_notbs">
    <div class="top">
      <div class="heading">
      <?php if ($this->resouce_type ==  'event') : ?>
				<?php echo $this->translate('Friends Who Are Attending')?>
			<?php elseif ($this->resouce_type ==  'group') : ?>
				<?php echo $this->translate('Friends Who Are Members')?>
      <?php else : ?>
				<?php echo $this->translate('Friends Who Like This')?>
      <?php endif; ?>
      <?php //echo $this->result->getTitle();

        //$this->htmlLink($this->result->getHref(), $this->result->getTitle(), array('title'=> $this->result->getTitle(), 'target' => '_blank'));?>
      </div>
    </div>
    <div class="seaocore_members_popup_content" id="results_friend">
<?php endif; ?>

<?php foreach( $this->paginator as $value ):
	if ($this->resouce_type ==  'event' || $this->resouce_type == 'group' || $this->resouce_type == 'siteevent_event') {
	  $resouceId = $value['user_id'];
// 		$user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
// 		$profile_url = $this->url(array('id' => $value['user_id']), 'user_profile');
	} else {
		$resouceId = $value['poster_id'];
// 		$user_subject = Engine_Api::_()->user()->getUser($value['poster_id']);
// 		$profile_url = $this->url(array('id' => $value['poster_id']), 'user_profile');
	}

		$user_subject = Engine_Api::_()->user()->getUser($resouceId);
		$profile_url = $this->url(array('id' => $resouceId), 'user_profile');
?>
  <div class="item_member_list" id="more_results_shows">
    <div class="item_member_thumb">
      <a href="<?php echo $profile_url ?>"  target="_parent"> <?php echo
$this->itemPhoto($this->user($user_subject), 'thumb.icon') ?></a>
    </div>
    <div class="item_member_option">
			<?php //FOR MESSAGE LINK
// 			if ($this->resouce_type ==  'event' || $this->resouce_type == 'group') {
// 			$item = Engine_Api::_()->getItem('user', $value['user_id']);
// 			} else {
// 			$item = Engine_Api::_()->getItem('user', $value['poster_id']);
// 			}
      $item = Engine_Api::_()->getItem('user', $resouceId);
			if (!empty($info_values) && in_array("message",	$info_values) &&
       (Engine_Api::_()->seaocore()->canSendUserMessage($item))) {
			?>
				<a href="<?php echo $this->base_url ?>/messages/compose/to/<?php echo $resouceId ?>"
        target="_parent" class="buttonlink" style=" background-image:
        url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Messages/externals/images/send.png);">
					<?php echo $this->translate('Message'); ?>
				</a>
			<?php } ?>
		</div>
    <div class="item_member_details">
      <div class="item_member_name">
        <a href="<?php echo $profile_url ?>" target="_parent"><?php echo
$this->user($user_subject)->getTitle() ?></a>
      </div>
    </div>
    <?php //if ($this->resouce_type == 'user') : ?>
		<?php //endif; ?>
  </div>
<?php endforeach;?>

<?php if (empty($this->showViewMore)): ?>
<div class="seaocore_item_list_popup_more" id="friend_pops_view_more" onclick="viewMoreFriend()">
    <?php
    echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
            'id' => 'feed_viewmore_link',
            'class' => 'buttonlink icon_viewmore'
    ))
    ?>
</div>
<div class="seaocore_item_list_popup_more" id="friends_pops_loding_image" style="display: none;">
  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
    <?php echo $this->translate("Loading ...") ?>
</div>

<?php //if (empty($this->showViewMore)): ?>
    </div>
  </div>
  <div class="seaocore_members_popup_bottom">
      <button  onclick='javascript:parent.Smoothbox.close()' ><?php echo $this->translate('Close') ?></button>
  </div>
<?php endif; ?>
<script type="text/javascript">
 function smoothboxclose () {
  parent.window.location.reload();
  parent.Smoothbox.close () ;
 }
</script>