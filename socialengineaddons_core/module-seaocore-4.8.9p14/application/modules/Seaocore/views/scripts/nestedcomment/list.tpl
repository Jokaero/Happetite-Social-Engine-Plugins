<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: list.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js');
?>
<?php $this->headLink()->appendStylesheet($this->seaddonsBaseUrl(). '/application/modules/Seaocore/externals/styles/style_comment.css'); ?>

<script type="text/javascript">
  var ReplyLikesTooltips;
  var seaocore_content_type = '<?php echo $this->subject->getType(); ?>';
  en4.core.runonce.add(function() {
    // Scroll to reply
    if( window.location.hash != '' ) {
      var hel = $(window.location.hash);
      if( hel ) {
        window.scrollTo(hel);
      }
    }
    // Add hover event to get likes
    $$('.seaocore_replies_comment_likes').addEvent('mouseover', function(event) {
      var el = $(event.target);
      if( !el.retrieve('tip-loaded', false) ) {
        el.store('tip-loaded', true);
        el.store('tip:title', '<?php echo $this->translate('Loading...') ?>');
        el.store('tip:text', '');
        var id = el.get('id').match(/\d+/)[0];
        // Load the likes
        var url = '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'nestedcomment', 'action' => 'get-likes'), 'default', true) ?>';
        var req = new Request.JSON({
          url : url,
          data : {
            format : 'json',
            type : 'core_comment',
            id : id
            //type : '<?php //echo $this->subject->getType() ?>',
            //id : '<?php //echo $this->subject->getIdentity() ?>',
            //comment_id : id
          },
          onComplete : function(responseJSON) {
             el.store('tip:title', responseJSON.body);
             el.store('tip:text', '');
             ReplyLikesTooltips.elementEnter(event, el); // Force it to update the text
						 el.addEvents({
               'mouseleave': function() {                
               ReplyLikesTooltips.hide(el);                    
               }
              });
          }
        });
        req.send();
      }
    });
    // Add tooltips
    ReplyLikesTooltips = new Tips($$('.seaocore_replies_comment_likes'), {
      fixed : true,
      title:'',
      className : 'seaocore_replies_comment_likes',
      showDelay : 0,
      offset : {
        'x' : 48,
        'y' : 16
      }
    });
     // Enable links
    $$('.seaocore_replies_body').enableLinks();
  });
</script>

<?php $this->headTranslate(array('Are you sure you want to delete this?')); ?>
<?php if( empty($this->parent_div)): ?>
<?php if (empty($this->tempComment) && empty($this->canComment)) : ?>
    <div id="parent_div" class="seaocore_replies_wrapper dnone">
<?php else : ?>
    <div id="parent_div" class="seaocore_replies_wrapper">
<?php endif; ?>
<?php endif; ?>
<?php if( !$this->page): ?>
<div class='seaocore_replies <?php if($this->parent_comment_id): ?>seaocore_replies_child<?php endif; ?>' id="comments_<?php echo $this->nested_comment_id?>">
<?php endif; ?>
  <?php if(empty($this->parent_comment_id)): ?>
		<div class='seaocore_replies_options seaocore_txt_light'>
			 <span><?php echo $this->translate(array('%s comment', '%s comments',
$this->commentsCount), $this->locale()->toNumber($this->commentsCount))
?></span>

		<?php if( $this->viewer()->getIdentity() && $this->canComment ): ?>
      <?php if( $this->subject->likes()->isLike($this->viewer()) ): ?>
        - <a href="javascript:void(0);" onclick="en4.seaocore.nestedcomments.unlike('<?php echo
$this->subject->getType()?>', '<?php echo $this->subject->getIdentity() ?>', '', '<?php echo $this->order?>', '<?php echo $this->parent_comment_id?>', 'parent');"><?php echo
$this->translate('Unlike This') ?></a>
<div id="unlike_comments" style="display:none;"></div>
      <?php else: ?>
        - <a href="javascript:void(0);" onclick="en4.seaocore.nestedcomments.like('<?php echo
