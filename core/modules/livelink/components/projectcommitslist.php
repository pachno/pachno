<?php

/**
 * @var \pachno\core\entities\Project $selected_project
 * @var \pachno\core\entities\Commit[] $commits
 * @var \pachno\core\entities\Branch $branch
 */

?>
<div class="project_commits_box">
    <div id="commits" class="commits-list">
        <?php include_component('livelink/projectcommits', ['selected_project' => $selected_project, 'commits' => $commits, 'offset' => $offset, 'total_commits_count' => $total_commits_count, 'branch' => $branch, 'branches' => $branches]); ?>
    </div>
</div>
