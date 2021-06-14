<?php

    /**
     * @var \pachno\core\entities\User $pachno_user
     * @var \pachno\core\framework\Response $pachno_response
     */

    if ($team instanceof \pachno\core\entities\Team) {
        $pachno_response->setTitle(__('Team dashboard for %team_name', array('%team_name' => $team->getName())));
    } else {
        $pachno_response->setTitle(__('Team dashboard'));
    }

?>
<div class="content-with-sidebar">
    <div class="main_area">
        <div class="dashboard layout_standard">
            <ul class="dashboard_column column_1">
                <li class="dashboard_view_container">
                    <div class="container_div transparent">
                        <div class="header">
                            <?= __('%teamname projects', ['%teamname' => $team->getName()]); ?>
                        </div>
                        <div class="project-list">
                            <?php foreach ($team->getAssociatedProjects() as $project): ?>
                                <?php include_component('project/project', ['project' => $project, 'include_subprojects' => false]); ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </li>
            </ul>
            <ul class="dashboard_column column_2 narrow" id="dashboard_righthand">
                <div class="flexible-table">
                    <div class="row header">
                        <div class="column header name-container">
                            <?php echo __('Members of %team', array('%team' => __($team->getName()))); ?>&nbsp;(<span id="team_<?= $team->getID(); ?>_membercount"><?= $team->getNumberOfMembers(); ?></span>)
                        </div>
                    </div>
                </div>
                <?php if ($team->getTeamLead() instanceof \pachno\core\entities\User): ?>
                    <div class="row">
                        <div class="column name-container">
                            <?php include_component('main/userdropdown', ['user' => $team->getTeamLead()]); ?>
                            <span class="count-badge"><?= __('Team lead'); ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                <?php foreach ($team->getMembers() as $user): ?>
                    <div class="row">
                        <div class="column name-container">
                            <?php include_component('main/userdropdown', compact('user')); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
