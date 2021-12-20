<?php

    /**
     * @var \pachno\core\entities\User $pachno_user
     * @var int $comment_count
     * @var string $comment_count_div
     * @var string $comment_error_body
     * @var int $target_id
     * @var int $target_type
     * @var int $mentionable_target_type
     * @var bool $can_post_comments
     */

    use pachno\core\framework\Context,
        pachno\core\entities\Comment;

    $comment_options = ['comment_count_div' => $comment_count_div, 'mentionable_target_type' => $mentionable_target_type, 'target_type' => $target_type, 'target_id' => $target_id];
    if (isset($issue)) {
        $comment_options['issue'] = $issue;
    }

?>
<?php $module = (isset($module)) ? $module : 'core'; ?>
<?php if ($can_post_comments): ?>
    <div id="comment_add" class="comment_add comment-editor" style="<?php if (!(isset($comment_error) && $comment_error)): ?>display: none; <?php endif; ?>margin-top: 5px;">
        <div class="backdrop_detail_header">
            <span><?= __('Create a comment'); ?></span>
            <?= javascript_link_tag(fa_image_tag('times'), ['class' => 'closer']); ?>
        </div>
        <div class="add-comment-container form-container">
            <form id="add-comment-form" accept-charset="<?= mb_strtoupper(Context::getI18n()->getCharset()); ?>" action="<?= make_url('comment_add', ['comment_applies_id' => $target_id, 'comment_applies_type' => $target_type, 'comment_module' => $module]); ?>" method="post" data-comment-count-element="<?= $comment_count_div; ?>" data-simple-submit>
                <div class="form-row">
                    <?php include_component('main/textarea', array('area_name' => 'comment_body', 'target_type' => $mentionable_target_type, 'target_id' => $target_id, 'area_id' => 'comment_bodybox', 'height' => '250px', 'width' => '100%', 'syntax' => $pachno_user->getPreferredCommentsSyntax(true), 'value' => ((isset($comment_error) && $comment_error) ? $comment_error_body : ''))); ?>
                </div>
                <div class="form-row">
                    <input type="checkbox" name="comment_visibility" id="comment_0_visibility" class="fancy-checkbox" value="1" checked>
                    <label for="comment_0_visibility">
                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                        <span><?= __('Visible for all users'); ?></span>
                    </label>
                </div>
                <div class="form-row error-container" id="comment-error-container">
                    <div class="error"></div>
                </div>
                <div class="form-row submit-container">
                    <button type="submit" class="button primary">
                        <?= fa_image_tag('spinner', ['class' => 'indicator fa-spin']);?>
                        <span><?= __('Post comment'); ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<div class="onboarding" id="comments-list-none" <?php if ($comment_count != 0): ?>style="display: none;"<?php endif; ?>>
    <div class="image-container">
        <?= image_tag('/unthemed/mono/no-comments.png', [], true); ?>
    </div>
    <div class="helper-text">
        <?= __('Expand, collaborate and share'); ?><br>
        <?= __('Post a comment and get things done'); ?>
    </div>
</div>
<div id="comments_box">
    <?php include_component('main/commentlist', $comment_options); ?>
</div>
