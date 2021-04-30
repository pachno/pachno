<?php

    use pachno\core\entities\Build;
    use pachno\core\entities\Project;
    use pachno\core\framework\Response;

    /**
     * @var Response $pachno_response
     * @var Project $selected_project
     * @var Build[][] $active_builds
     * @var Build[][] $upcoming_builds
     */

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
        <div class="fancy-tabs tab-switcher" id="project-releases-menu">
            <a id="tab_project_releases_active" class="tab selected tab-switcher-trigger" data-tab-target="active"><?= fa_image_tag('box', ['class' => 'icon']); ?><span><?= __('Available releases'); ?></span></a>
            <a id="tab_project_releases_inactive" class="tab tab-switcher-trigger" data-tab-target="inactive"><?= fa_image_tag('calendar', ['class' => 'icon'], 'far'); ?><span><?= __('Upcoming releases'); ?></span></a>
        </div>
        <div id="project-releases-menu_panes" class="fancy-panes">
            <div id="tab_project_releases_active_pane" data-tab-id="active" class="pane">
                <div class="flexible-table">
                    <div class="row header">
                        <div class="column header info-icons">&nbsp;</div>
                        <div class="column header name-container"><?= __('Release name'); ?></div>
                        <div class="column header"><?= __('Version'); ?></div>
                        <div class="column header"><?= __('Release date'); ?></div>
                        <div class="column header actions"></div>
                    </div>
                    <div class="body" id="active_releases_list">
                    <?php foreach ($active_builds[0] as $build): ?>
                        <?php include_component('project/release', array('build' => $build)); ?>
                    <?php endforeach; ?>
                    </div>
                    <?php if ($selected_project->isEditionsEnabled()): ?>
                        <?php foreach ($selected_project->getEditions() as $edition_id => $edition): ?>
                            <div class="body" id="active_releases_list_<?= $edition_id; ?>">
                                <?php foreach ($active_builds[$edition_id] as $build): ?>
                                    <?php include_component('project/release', array('build' => $build)); ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div id="tab_project_releases_inactive_pane" style="display: none;" data-tab-id="inactive">
                <ul id="inactive_releases_list">
                    <?php foreach ($upcoming_builds[0] as $build): ?>
                        <?php include_component('project/release', array('build' => $build)); ?>
                    <?php endforeach; ?>
                </ul>
                <?php if ($selected_project->isEditionsEnabled()): ?>
                    <?php foreach ($selected_project->getEditions() as $edition_id => $edition): ?>
                        <ul id="archived_releases_list_<?= $edition_id; ?>">
                            <?php foreach ($upcoming_builds[$edition_id] as $build): ?>
                                <?php include_component('project/release', array('build' => $build)); ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
