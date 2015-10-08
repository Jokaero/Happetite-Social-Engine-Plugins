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

<script type="text/javascript">
  var CommentLikesTooltips;
  en4.core.runonce.add(function() {
    // Scroll to comment
    if( window.location.hash != '' ) {
      var hel = $(window.location.hash);
      if( hel ) {
        window.scrollTo(hel);
      }
    }
    // Add hover event to get likes
    $$('.comments_comment_likes').addEvent('mouseover', function(event) {
      var el = $(event.target);
      if( !el.retrieve('tip-loaded', false) ) {
        el.store('tip-loaded', true);
        el.store('tip:title', '<?php echo $this->translate('Loading...') ?>');
        el.store('tip:text', '');
        var id = el.get('id').match(/\d+/)[0];
        // Load the likes
        var url = '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'comment',
'action' => 'get-likes'), 'default', true) ?>';
        var req = new Request.JSON({
          url : url,
          data : {
            format : 'json',
            type : 'core_comment',
            id : id
            //type : '<?php //echo $this->subject()->getType() ?>',
            //id : '<?php //echo $this->subject()->getIdentity() ?>',
            //comment_id : id
          },
          onComplete : function(responseJSON) {
            el.store('tip:title', responseJSON.body);
            el.store('tip:text', '');
            CommentLikesTooltips.elementEnter(event, el); // Force it to update the text
          }
        });
        req.send();
      }
    });
    // Add tooltips
    CommentLikesTooltips = new Tips($$('.comments_comment_likes'), {
      fixed : true,
      className : 'comments_comment_likes_tips',
      offset : {
        'x' : 48,
        'y' : 16
      }
    });
    // Enable links
    $$('.comments_body').enableLinks();
  });
</script>

<?php $this->headTranslate(array('Are you sure you want to delete this?',)); ?>

<?php if( !$this->page ): ?>
<div class='comments' id="comments_<?php echo $this->subject()->getType()?>_<?php echo
$this->subject()->getIdentity() ?>">
<?php endif; ?>
  <div class='comments_options'>
    <span><?php echo $this->translate(array('%s comment', '%s comments',
$this->comments->getTotalItemCount()), $this->locale()->toNumber($this->comments->getTotalItemCount()))
?></span>

    <?php if( isset($this->form) ): ?>
      - <a href='javascript:void(0);' onclick="if($('comment-form-open-li_<?php echo $this->subject()->getType()?>_<?php echo $this->subject()->getIdentity() ?>')){ $('comment-form-open-li_<?php echo $this->subject()->getType()?>_<?php echo $this->subject()->getIdentity() ?>').style.display = 'none'; } $('comment-form_<?php echo $this->subject()->getType()?>_<?php
echo $this->subject()->getIdentity() ?>').style.display = '';$('comment-form_<?php echo
$this->subject()->getType()?>_<?php echo $this->subject()->getIdentity() ?>').body.focus();"><?php echo
$this->translate('Post Comment') ?></a>
    <?php endif; ?>

    <?php if( $this->viewer()->getIdentity() && $this->canComment ): ?>
      <?php if( $this->subject()->likes()->isLike($this->viewer()) ): ?>
        - <a href="javascript:void(0);" onclick="en4.seaocore.comments.unlike('<?php echo
$this->subject()->getType()?>', '<?php echo $this->subject()->getIdentity() ?>','<?php echo $this->show_bottom_post ?>')"><?php echo
$this->translate('Unlike This') ?></a>
      <?php else: ?>
        - <a href="javascript:void(0);" onclick="en4.seaocore.comments.like('<?php echo
$this->subject()->getType()?>', '<?php echo $this->subject()->getIdentity() ?>','<?php echo $this->show_bottom_post ?>')"><?php echo
$this->translate('Like This') ?></a>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <ul>

    <?php if( $this->likes->getTotalItemCount() > 0 ): // LIKES ------------- ?>
      <li>
        <?php if( $this->viewAllLikes || $this->likes->getTotalItemCount() <= 3 ): ?>
          <?php $this->likes->setItemCountPerPage($this->likes->getTotalItemCount()) ?>
          <div> </div>
          <div class="comments_likes">
            <?php echo $this->translate(array('%s likes this', '%s like this',
$this->likes->getTotalItemCount()), $this->fluentList($this->subject()->likes()->getAllLikesUsers())) ?>
          </div>
        <?php else: ?>
          <div> </div>
          <div class="comments_likes">
            <?php echo $this->htmlLink('javascript:void(0);',
                          $this->translate(array('%s person likes this', '%s people like this',
$this->likes->getTotalItemCount()), $this->locale()->toNumber($this->likes->getTotalItemCount())),
                          array('onclick' =>
'en4.seaocore.comments.showLikes("'.$this->subject()->getType().'",
"'.$this->subject()->getIdentity().'","'. $this->show_bottom_post.'");')
                      ); ?>
          </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if( $this->comments->getTotalItemCount() > 0 ): // COMMENTS ------- ?>

      <?php if( $this->page && $this->comments->getCurrentPageNumber() > 1 ): ?>
        <li>
          <div> </div>
          <div class="comments_viewall">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View previous comments'),
array(
              'onclick' => 'en4.seaocore.comments.loadComments("'.$this->subject()->getType().'",
"'.$this->subject()->getIdentity().'", "'.($this->page - 1).'","'.$this->show_bottom_post.'")'
            )) ?>
          </div>
        </li>
      <?php endif; ?>

      <?php if( !$this->page && $this->comments->getCurrentPageNumber() < $this->comments->count() ): ?>
        <li>
          <div> </div>
          <div class="comments_viewall">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View more comments'), array(
              'onclick' => 'en4.seaocore.comments.loadComments("'.$this->subject()->getType().'",