$this->subject->getType()?>', '<?php echo $this->subject->getIdentity() ?>','', '<?php echo $this->order?>', '<?php echo $this->parent_comment_id?>', 'parent');"><?php echo
$this->translate('Like This') ?></a>
<div id="like_comments" style="display:none;"></div>
      <?php endif; ?>
    <?php endif; ?>

		</div>
  <?php else: ?>
   <!-- <div class='nested_seaocore_replies_options'></div>-->
  <?php endif; ?>
  <?php if( isset($this->formComment) ):
			if($this->parent_comment_id):
				echo $this->formComment->setAttribs(array('id' => 'comments-form_'.$this->nested_comment_id, 'style' => 'display:none;'))->render();
				else:
				echo $this->formComment->setAttribs(array('id' => 'comments-form_'.$this->nested_comment_id))->render();
			endif; ?>
	<?php endif;?>
 
 <ul>
    <?php if((empty($this->parent_comment_id) && $this->likes->getTotalItemCount() > 0) || ($this->comments->getTotalItemCount() > 0 )):?>
			<li>
				<?php if( empty($this->parent_comment_id) && $this->likes->getTotalItemCount() > 0 ): // LIKES ------------- ?>
					<div class="seaocore_replies_likes fleft">
						<?php if( $this->viewAllLikes || $this->likes->getTotalItemCount() <= 3 ): ?>
							<?php $this->likes->setItemCountPerPage($this->likes->getTotalItemCount()) ?>
							<?php echo $this->translate(array('%s likes this', '%s like this', $this->likes->getTotalItemCount()), $this->fluentList($this->subject->likes()->getAllLikesUsers())) ?>
						<?php else: ?>
								<?php echo $this->htmlLink('javascript:void(0);', 
									$this->translate(array('%s person likes this', '%s people like this', $this->likes->getTotalItemCount()), $this->locale()->toNumber($this->likes->getTotalItemCount())),
									array('onclick' => 'en4.seaocore.nestedcomments.showLikes("'.$this->subject->getType().'", "'.$this->subject->getIdentity().'",  "'.$this->order.'", "'.$this->parent_comment_id.'");')
							); ?>
						<?php endif; ?>
					</div>    
				<?php endif; ?>
				<?php if( $this->comments->getTotalItemCount() > 1 ): // REPLIES ------- ?>
					<?php if(empty($this->parent_comment_id)):?>                             
						<div class="seaocore_replies_sorting fright">
							<div class="mright5" id="sort_<?php echo $this->nested_comment_id;?>" style="display:none;"></div>
							<b><?php echo $this->translate("Sort By"); ?></b>
							<select onchange="sortComments(this.value, '<?php echo $this->subject->getType(); ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $this->parent_comment_id?>');" name="sortComments" class="searchTarget"> 
							<option value="DESC" <?php if($this->order == 'DESC'):?> selected="selected" <?php endif;?>><?php echo $this->translate("Newest"); ?></option> 
							<option value="ASC" <?php if($this->order == 'ASC'):?> selected="selected" <?php endif;?>><?php echo $this->translate("Oldest"); ?></option>  
							</select>
						</div>
					<?php endif;?>
				<?php endif;?>
			</li>  
    <?php endif;?>
    <?php if( $this->comments->getTotalItemCount() > 0 ): // REPLIES ------- ?>

      <?php if( $this->page && $this->comments->getCurrentPageNumber() > 1 ): ?>
        <li>
          <div> </div>
          <div class="seaocore_replies_viewall">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View previous comments'), array(
              'onclick' => 'en4.seaocore.nestedcomments.loadComments("'.$this->subject->getType().'", "'.$this->subject->getIdentity().'", "'.($this->page - 1).'", "'.$this->order.'", "'.$this->parent_comment_id.'")', 'class' => 'mright5'
            )) ?>
          	<div id="view_previous_comments_<?php echo $this->parent_comment_id;?>" style="display:none;"></div>
          </div>
        </li>
      <?php endif; ?>

      <?php if( $this->comments->getCurrentPageNumber() < $this->comments->count() ): ?>
        <li>
          <div> </div>
          <div class="seaocore_replies_viewall">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View more comments'), array(
              'onclick' => 'en4.seaocore.nestedcomments.loadComments("'.$this->subject->getType().'", "'.$this->subject->getIdentity().'", "'.($this->comments->getCurrentPageNumber()).'", "'.$this->order.'", "'.$this->parent_comment_id.'")', 'class' => 'mright5'
            )) ?>
					 <div id="view_more_comments_<?php echo $this->parent_comment_id;?>" style="display:none;"></div>
          </div>
        </li>
      <?php endif; ?>

      <?php // Iterate over the replies backwards (or forwards!)
        $replies = $this->comments->getIterator();
        $i = 0;
        $l = count($replies) - 1;
        $d = 1;
        $e = $l + 1;
      for( ; $i != $e; $i += $d ):
        $comment = $replies[$i];
        $poster = $this->item($comment->poster_type, $comment->poster_id);
        $canDelete = ( $this->canDelete || $poster->isSelf($this->viewer()) );

        ?>
        <li id="comment-<?php echo $comment->comment_id ?>" class="seaocore_replies_list">
        	<div class="seaocore_replies_content">
	          <div class="seaocore_replies_author_photo">
	            <?php echo $this->htmlLink($poster->getHref(),
	              $this->itemPhoto($poster, 'thumb.icon', $poster->getTitle())
	            ) ?>
	          </div>
          
	          <div class="seaocore_replies_info">
	          
	          	<span class="seaocore_replies_info_op">
	          	
								<?php //if($this->subject->getType() !== 'seaocore_reply') :?>
								<span class="seaocore_replies_showhide">
									<span class="minus" onclick="showData(1, '<?php echo $comment->comment_id ?>');" id="hide_<?php echo $comment->comment_id ?>" title="<?php echo $this->translate("Collapse");?>"></span> 
									<span class="plus" onclick="showData(0, '<?php echo $comment->comment_id ?>');" id="show_<?php echo $comment->comment_id ?>" style="display:none;" title="<?php echo $this->translate("Expand");?>"></span> 
								</span>	
								<?php //endif;?>
	          	  <?php if($this->viewer_id):?>
									<span class="seaocore_replies_pulldown">
										<div class="seaocore_dropdown_menu_wrapper">
											<div class="seaocore_dropdown_menu">
												<ul>     
												<?php if( $canDelete ): ?>                 
													<li>
															<?php if( $this->parent_comment_id):?>
																<?php $title = $this->translate("Delete Reply");?>
															<?php else:?>
																<?php $title = $this->translate("Delete Comment");?>
															<?php endif;?>
															<a href="javascript:void(0);" title="<?php echo $title;?>" onclick="en4.seaocore.nestedcomments.deleteComment('<?php echo $this->subject->getType()?>', '<?php echo $this->subject->getIdentity() ?>', '<?php echo $comment->comment_id ?>','<?php echo $comment->parent_comment_id ?>')"><?php echo $this->translate('Delete'); ?></a>
														</li>
													<?php endif; ?>
													
														<li>
															<?php echo $this->htmlLink($this->url(array('action' => 'create', 'module' => 'core', 'controller' => 'report', 'subject' => $comment->getGuid()), 'default', true), $this->translate("Report"), array('title' => $this->translate("Report"), 'class' => "smoothbox")) ?>
														</li>
												</ul>
											</div>
										</div>
										<span class="seaocore_dropdown_btn"></span>
									</span>
	              <?php endif; ?>
							</span>
	         
	            <div class='seaocore_replies_author seaocore_txt_light'>
	            	<?php echo $this->htmlLink($poster->getHref(), $poster->getTitle()); ?>
	            	&nbsp;&#183;&nbsp;<?php echo $this->translate("%s",$this->timestamp($comment->creation_date)); ?>
	            </div>
	            <div id="seaocore_data-<?php echo $comment->comment_id ?>">
								<div class="seaocore_replies_comment">
									<?php echo $this->smileyToEmoticons($this->viewMore($comment->body)) ?>
								</div>
	            <div class="seaocore_replies_date seaocore_txt_light">
	             
	              <?php if( $this->canComment ): ?>
	                <?php if ( isset($this->formComment)): ?>
	
	                   <a href='javascript:void(0);' onclick="showReplyForm('<?php echo $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity();?>', '<?php echo $comment->getIdentity()?>');"><?php echo $this->translate('SEREPLY') ?></a> &#183;
	                <?php endif; ?>
	              <?php 
	                $isLiked = $comment->likes()->isLike($this->viewer());
	                ?>
	                
	                <?php if( !$isLiked ): ?>
	                  <a href="javascript:void(0)" onclick="en4.seaocore.nestedcomments.like(<?php echo sprintf("'%s', %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'child')"><?php echo $this->translate('like') ?></a>
	                  <div class="seaocore_commentlike_loading" id="like_comments_<?php echo $comment->getIdentity();?>" style="display:none;"></div>
	                <?php else: ?>
	                  <a href="javascript:void(0)" onclick="en4.seaocore.nestedcomments.unlike(<?php echo sprintf("'%s', %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->order ?>','<?php echo $this->parent_comment_id ?>', 'child')"><?php echo $this->translate('unlike') ?></a>
	                  <div class="seaocore_commentlike_loading" id="unlike_comments_<?php echo $comment->getIdentity();?>" style="display:none;"></div>
	                <?php endif ?>
	              <?php endif ?>
	              <?php if( $comment->likes()->getLikeCount() > 0 ): ?>
	               &#183;
	                <a href="javascript:void(0);" id="replies_reply_likes_<?php echo $comment->comment_id ?>" class="seaocore_replies_comment_likes" title="<?php echo $this->translate('Loading...') ?>">
	                  <?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
	                </a>
	              <?php endif ?>
	            </div>    
						</div>
	          </div>
          </div>
          
					<?php if($this->format):?>
						<?php echo $this->action("list", "nestedcomment", "seaocore", array("type" => $this->subject->getType(), "id" =>
					$this->subject->getIdentity(),'format' =>'html', 'parent_comment_id' => $comment->comment_id, 'page' => 0, 'parent_div' => 1));?>
					<?php else: ?>
           <?php echo $this->action("list", "nestedcomment", "seaocore", array("type" => $this->subject->getType(), "id" =>
					$this->subject->getIdentity(), 'parent_comment_id' => $comment->comment_id, 'page' => 0, 'parent_div' => 1));?>
					<?php  endif;?>
          
        </li>
      <?php endfor; ?>

      <?php if( $this->page && $this->comments->getCurrentPageNumber() < $this->comments->count() ): ?>
        <li>
          <div> </div>
          <div class="seaocore_replies_viewall">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View later comments'), array(
              'onclick' => 'en4.seaocore.nestedcomments.loadComments("'.$this->subject->getType().'", "'.$this->subject->getIdentity().'", "'.($this->page + 1).'", "'.$this->order.'", "'.$this->parent_comment_id.'")', 'class' => 'mright5'
            )) ?>
					<div id="view_later_comments_<?php echo $this->parent_comment_id;?>" style="display:none;"></div>
          </div>
        </li>
      <?php endif; ?>
       </ul>
    <?php endif; ?>


  <script type="text/javascript">
    en4.core.runonce.add(function(){
      $($('comments-form_<?php echo $this->nested_comment_id?>').body).autogrow();
      en4.seaocore.nestedcomments.attachCreateComment($('comments-form_<?php echo $this->nested_comment_id?>'),'<?php echo $this->subject->getType()?>',<?php echo $this->subject->getIdentity() ?>,'<?php echo $this->parent_comment_id ?>');
    });
  </script>
