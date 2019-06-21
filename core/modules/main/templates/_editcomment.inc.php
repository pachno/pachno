<?php

    use pachno\core\framework\Context;
    use pachno\core\framework\Settings;
    /** @var \pachno\core\entities\Comment $comment */

?>
<div id="comment_edit_<?= $comment->getID(); ?>" class="comment-edit comment-editor editor_container form-container">
    <form id="comment_edit_form_<?= $comment->getID(); ?>" class="syntax_<?= Settings::getSyntaxClass($comment->getSyntax()); ?>" action="<?= make_url('comment_update', ['comment_applies_id' => $comment->getTargetID(), 'comment_applies_type' => $comment->getTargetType(), 'comment_module' => $comment->getModuleName(), 'comment_id' => $comment->getID()]); ?>" method="post" onSubmit="Pachno.Main.Comment.update('<?= $comment->getID(); ?>'); return false;">
        <input type="hidden" name="comment_id" value="<?= $comment->getID(); ?>" />
        <div class="form-row">
            <?php include_component('main/textarea', ['area_name' => 'comment_body', 'target_type' => isset($mentionable_target_type) ? $mentionable_target_type : $comment->getTargetType(), 'target_id' => $comment->getTargetId(), 'area_id' => 'comment_edit_'.$comment->getID().'_bodybox', 'height' => '200px', 'width' => '100%', 'syntax' => Settings::getSyntaxClass($comment->getSyntax()), 'value' => Context::getI18n()->decodeUTF8($comment->getContent(), true)]); ?>
        </div>
        <div class="form-row">
            <input type="checkbox" name="comment_visibility" id="comment_<?= $comment->getId(); ?>_visibility" class="fancy-checkbox" value="1" <?php if ($comment->isPublic()) echo ' checked'; ?>>
            <label for="comment_<?= $comment->getId(); ?>_visibility">
                <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                <span><?= __('Visible for all users'); ?></span>
            </label>
        </div>
        <div class="form-row error-container" id="comment-error-container">
            <div class="error"></div>
        </div>
        <div id="comment_edit_controls_<?= $comment->getID(); ?>" class="form-row submit-container">
            <?= javascript_link_tag('<span>'.__('Cancel').'</span>', ['class' => 'button secondary', 'onclick' => "$('comment_edit_{$comment->getID()}').removeClassName('active');$('comment_view_{$comment->getID()}').show();$('comment_add_button').show();"]); ?>
            <button type="submit" class="button primary">
                <?= fa_image_tag('spinner', ['class' => 'indicator fa-spin']);?>
                <span><?= __('Update comment'); ?></span>
            </button>
        </div>
    </form>
</div>
