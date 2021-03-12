<?php

    /** @var \pachno\core\entities\Branch $branch */
    /** @var \pachno\core\entities\Branch[] $branches */
    /** @var \pachno\core\entities\Commit $commit */
    /** @var \pachno\core\entities\Project $project */

    $url = (isset($branch)) ? make_url('livelink_project_commit', ['commit_hash' => $commit->getRevision(), 'project_key' => $project->getKey(), 'branch' => $branch->getName()]) : make_url('livelink_project_commit', ['commit_hash' => $commit->getRevision(), 'project_key' => $project->getKey()]);

?>
<div class="list-item trigger-show-commit commit multiline <?php if (isset($branch)) echo ' branch_' . $branch->getName(); ?>" id="commit_<?= $commit->getID(); ?>" data-url="<?= $url; ?>" data-commit-id="<?= $commit->getId(); ?>">
    <span class="icon">
        <span class="avatar-container">
            <?php echo image_tag($commit->getAuthor()->getAvatarURL(false), array('alt' => ' '), true); ?>
        </span>
    </span>
    <span class="name">
        <span class="title"><?= trim($commit->getTitle(true)); ?></span>
        <span class="description"><?= $commit->getAuthor()->getName(); ?>, <?= \pachno\core\framework\Context::getI18n()->formatTime($commit->getDate(), 12); ?></span>
    </span>
    <span class="information">
        <?php /*if (isset($branches[$commit->getID()])): ?>
            <span class="commit-branches">
                <?php foreach ($branches[$commit->getID()] as $commit_branch): ?>
                    <span class="branch"><?= fa_image_tag('code-branch'); ?><span><?= $commit_branch->getName(); ?></span></span>
                <?php endforeach; ?>
            </span>
        <?php endif;*/ ?>
        <span class="row">
            <span class="item commit-sha"><?= $commit->getShortRevision(); ?></span>
        </span>
        <span class="row">
            <?php if ($commit->isImported()): ?>
                <?php include_component('livelink/diff_summary', ['diffable' => $commit]); ?>
            <?php else: ?>
                <span class="item tooltip-container not-imported">
                    <?= fa_image_tag('exclamation-triangle', ['class' => 'icon']); ?>
                    <span class="tooltip from-right">
                        <span class="message"><?= __('This imported commit is missing some details. The details will be fetched when you open the commit details.'); ?></span>
                    </span>
                </span>
            <?php endif; ?>
            <span class="item commit-comments"><?= fa_image_tag('comments', ['class' => 'icon']); ?><span><?= $commit->getCommentCount(); ?></span></span>
        </span>
    </span>
</div>
