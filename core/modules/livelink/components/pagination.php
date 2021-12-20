<?php

    /**
     * @var int $offset
     * @var int $total_commits_count
     * @var \pachno\core\entities\Branch $branch
     *
     * @var \pachno\core\entities\Commit $last_commit
     */

?>
<div class="commits-paginator paginator">
    <div class="page-buttons">
        <?php if ($offset < $total_commits_count): ?>
            <button class="button secondary highlight trigger-show-branch" data-branch="<?= $branch->getName(); ?>" data-from-commit="<?= $last_commit->getRevision(); ?>" data-offset="<?php echo $offset; ?>"><?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?><span><?= __('Older commits'); ?></span></button>
        <?php else: ?>
            <button class="button secondary" disabled><?= __('Older commits'); ?></button>
        <?php endif; ?>
    </div>
</div>
