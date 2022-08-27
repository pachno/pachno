<?php
    
    use pachno\core\entities\IssueCommit;
    use pachno\core\entities\Project;
    
    /**
     * @var Project $project
     * @var IssueCommit[] $commits
     */
    
?>
<div class="configurable-components-list affected-list" id="affected_list">
    <?php foreach ($commits as $issue_commit): ?>
        <?php include_component('livelink/issuecommit', array('project' => $project, 'commit' => $issue_commit->getCommit())); ?>
    <?php endforeach; ?>
</div>