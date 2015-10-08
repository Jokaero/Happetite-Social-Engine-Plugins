<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: list.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<?php $composerOptions = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.options', array("emotions", "withtags")); 
$allowEmotionsIcon = in_array("emotions", $composerOptions)
?>

<?php $this->headTranslate(array('Are you sure you want to delete this?')); ?>

<?php if( !$this->page ): ?>
  <div class='comments comments_<?php echo $this->subject->getGuid();?>' id="comments_<?php echo $this->element_id ?>">
<?php endif; ?>
    
<div class='comments_options'>
  <span ><?php echo $this->translate(array('%s comment', '%s comments',
$this->comments->getTotalItemCount()), $this->locale()->toNumber($this->comments->getTotalItemCount()))
?></span>
</div>
    
<ul>
  <?php if( $this->comments->getTotalItemCount() > 0 ):?>

    <?php if( $this->page && $this->comments->getCurrentPageNumber() > 1 ): ?>
      <li>
        <div> </div>
        <div class="comments_viewall">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View previous comments'),
array(
            'onclick' => 'en4.seaocorepinboard.comments.loadComments("'.$this->subject->getType().'",
"'.$this->subject->getIdentity().'", "'.($this->page - 1).'","'.$this->widget_id.'")'
          )) ?>
        </div>
      </li>
    <?php endif; ?>

    <?php if( !$this->page && $this->comments->getCurrentPageNumber() < $this->comments->count() ): ?>
      <li>
        <div> </div>
        <div class="comments_viewall">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View more comments'), array(
            'onclick' => 'en4.seaocorepinboard.comments.loadComments("'.$this->subject->getType().'",
"'.$this->subject->getIdentity().'", "'.($this->comments->getCurrentPageNumber()).'","'.$this->widget_id .'")'
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
      
    <li class="comment-<?php echo $comment->comment_id ?>" id="comment-<?php echo $comment->comment_id ?>">
      <?php if( $canDelete ): ?>
         <a href="javascript:void(0);" onclick="en4.seaocorepinboard.comments.deleteComment('<?php
  echo $this->subject->getType()?>', '<?php echo $this->subject->getIdentity() ?>', '<?php echo
  $comment->comment_id ?>', '<?php echo
  $this->widget_id ?>')" class="seaocore_comment_remove" title="<?php echo $this->translate('delete') ?>">
         </a>
      <?php endif; ?>
      
      <div class="comments_author_photo">
        <?php echo $this->htmlLink($poster->getHref(),
          $this->itemPhoto($poster, 'thumb.icon', $poster->getTitle())
        ) ?>
      </div>
      <div class="comments_info">
        <span class='comments_author'><?php echo $this->htmlLink($poster->getHref(), $poster->getTitle());
?></span>
         <span class="comments_body">
           <?php //echo $this->viewMore($comment->body, 70, 5000) ?>
           
           <?php echo $allowEmotionsIcon ? $this->smileyToEmoticons($this->viewMore($comment->body, 70, 5000)) : $this->viewMore($comment->body); ?>
         </span> 
        <div class="comments_date">
        <?php if( $this->canComment ||$comment->likes()->getLikeCount() > 0 ): ?>
          <?php if( $this->canComment ):
              $isLiked = $comment->likes()->isLike($this->viewer());
              ?>
            <?php if( !$isLiked ): ?>
              <a href="javascript:void(0)" onclick="en4.seaocorepinboard.comments.like(<?php echo
sprintf("'%s', %d, %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $this->widget_id, $comment->getIdentity())
?>)">
                <?php echo $this->translate('like') ?>
              </a>
            <?php else: ?>
              <a href="javascript:void(0)" onclick="en4.seaocorepinboard.comments.unlike(<?php echo
sprintf("'%s', %d, %d, %d ", $this->subject->getType(), $this->subject->getIdentity(), $this->widget_id, $comment->getIdentity())
?>)">
                <?php echo $this->translate('unlike') ?>
              </a>
            <?php endif ?>
            &middot;
          <?php endif ?>
          
          <?php if( $comment->likes()->getLikeCount() > 0 ): ?>
            
            <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>"
class="comments_comment_likes">
              <?php echo $this->translate(array('%s likes this', '%s like this',
$comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
            </a>
            &middot;
          <?php endif; ?>
        <?php endif; ?>
        <?php echo $this->timestamp($comment->creation_date); ?>
        </div>
                
      </div>
    </li>
  <?php endfor; ?>

  <?php if( $this->page && $this->comments->getCurrentPageNumber() < $this->comments->count() ): ?>
    <li>
      <div> </div>
      <div class="comments_viewall">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View later comments'), array(
          'onclick' => 'en4.seaocorepinboard.comments.loadComments("'.$this->subject->getType().'",
"'.$this->subject->getIdentity().'", "'.($this->page + 1).'","'. $this->widget_id .'")'
        )) ?>
      </div>
    </li>
  <?php endif; ?>
        
  <?php if( isset($this->form) && $this->canComment) : ?>
    <li id='comment-form-open-li_<?php echo $this->element_id ?>' onclick="en4.seaocorepinboard.comments.addComment('<?php echo $this->element_id ?>');"> 
      <div/></div>
      <div class="seaocore_comment_box seaocore_txt_light"><?php echo $this->translate('Post a comment...') ?></div>
    </li>
    <?php endif ?> 
  <?php endif; ?>
     <li id='comment-form-loading-li_<?php echo $this->element_id ?>' style="display: none;"> 
      <div class="comments_author_photo">
          <?php echo $this->htmlLink($this->viewer()->getHref(),
            $this->itemPhoto($this->viewer(), 'thumb.icon', $this->viewer()->getTitle())
          ) ?>
        </div>
        <div class="comments_info">
          <span class='comments_author'><?php echo $this->htmlLink($this->viewer()->getHref(), $this->viewer()->getTitle());
?></span>
          <span class="comments_body">

          </span> 
          <div class="comments_date"><img src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Core/externals/images/loading.gif" /></div>
        </div>
    </li>
</ul>
<?php if($this->canComment):?>    
<script type="text/javascript">
  en4.core.runonce.add(function(){
    $($('comment-form_<?php echo $this->element_id ?>').body).autogrow();
    en4.seaocorepinboard.comments.attachCreateComment($('comment-form_<?php echo $this->element_id ?>'),'<?php echo
$this->subject->getType()?>',<?php echo $this->subject->getIdentity() ?> , '<?php echo $this->widget_id ?>');

  <?php if($this->submit_post):?>
    $('comment-form_<?php echo $this->element_id ?>').style.display = '';$('comment-form_<?php echo
$this->subject->getGuid()?>').body.focus()
    <?php endif; ?>

  });
</script>
<?php endif; ?> 
<?php if( isset($this->form) ) echo $this->form->setAttribs(array('id' =>
'comment-form_'.$this->element_id, 'style' =>
'display:none;'))->render() ?>

<?php if( !$this->page ): ?>
  </div>
<?php endif; ?>
