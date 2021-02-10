<div id="comment_reply_<?= $comment->getID(); ?>" class="comment-reply comment-editor editor_container form-container">
    <form id="comment_reply_form_<?= $comment->getID(); ?>" accept-charset="<?= mb_strtoupper(\pachno\core\framework\Context::getI18n()->getCharset()); ?>" action="<?= make_url('comment_add', ['comment_applies_id' => $comment->getTargetID(), 'comment_applies_type' => $comment->getTargetType(), 'comment_module' => $comment->getModuleName()]); ?>" method="post" data-comment-reply-id="<?= $comment->getId(); ?>" data-simple-submit>
        <input type="hidden" name="reply_to_comment_id" value="<?= $comment->getID(); ?>" />
        <div class="form-row">
            <?php include_component('main/textarea', array('area_name' => 'comment_body', 'placeholder' => __('Enter your reply here...'), 'target_type' => isset($mentionable_target_type) ? $mentionable_target_type : $comment->getTargetType(), 'target_id' => $comment->getTargetId(), 'area_id' => 'comment_reply_'.$comment->getID().'_bodybox', 'height' => '200px', 'width' => '100%', 'syntax' => \pachno\core\framework\Settings::getSyntaxClass($comment->getSyntax()), 'value' => '')); ?>
        </div>
        <?php if ($comment->isPublic()): ?>
            <div class="form-row">
                <input type="checkbox" name="comment_visibility" id="comment_<?= $comment->getId(); ?>_reply_visibility" class="fancy-checkbox" value="1" checked>
                <label for="comment_<?= $comment->getId(); ?>_reply_visibility">
                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                    <span><?= __('Visible for all users'); ?></span>
                </label>
            </div>
        <?php else: ?>
            <input type="hidden" name="comment_visibility" value="0">
        <?php endif; ?>
        <div class="form-row error-container" id="comment-error-container">
            <div class="error"></div>
        </div>
        <div id="comment_reply_controls_<?= $comment->getID(); ?>" class="form-row submit-container">
            <?= javascript_link_tag(__('Cancel'), ['onclick' => "$('#comment_reply_{$comment->getID()}').removeClass('active');$('#comment_view_{$comment->getID()}').show();", 'class' => 'closer button secondary']); ?>
            <button type="submit" class="button primary">
                <?= fa_image_tag('spinner', ['class' => 'indicator fa-spin']);?>
                <span><?= __('Post reply'); ?></span>
            </button>
        </div>
    </form>
</div>
