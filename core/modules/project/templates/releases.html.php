<?php

    use pachno\core\entities\Build;
    use pachno\core\entities\Project;
    use pachno\core\framework\Response;

    /**
     * @var Response $pachno_response
     * @var Project $selected_project
     * @var Build[][] $active_builds
     * @var int $active_builds_count
     * @var Build[][] $archived_builds
     * @var int $archived_builds_count
     * @var Build[][] $upcoming_builds
     * @var int $upcoming_builds_count
     */

    $pachno_response->setTitle(__('"%project_name" releases', array('%project_name' => $selected_project->getName())));

?>
<div class="content-with-sidebar">
    <?php include_component('project/sidebar', ['dashboard' => __('Releases')]); ?>
    <div id="project_releases_container">
        <div class="fancy-tabs tab-switcher" id="project-releases-menu">
            <a id="tab_project_releases_active" class="tab tab-switcher-trigger" data-tab-target="archived"><?= fa_image_tag('archive', ['class' => 'icon']); ?><span><?= __('Archived releases'); ?></span><span class="count-badge release-count" data-list="archived"><?= $archived_builds_count; ?></span></a>
            <a id="tab_project_releases_active" class="tab selected tab-switcher-trigger" data-tab-target="active"><?= fa_image_tag('box', ['class' => 'icon']); ?><span><?= __('Active releases'); ?></span><span class="count-badge release-count" data-list="active"><?= $active_builds_count; ?></span></a>
            <a id="tab_project_releases_inactive" class="tab tab-switcher-trigger" data-tab-target="upcoming"><?= fa_image_tag('calendar', ['class' => 'icon'], 'far'); ?><span><?= __('Upcoming releases'); ?></span><span class="count-badge release-count" data-list="upcoming"><?= $upcoming_builds_count; ?></span></a>
        </div>
        <div id="project-releases-menu_panes" class="fancy-panes">
            <div id="tab_project_releases_active_pane" data-tab-id="active" class="pane">
                <?php include_component('project/releases', ['project' => $selected_project, 'builds' => $active_builds, 'mode' => 'active']); ?>
            </div>
            <div id="tab_project_releases_archived_pane" data-tab-id="archived" class="pane" style="display: none;">
                <?php include_component('project/releases', ['project' => $selected_project, 'builds' => $archived_builds, 'mode' => 'archived']); ?>
            </div>
            <div id="tab_project_releases_upcoming_pane" style="display: none;" data-tab-id="upcoming" class="pane">
                <?php include_component('project/releases', ['project' => $selected_project, 'builds' => $upcoming_builds, 'mode' => 'upcoming']); ?>
            </div>
        </div>
    </div>
</div>
<script>
    Pachno.on(Pachno.EVENTS.build.delete, function (PachnoApplication, data) {
        $(`[data-release][data-release-id="${data.build_id}"]`).remove();
        Pachno.UI.Dialog.setSubmitting();

        Pachno.fetch(data.url, { method: 'DELETE' })
            .then(json => {
                Pachno.UI.Dialog.dismiss();
                for (const mode of ['archived', 'active', 'upcoming']) {
                    $('.release-count[data-list=' + mode + ']').html($('#tab_project_releases_' + mode + '_pane .release-row').length);
                }
            });
    });
</script>