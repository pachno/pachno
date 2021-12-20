<?php

    use pachno\core\entities\Commit;
    use pachno\core\entities\Comment;
    use pachno\core\framework\Context;

    /**
     * @var \pachno\core\entities\Branch $branch
     * @var \pachno\core\entities\Commit $commit
     * @var \pachno\core\entities\Project $project
     * @var \pachno\core\framework\Response $pachno_response
     * @var \pachno\core\entities\User $pachno_user
     */

    $pachno_response->setTitle(__('"%project_name" commit %commit_sha', ['%project_name' => $project->getName(), '%commit_sha' => $commit->getRevisionString()]));
    $branch_string = (isset($branch)) ? '&nbsp;&raquo;&nbsp;' . $branch->getName() : '';

?>
<div class="top-search-filters-container">
    <div class="search-and-filters-strip">
        <div class="header">
            <span class="name-container"><span class="item-name"><?= $commit->getTitle(true); ?></span></span>
        </div>
        <div class="search-strip">
            <button class="button secondary back-to-commits-list">
                <span class="icon"><?= fa_image_tag('angle-double-left'); ?></span>
                <span class="name"><?= __('Back'); ?></span>
            </button>
            <?php if ($commit->isImported()): ?>
                <div class="commit-files-summary">
                    <?= fa_image_tag('file-invoice'); ?>
                    <span class="summary">
                        <?php if ($commit->getLinesAdded() && $commit->getLinesRemoved()): ?>
                            <?= __('This commit has %num_additions_and_num_deletions across %num_files', ['%num_additions_and_num_deletions' => '<span class="num_changes">' . __('%num_a addition(s) and %num_d deletion(s)', ['%num_a' => $commit->getLinesAdded(), '%num_d' => $commit->getLinesRemoved()]) . '</span>', '%num_files' => '<span class="num_files">' . __('%num file(s)', ['%num' => count($commit->getFiles())]) . '</span>']); ?>
                        <?php elseif ($commit->getLinesAdded()): ?>
                            <?= __('This commit has %num_additions across %num_files', ['%num_additions' => '<span class="num_changes">' . __('%num addition(s)', ['%num' => $commit->getLinesAdded()]) . '</span>', '%num_files' => '<span class="num_files">' . __('%num file(s)', ['%num' => count($commit->getFiles())]) . '</span>']); ?>
                        <?php else: ?>
                            <?= __('This commit has %num_deletions across %num_files', ['%num_deletions' => '<span class="num_changes">' . __('%num deletion(s)', ['%num' => $commit->getLinesRemoved()]) . '</span>', '%num_files' => '<span class="num_files">' . __('%num file(s)', ['%num' => count($commit->getFiles())]) . '</span>']); ?>
                        <?php endif; ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="content-with-sidebar">
    <nav class="sidebar" id="commit-sidebar-menu">
        <div class="list-mode">
            <div class="commit-details-list">
                <ul class="fields-list">
                    <li class="header">
                        <span><?= __('Commit details'); ?></span>
                    </li>
                    <li>
                        <div class="label"><?= __('Commit id'); ?></div>
                        <div class="value"><span class="commit-sha"><?= $commit->getShortRevision(); ?></span></div>
                    </li>
                    <li>
                        <div class="label"><?= __('Committed by'); ?></div>
                        <div class="value"><?php include_component('main/userdropdown', ['user' => $commit->getAuthor()]); ?></div>
                    </li>
                    <li>
                        <div class="label"><?= __('Committed at'); ?></div>
                        <div class="value"><?= \pachno\core\framework\Context::getI18n()->formatTime($commit->getDate(), 25); ?></div>
                    </li>
                    <?php if ($commit->getPreviousCommit() instanceof Commit): ?>
                        <li>
                            <div class="label"><?= __('Previous commit'); ?></div>
                            <div class="value"><span data-url="<?= make_url('livelink_project_commit', ['commit_hash' => $commit->getPreviousCommit()->getRevision(), 'project_key' => $commit->getProject()->getKey(), 'branch' => $branch->getName()]); ?>" class="commit-sha trigger-show-commit"><?= $commit->getPreviousCommit()->getShortRevision(); ?></span></div>
                        </li>
                    <?php endif; ?>
                    <li class="header">
                        <?= fa_image_tag('caret-right', ['class' => 'icon expander']); ?>
                        <span><?= __('Branch(es)'); ?></span>
                        <span class="count-badge"><?= count($commit->getBranches()); ?></span>
                    </li>
                    <li>
                        <div class="value list">
                            <?php foreach ($commit->getBranches() as $branch): ?>
                                <div class="status-badge branch-badge"><?= fa_image_tag('code-branch', ['class' => 'icon']) . $branch->getName(); ?></div>
                            <?php endforeach; ?>
                        </div>
                    </li>
                    <li class="header">
                        <?php if ($commit->hasIssues()): ?>
                            <?= fa_image_tag('caret-right', ['class' => 'icon expander']); ?>
                        <?php endif; ?>
                        <span><?= __('Affected issues'); ?></span>
                        <span class="count-badge"><?= count($commit->getIssues()); ?></span>
                    </li>
                    <li class="list-mode">
                        <?php if ($commit->hasIssues()): ?>
                            <?php foreach ($commit->getIssues() as $issue): ?>
                                <?php include_component('main/relatedissue', ['issue' => $issue]); ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </li>
                </ul>
                <?php if ($commit->isImported()): ?>
                    <ul class="fields-list">
                        <li></li>
                        <li class="header">
                            <span><?= __('Files committed'); ?></span>
                            <span class="count-badge"><?= count($commit->getFiles()); ?></span>
                        </li>
                    </ul>
                    <div class="files-list">
                        <?php include_component('livelink/tree', ['structure' => $commit->getStructure()]); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div id="commit-information" class="commit-summary-container">
        <div class="project_right branch_<?php echo $branch->getName(); ?>" id="commit_<?php echo $commit->getID(); ?>">
            <?php if ($is_importing): ?>
                <div class="message-box type-warning">
                    <span class="message">
                        <?= fa_image_tag('spinner', ['class' => 'fa-spin']) . __('This repository is still being imported and may not be fully up-to-date yet.'); ?>
                    </span>
                </div>
            <?php elseif (!$commit->isImported()): ?>
                <div class="message-box type-warning">
                    <span class="message">
                        <?= fa_image_tag('exclamation-triangle') . __('This commit was imported and does not contain all information. Press the "%update_commit"-button to load details.', ['%update_commit' => __('Update commit')]); ?>
                    </span>
                    <span class="actions">
                        <?php if (isset($branch)): ?>
                            <a class="button" href="<?= make_url('livelink_project_commit_import', ['project_key' => $project->getKey(), 'commit_hash' => $commit->getRevision(), 'branch' => $branch->getName()]); ?>"><?= __('Update commit'); ?></a>
                        <?php else: ?>
                            <a class="button" href="<?= make_url('livelink_project_commit_import', ['project_key' => $project->getKey(), 'commit_hash' => $commit->getRevision()]); ?>"><?= __('Update commit'); ?></a>
                        <?php endif; ?>
                    </span>
                </div>
            <?php endif; ?>
            <?php if ($commit->getMessage()): ?>
                <div class="commit-message">
                    <div class="overflow"><?= \pachno\core\helpers\TextParser::parseText(trim($commit->getMessage(false)), false, null, [], \pachno\core\framework\Settings::SYNTAX_MD); ?></div>
                </div>
            <?php endif; ?>
            <div class="commit-files">
                <?php foreach ($commit->getFiles() as $file): ?>
                    <a class="file-anchor" name="file_<?= $file->getID(); ?>"></a>
                    <div class="file-preview action_<?= $file->getAction(); ?>">
                        <?php if ($file->getAction() == \pachno\core\entities\CommitFile::ACTION_DELETED): ?>
                            <div class="filename"><?= fa_image_tag('trash-alt') . $file->getPath(); ?></div>
                            <div class="diffs">
                                <div class="message-box type-warning too-long"><?= fa_image_tag('trash') . __('This file was deleted in this commit'); ?></div>
                            </div>
                        <?php elseif ($file->getAction() == \pachno\core\entities\CommitFile::ACTION_RENAMED): ?>
                            <div class="filename"><?= fa_image_tag('edit', [], 'far') . $file->getData()['previous_filename'] . fa_image_tag('arrow-right-alt') . $file->getPath(); ?></div>
                        <?php else: ?>
                            <div class="filename">
                                <?= fa_image_tag($file->getFontAwesomeIcon(), [], $file->getFontAwesomeIconStyle()) . $file->getPath(); ?>
                                <?php include_component('livelink/diff_summary', ['diffable' => $file]); ?>
                                <?php if ($file->getAction() == \pachno\core\entities\CommitFile::ACTION_ADDED): ?>
                                    <div class="added-badge"><?= __('Added in this commit'); ?></div>
                                <?php endif; ?>
                                <div class="actions-container">
                                    <input type="checkbox" class="fancy-checkbox trigger-mark-seen" data-file-id="<?= $file->getID(); ?>" id="commit-seen-file-<?= $file->getID(); ?>">
                                    <label for="commit-seen-file-<?= $file->getID(); ?>" class="trigger-mark-seen" data-file-id="<?= $file->getID(); ?>">
                                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                        <?= fa_image_tag('eye'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="diffs">
                                <?php foreach ($file->getDiffs() as $diff): ?>
                                    <?php include_component('livelink/diff', ['diff' => $diff]); ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div id="commit-comments">
                <h4>
                    <?= fa_image_tag('comment', ['class' => 'icon'], 'far'); ?>
                    <span class="name">
                        <span><?php echo __('Comments'); ?></span>
                        <span class="count-badge" id="commit_comment_count"><?= $comment_count; ?></span>
                    </span>
                    <div class="button-group">
                        <?php echo fa_image_tag('spinner', ['class' => 'fa-spin', 'style' => 'display: none;', 'id' => 'comments_loading_indicator']); ?>
                        <button class="secondary icon trigger-comment-sort" data-target-type="<?= Comment::TYPE_COMMIT; ?>" data-target-id="<?= $commit->getID(); ?>" id="sort-comments-button" style="<?php if (!$comment_count) echo 'display: none; '; ?>"><?= fa_image_tag('sort', ['class' => 'icon']); ?></button>
                        <?php if ($pachno_user->canPostComments(Comment::TYPE_COMMIT, Context::getCurrentProject())): ?>
                            <button id="comment_add_button" class="button secondary highlight trigger-show-comment-post">
                                <?= fa_image_tag('comment', ['class' => 'icon']); ?>
                                <span class="name"><?= __('Post a comment'); ?></span>
                            </button>
                        <?php endif; ?>
                    </div>
                </h4>
                <?php include_component('main/comments', ['target_id' => $commit->getID(), 'mentionable_target_type' => 'commit', 'target_type' => Comment::TYPE_COMMIT, 'show_button' => false, 'comment_count_div' => 'commit_comment_count', 'can_post_comments' => $pachno_user->canPostComments(Comment::TYPE_COMMIT, Context::getCurrentProject())]); ?>
            </div>
        </div>
    </div>
</div>
