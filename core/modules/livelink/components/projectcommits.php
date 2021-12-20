<?php

    /**
     * @var \pachno\core\entities\Commit[] $commits
     */

    $first = true;

?>
<?php foreach ($commits as $commit): ?>
    <?php if ($first || !$commit->getPreviousCommit() instanceof \pachno\core\entities\Commit || date('ymd', $commit->getDate()) != date('ymd', $commit->getPreviousCommit()->getDate())): ?>
        <?php include_component('livelink/commitrowheader', ['commit' => $commit]); ?>
    <?php endif; ?>
    <?php include_component('livelink/commitrow', ['project' => $selected_project, 'commit' => $commit, 'branch' => $branch, 'branches' => $branches]); ?>
    <?php $first = false; ?>
<?php endforeach; ?>
<?php if (isset($commit)): ?>
    <?php include_component('livelink/pagination', ['last_commit' => $commit, 'branch' => $branch, 'offset' => $offset, 'total_commits_count' => $total_commits_count]); ?>
<?php endif; ?>
