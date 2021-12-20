<?php

    use pachno\core\entities\Comment;
    use pachno\core\entities\Issue;
    use pachno\core\entities\User;

    /**
     * @var Issue $issue
     * @var User $pachno_user
     */

?>
<div class="comments" id="viewissue_comments_container">
    <div class="comments-header-strip">
        <div class="dropper-container">
            <button class="dropper secondary icon">
                <?php echo fa_image_tag('spinner', ['class' => 'fa-spin', 'style' => 'display: none;', 'id' => 'comments_loading_indicator']); ?>
                <?= fa_image_tag('cog'); ?>
            </button>
            <div class="dropdown-container">
                <div class="list-mode">
                    <a href="javascript:void(0);" class="list-item" id="comments_show_system_comments_toggle" onclick="$$('#comments_box .system_comment').each(function (elm) { $(elm).toggle(); });">
                        <span class="icon"><?= fa_image_tag('comment-slash'); ?></span>
                        <span class="name"><?php echo __('Toggle system-generated comments'); ?></span>
                    </a>
                    <a href="javascript:void(0);" class="list-item trigger-comment-sort" data-target-type="<?= Comment::TYPE_ISSUE; ?>" data-target-id="<?= $issue->getID(); ?>">
                        <span class="icon"><?= fa_image_tag('arrows-alt-v'); ?></span>
                        <span class="name"><?php echo __('Sort comments in opposite direction'); ?></span>
                    </a>
                </div>
            </div>
        </div>
        <?php if ($pachno_user->canPostComments(Comment::TYPE_ISSUE, $issue->getProject())): ?>
            <button class="button secondary highlight trigger-show-comment-post" id="comment_add_button">
                <?= fa_image_tag('comment', ['class' => 'icon']); ?>
                <span class="name"><?php echo __('Post comment'); ?></span>
            </button>
        <?php endif; ?>
    </div>
    <div id="viewissue_comments">
        <?php include_component('main/comments', [
            'target_id' => $issue->getID(),
            'mentionable_target_type' => 'issue',
            'target_type' => Comment::TYPE_ISSUE,
            'show_button' => false,
            'comment_count_div' => 'viewissue_comment_count',
            'can_post_comments' => $pachno_user->canPostComments(Comment::TYPE_ISSUE, $issue->getProject()),
            'issue' => $issue
        ]); ?>
    </div>
</div>