"'.$this->subject()->getIdentity().'", "'.($this->comments->getCurrentPageNumber()).'","'.$this->show_bottom_post .'")'
            )) ?>
          </div>
        </li>
      <?php endif; ?>

      <?php // Iterate over the comments backwards (or forwards!)
      $comments = $this->comments->getIterator();
      if( $this->page ):
        $i = 0;
        $l = count($comments) - 1;
        $d = 1;
        $e = $l + 1;
      else:
        $i = count($comments) - 1;
        $l = count($comments);
        $d = -1;
        $e = -1;
      endif;
      for( ; $i != $e; $i += $d ):
        $comment = $comments[$i];
        $poster = $this->item($comment->poster_type, $comment->poster_id);

    
     
        $canDelete = ( $this->canDelete || $poster->isSelf($this->viewer()));
        ?>
        <li id="comment-<?php echo $comment->comment_id ?>">
          <div class="comments_author_photo">
            <?php echo $this->htmlLink($poster->getHref(),
              $this->itemPhoto($poster, 'thumb.icon', $poster->getTitle())
            ) ?>
          </div>
          <div class="comments_info">
            <span class='comments_author'><?php echo $this->htmlLink($poster->getHref(), $poster->getTitle());
?></span>
            <?php echo $this->smileyToEmoticons($this->viewMore($comment->body)); ?>
            <div class="comments_date">
              <?php echo $this->timestamp($comment->creation_date); ?>
              <?php if( $canDelete ): ?>
                -
                <a href="javascript:void(0);" onclick="en4.seaocore.comments.deleteComment('<?php
echo $this->subject()->getType()?>', '<?php echo $this->subject()->getIdentity() ?>', '<?php echo
$comment->comment_id ?>')">
                  <?php echo $this->translate('delete') ?>
                </a>
              <?php endif; ?>
              <?php if( $this->canComment ):
                $isLiked = $comment->likes()->isLike($this->viewer());
                ?>
                -
                <?php if( !$isLiked ): ?>
                  <a href="javascript:void(0)" onclick="en4.seaocore.comments.like(<?php echo
sprintf("'%s', %d, %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $this->show_bottom_post, $comment->getIdentity())
?>)">
                    <?php echo $this->translate('like') ?>
                  </a>
                <?php else: ?>
                  <a href="javascript:void(0)" onclick="en4.seaocore.comments.unlike(<?php echo
sprintf("'%s', %d, %d, %d ", $this->subject()->getType(), $this->subject()->getIdentity(), $this->show_bottom_post, $comment->getIdentity())
?>)">
                    <?php echo $this->translate('unlike') ?>
                  </a>
                <?php endif ?>
              <?php endif ?>
              <?php if( $comment->likes()->getLikeCount() > 0 ): ?>
                -
                <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>"
class="comments_comment_likes" title="<?php echo $this->translate('Loading...') ?>">
                  <?php echo $this->translate(array('%s likes this', '%s like this',
$comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
                </a>
              <?php endif ?>
            </div>
          </div>
        </li>
      <?php endfor; ?>

      <?php if( $this->page && $this->comments->getCurrentPageNumber() < $this->comments->count() ): ?>
        <li>
          <div> </div>
          <div class="comments_viewall">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View later comments'), array(
              'onclick' => 'en4.seaocore.comments.loadComments("'.$this->subject()->getType().'",
"'.$this->subject()->getIdentity().'", "'.($this->page + 1).'","'. $this->show_bottom_post .'")'
            )) ?>
          </div>
        </li>
      <?php endif; ?>
        
      <?php if( isset($this->form) && $this->show_bottom_post ) : ?>
      <li id='comment-form-open-li_<?php echo $this->subject()->getType()?>_<?php echo $this->subject()->getIdentity() ?>' onclick="(this).style.display ='none'; $('comment-form_<?php echo $this->subject()->getType()?>_<?php echo $this->subject()->getIdentity() ?>').style.display = '';$('comment-form_<?php echo $this->subject()->getType()?>_<?php echo $this->subject()->getIdentity() ?>').body.focus();"> 
        <div/></div>
        <div style="border: 1px solid #DDD; cursor: text; padding: 2px 7px;  color:#999;" ><?php echo $this->translate('Post Comment') ?></div>
      </li>
      <?php endif ?> 
        
    <?php endif; ?>

  </ul>
  <script type="text/javascript">
    en4.core.runonce.add(function(){
      $($('comment-form_<?php echo $this->subject()->getType()?>_<?php echo $this->subject()->getIdentity()
?>').body).autogrow();
      en4.seaocore.comments.attachCreateComment($('comment-form_<?php echo
$this->subject()->getType()?>_<?php echo $this->subject()->getIdentity() ?>'),'<?php echo
$this->subject()->getType()?>',<?php echo $this->subject()->getIdentity() ?> , '<?php echo $this->show_bottom_post ?>');
    });
  </script>
  <?php if( isset($this->form) ) echo $this->form->setAttribs(array('id' =>
'comment-form_'.$this->subject()->getType()."_".$this->subject()->getIdentity(), 'style' =>
'display:none;'))->render() ?>
<?php if( !$this->page ): ?>
</div>
    <?php endif; ?>