<?php if( !$this->page): ?>
  </div>
<?php endif; ?>
<?php if( empty($this->parent_div)): ?>
  </div>
<?php endif; ?>

<script type="text/javascript">

	function showData(option,id) {
		if(option == 1) {
			$('seaocore_data-'+id).style.display = 'none';
			if($('comment-'+id))
			$('comment-'+id).className="seaocore_replies_list seaocore_comments_hide";      
			$('show_'+id).style.display = 'block';
			$('hide_'+id).style.display = 'none';
		} else {
			$('seaocore_data-'+id).style.display = 'block';
			   
			$('show_'+id).style.display = 'none';
			$('hide_'+id).style.display = 'block';
			if($('comment-'+id))
			$('comment-'+id).className="seaocore_replies_list"; 
		}
	}

  function sortComments(order, type, id, parent_comment_id) {
     en4.seaocore.nestedcomments.loadcommentssortby(type, id, order, parent_comment_id);
  }

  function showReplyForm(type, id, comment_id) {
    if($('comments-form_'+type+'_'+id+'_'+comment_id).style.display == 'none') {
      $('comments-form_'+type+'_'+id+'_'+comment_id).style.display = 'block';
      $('comments-form_'+type+'_'+id+'_'+comment_id).body.focus();
    } 
    else {
      $('comments-form_'+type+'_'+id+'_'+comment_id).style.display = 'none';
    }
  }
  
</script>