<?php
$info_values = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.action.link',
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
        if($('mutual_friend_pops_view_more'))
            $('mutual_friend_pops_view_more').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>';
    }

    function viewMoreTabMutualFriend()
    {
    var friend_id = '<?php echo $this->friend_id; ?>';
    document.getElementById('mutual_friend_pops_view_more').style.display ='none';
    document.getElementById('mutual_friends_pops_loding_image').style.display ='';
    en4.core.request.send(new Request.HTML({
        method : 'post',
        'url' : en4.core.baseUrl + 'seaocore/feed/more-mutual-friend/id/' + friend_id,
        'data' : {
            format : 'html',
            showViewMore : 1,
            page: getNextPageViewMoreResults()
        },
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {

            document.getElementById('mutual_results_friend').innerHTML = document.getElementById('mutual_results_friend').innerHTML + responseHTML;
            document.getElementById('mutual_friend_pops_view_more').destroy();
            document.getElementById('mutual_friends_pops_loding_image').style.display ='none';
        }
    }));

    return false;

  }
 
  </script>
<?php //endif; ?>

<?php if (empty($this->showViewMore)): ?>
  <div class="seaocore_members_popup seaocore_members_popup_notbs">
    <div class="top">
      <div class="heading"><?php echo $this->translate('Mutual Friends')?>
      <?php //echo $this->result->getTitle();

        //$this->htmlLink($this->result->getHref(), $this->result->getTitle(), array('title'=> $this->result->getTitle(), 'target' => '_blank'));?>
      </div>
    </div>
    <div class="seaocore_members_popup_content" id="mutual_results_friend">
<?php endif; ?>

<?php foreach( $this->paginator as $value ):
  $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
  $profile_url = $this->url(array('id' => $value['user_id']), 'user_profile');
?>

  <div class="item_member_list" id="more_results_shows">
    <div class="item_member_thumb">
      <a href="<?php echo $profile_url ?>"  target="_parent"> <?php echo
$this->itemPhoto($this->user($user_subject), 'thumb.icon') ?></a>
    </div>
    <div class="item_member_option">
			<?php //FOR MESSAGE LINK
			$item = Engine_Api::_()->getItem('user', $value['user_id']);
			if (!empty($info_values) && in_array("message",	$info_values) &&
       (Engine_Api::_()->seaocore()->canSendUserMessage($item))) {
			?>
				<a href="<?php echo $this->base_url ?>/messages/compose/to/<?php echo $value['user_id'] ?>" target="_parent" class="buttonlink" style=" background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Messages/externals/images/send.png);">
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
  </div>

<?php endforeach;?>

<?php if (empty($this->showViewMore)): ?>
<div class="seaocore_item_list_popup_more" id="mutual_friend_pops_view_more" onclick="viewMoreTabMutualFriend()" >
    <?php
    echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
            'id' => 'feed_viewmore_link',
            'class' => 'buttonlink icon_viewmore'
    ))
    ?>
</div>
<div class="seaocore_item_list_popup_more" id="mutual_friends_pops_loding_image" style="display: none;">
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