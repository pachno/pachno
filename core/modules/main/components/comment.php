<?php

    /**
     * @var \pachno\core\entities\Comment $comment
     * @var \pachno\core\entities\User $pachno_user
     */

?>
<?php if ($comment->isReply()): ?>
    <div class="comment-container reply <?php if (!$comment->isPublic()): ?> private_comment<?php endif; ?> syntax_<?= \pachno\core\framework\Settings::getSyntaxClass($comment->getSyntax()); ?>" id="comment_<?= $comment->getID(); ?>">
        <div id="comment_view_<?= $comment->getID(); ?>" class="comment">
<?php endif; ?>
            <div id="comment_<?= $comment->getID(); ?>_header" class="commentheader">
                <div class="header">
                    <?php if(!$comment->isPublic()): ?>
                        <?= fa_image_tag('lock', ['class' => 'comment_restricted', 'title' => __('Access to this comment is restricted')]); ?>
                    <?php endif; ?>
                    <?= include_component('main/userdropdown', array('user' => $comment->getPostedBy(), 'size' => 'large')); ?>
                </div>
                <div class="date" id="comment_<?= $comment->getID(); ?>_date">
                    <?= \pachno\core\framework\Context::getI18n()->formatTime($comment->getPosted(), 25); ?>
                </div>
            </div>
            <?php include_component('main/editcomment', ['comment' => $comment, 'mentionable_target_type' => isset($mentionable_target_type) ? $mentionable_target_type : $comment->getTargetType()]); ?>
            <div class="body article" id="comment_<?= $comment->getID(); ?>_body">
                <div class="content" id="comment_<?= $comment->getID(); ?>_content">
                    <?= $comment->getParsedContent($options); ?>
                </div>
                <?php if ((\pachno\core\framework\Context::isProjectContext() && !\pachno\core\framework\Context::getCurrentProject()->isArchived()) || !\pachno\core\framework\Context::isProjectContext()) : ?>
                    <div class="tools action-buttons">
                        <a class="action-button" href="#comment_<?= $comment->getID(); ?>"><?= fa_image_tag('link'); ?></a>
                        <?php if ($comment->canUserEdit($pachno_user)): ?>
                            <a class="action-button" href="javascript:void(0)" onclick="$('.comment-editor').removeClass('active');$('#comment_edit_<?= $comment->getID(); ?>').addClass('active');"><?= fa_image_tag('edit'); ?></a>
                        <?php endif; ?>
                        <?php if ($comment->canUserDelete($pachno_user)): ?>
                            <?= javascript_link_tag(fa_image_tag('trash-alt'), ['class' => 'action-button', 'onclick' => "Pachno.UI.Dialog.show('".__('Do you really want to delete this comment?')."', '".__('Please confirm that you want to delete this comment.')."', {yes: {click: function() {Pachno.trigger(Pachno.EVENTS.comment.remove, { url: '".make_url('comment_delete', ['comment_applies_id' => $comment->getTargetID(), 'comment_applies_type' => $comment->getTargetType(), 'comment_module' => $comment->getModuleName(), 'comment_id' => $comment->getID()])."', comment_id: ".$comment->getID().", count_element: '".$comment_count_div."'}); }}, no: { click: Pachno.UI.Dialog.dismiss }});"]); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if ($comment->hasAssociatedChanges()): ?>
                    <h5 class="change-list"><?= __('Changes: %list_of_changes', ['%list_of_changes' => '']); ?></h5>
                    <ul class="comment-change-list">
                        <?php foreach ($comment->getLogItems() as $item): ?>
                            <?php if (!$item instanceof \pachno\core\entities\LogItem) continue; ?>
                            <?php /* Pass item's own time in order to prevent issuelogitem template from including timestamp for the item. The timestamp span is additionally hidden by the CSS.*/ ?>
                            <?php $previous_time = $item->getTime(); ?>
                            <?php include_component('main/issuelogitem', compact('item', 'previous_time')); ?>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
<?php if ($comment->isReply()): ?>
        </div>
    </div>
<?php endif; ?>