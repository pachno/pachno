<?php

    $pachno_response->setTitle(__('"%project_name" releases', array('%project_name' => $selected_project->getName())));

?>
<div class="content-with-sidebar">
    <?php include_component('project/sidebar', ['dashboard' => __('Releases')]); ?>
    <?php /* <div class="project_left">
            <h3><?= __('Release selection'); ?></h3>
            <ul class="simple-list">
                <li class="selected"><a href="javascript:void(0);" onclick="$$('.releases_list').each(function (r) { (r.hasClass('active_releases')) ? r.show() : r.hide() }); Pachno.Project.toggleLeftSelection(this);"><?= __('All active releases'); ?></a></li>
                <li ><a href="javascript:void(0);" onclick="$$('.releases_list').each(function (r) { (r.hasClass('archived_releases')) ? r.show() : r.hide() }); Pachno.Project.toggleLeftSelection(this);"><?= __('Archived releases'); ?></a></li>
            </ul>
        </div> */ ?>
    <div id="project_releases_container">
        <?php if ($pachno_user->canEditProjectDetails($selected_project)): ?>
            <div class="project_save_container">
                <?= link_tag(make_url('project_release_center', array('project_key' => $selected_project->getKey())), __('Manage project releases'), ['class' => 'button']); ?>
            </div>
        <?php endif; ?>
        <div class="active_releases releases_list">
            <h3><?= __('Active project releases'); ?></h3>
            <?php if (count($active_builds[0])): ?>
                <ul class="simple-list">
                <?php foreach ($active_builds[0] as $build): ?>
                    <?php include_component('project/release', array('build' => $build)); ?>
                <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="faded_out"><?= __('There are no active releases for this project'); ?></div>
            <?php endif; ?>
            <?php if ($selected_project->isEditionsEnabled()): ?>
                <?php foreach ($selected_project->getEditions() as $edition_id => $edition): ?>
                    <h4><?= __('Active %edition_name releases', array('%edition_name' => $edition->getName())); ?></h4>
                    <?php if (count($active_builds[$edition_id])): ?>
                        <ul class="simple-list">
                        <?php foreach ($active_builds[$edition_id] as $build): ?>
                            <?php include_component('project/release', array('build' => $build)); ?>
                        <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="faded_out"><?= __('There are no active releases for this edition'); ?></div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="archived_releases releases_list" style="display: none;">
            <h3><?= __('Archived project releases'); ?></h3>
            <?php if (count($archived_builds[0])): ?>
                <ul class="simple-list">
                <?php foreach ($archived_builds[0] as $build): ?>
                    <?php include_component('project/release', array('build' => $build)); ?>
                <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="faded_out"><?= __('There are no archived releases for this project'); ?></div>
            <?php endif; ?>
            <?php if ($selected_project->isEditionsEnabled()): ?>
                <?php foreach ($selected_project->getEditions() as $edition_id => $edition): ?>
                    <h4><?= __('Archived %edition_name releases', array('%edition_name' => $edition->getName())); ?></h4>
                    <?php if (count($archived_builds[$edition_id])): ?>
                        <ul class="simple-list">
                        <?php foreach ($archived_builds[$edition_id] as $build): ?>
                            <?php include_component('project/release', array('build' => $build)); ?>
                        <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="faded_out"><?= __('There are no archived releases for this edition'); ?></div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
