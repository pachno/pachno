<?php

/**
 * @var array $options
 * @var \pachno\core\entities\Issue $issue
 * @var \pachno\core\entities\Comment $comment
 * @var \pachno\core\entities\User $pachno_user
 * @var string $comment_count_div
 * @var string $mentionable_target_type
 */

?>
<?php $options = (isset($issue)) ? ['issue' => $issue] : []; ?>
<?php if ($comment->isViewableByUser($pachno_user)): ?>
    <div class="comment-container <?php if ($comment->isSystemComment()) echo 'system-comment '; if (!$comment->isPublic()) echo 'private-comment '; ?> syntax_<?= \pachno\core\framework\Settings::getSyntaxClass($comment->getSyntax()); ?>" id="comment_<?= $comment->getID(); ?>">
        <div id="comment_view_<?= $comment->getID(); ?>" class="comment">
            <?php include_component('main/comment', ['comment' => $comment, 'options' => $options, 'comment_count_div' => $comment_count_div]); ?>
            <div class="comment-replies" id="comment_<?= $comment->getID(); ?>_replies">
                <?php foreach ($comment->getReplies() as $reply): ?>
                    <?php include_component('main/comment', ['comment' => $reply, 'options' => $options, 'comment_count_div' => $comment_count_div]); ?>
                <?php endforeach; ?>
            </div>
            <?php if (!$comment->isSystemComment() && $pachno_user->canPostComments() && ((\pachno\core\framework\Context::isProjectContext() && !\pachno\core\framework\Context::getCurrentProject()->isArchived()) || !\pachno\core\framework\Context::isProjectContext())): ?>
                <div class="reply-container">
                    <?php include_component('main/replycomment', ['comment' => $comment, 'mentionable_target_type' => isset($mentionable_target_type) ? $mentionable_target_type : $comment->getTargetType()]); ?>
                    <div class="fake-reply">
                        <div class="avatar-container"><?php echo image_tag($pachno_user->getAvatarURL(), ['alt' => ' ', 'class' => 'avatar small'], true); ?></div>
                        <a href="javascript:void(0);" onclick="$$('.comment-editor').each(function (elm) { elm.removeClass('active'); });$('#comment_reply_<?= $comment->getID(); ?>').addClass('active');$('#comment_reply_bodybox_<?= $comment->getID(); ?>').focus();"><?= __('Reply ...'); ?></a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
