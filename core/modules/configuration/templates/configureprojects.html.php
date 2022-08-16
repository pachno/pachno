<?php
    
    use pachno\core\entities\Group;
    use pachno\core\entities\Project;
    use pachno\core\entities\User;
    use pachno\core\framework;

    /**
     * @var framework\Response $pachno_response
     * @var User $pachno_user
     * @var Group $user_group
     * @var int $access_level
     * @var Project[] $active_projects
     * @var Project[] $archived_projects
     */

    $pachno_response->setTitle(__('Manage projects'));

?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => framework\Settings::CONFIGURATION_SECTION_PROJECTS]); ?>
    <div class="configuration-container">
        <div class="configuration-content">
            <h1><?php echo __('Configure projects'); ?></h1>
            <div class="helper-text">
                <div class="image-container"><?= image_tag('/unthemed/onboarding_configuration_projects_icon.png', [], true); ?></div>
                <span class="description"><?php echo __('More information about projects - including how to import projects from external sources such as %github or %gitlab, collaborate with your project team(s) or configuring your project is found in the %project_documentation.', array('%project_documentation' => link_tag(\pachno\core\modules\publish\Publish::getArticleLink('Projects'), '<b>' . __('project documentation') . '</b>'), '%github' => fa_image_tag('github', ['class' => 'icon'], 'fab') . '&nbsp;<span>Github</span>', '%gitlab' => fa_image_tag('gitlab', ['class' => 'icon'], 'fab') . '&nbsp;<span>Gitlab</span>')); ?></span>
            </div>
            <?php if (framework\Context::getScope()->getMaxProjects()): ?>
                <div class="message-box type-info">
                    <?= fa_image_tag('info-circle'); ?>
                    <span><?php echo __('This instance is using %num of max %max projects', array('%num' => '<b id="current_project_num_count">' . Project::getProjectsCount() . '</b>', '%max' => '<b>' . framework\Context::getScope()->getMaxProjects() . '</b>')); ?></span>
                </div>
            <?php endif; ?>
            <h2>
                <span><?= __('Allow users to create projects'); ?></span>
                <input type="checkbox" class="fancy-checkbox" data-interactive-toggle value="1" id="toggle_allow_user_projects" data-url="<?= make_url('configure_projects'); ?>" <?php if ($user_group->hasPermission(\pachno\core\entities\Permission::PERMISSION_CREATE_PROJECTS)) echo ' checked'; ?>>
                <label class="button secondary" for="toggle_allow_user_projects"><?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']) . fa_image_tag('toggle-on', ['class' => 'icon checked']) . fa_image_tag('toggle-off', ['class' => 'icon unchecked']); ?><span class="checked"><?= __('Allowed'); ?></span><span class="unchecked"><?= __('Not allowed'); ?></span></label>
            </h2>
            <div class="helper-text">
                <span class="description"><?= __('Toggle on the setting above to allow users to create projects. This lets users create projects freely from the project list, and invite other users to collaborate'); ?></span>
            </div>
            <h2>
                <span><?php echo __('Active projects'); ?></span>
                <?php if (framework\Context::getScope()->hasProjectsAvailable()): ?>
                    <button class="button"
                            onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'project_config']); ?>');"><?= __('Create project'); ?></button>
                <?php endif; ?>
            </h2>
            <div id="project_table" class="flexible-table">
                <div class="row header">
                    <div class="column header info-icons"></div>
                    <div class="column header name-container"><?= __('Project name'); ?></div>
                    <div class="column header"><?= __('Project key'); ?></div>
                    <div class="column header"><?= __('Issues prefix'); ?></div>
                    <div class="column header"><?= __('Owner'); ?></div>
                    <div class="column header actions"></div>
                </div>
                <div class="body" id="project-active-list">
                    <?php foreach ($active_projects as $project): ?>
                        <?php include_component('projectbox', array('project' => $project, 'access_level' => $access_level)); ?>
                    <?php endforeach; ?>
                </div>
                <div class="body" id="noprojects_tr" style="<?php if (count($active_projects) > 0): ?> display: none;<?php endif; ?>">
                    <div class="row disabled">
                        <div class="column info-icons">
                            <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                        </div>
                        <div class="column name-container">
                            <?php echo __('There are no projects available'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <h2>
                <span>
                    <?php echo __('Archived projects'); ?>
                </span>
            </h2>
            <div id="project_table_archived" class="flexible-table">
                <div class="row header">
                    <div class="column header info-icons"></div>
                    <div class="column header name-container"><?= __('Project name'); ?></div>
                    <div class="column header"><?= __('Project key'); ?></div>
                    <div class="column header"><?= __('Issues prefix'); ?></div>
                    <div class="column header"><?= __('Owner'); ?></div>
                    <div class="column header actions"></div>
                </div>
                <div class="body" id="project-archived-list">
                    <?php foreach ($archived_projects as $project): ?>
                        <?php include_component('projectbox', array('project' => $project, 'access_level' => $access_level)); ?>
                    <?php endforeach; ?>
                </div>
                <div class="body" id="noprojects_tr_archived" style="<?php if (count($archived_projects) > 0): ?> display: none;<?php endif; ?>">
                    <div class="row disabled">
                        <div class="column info-icons">
                            <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                        </div>
                        <div class="column name-container">
                            <?php echo __('There are no archived projects'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
  Pachno.on(Pachno.EVENTS.ready, () => {
    Pachno.on(Pachno.EVENTS.configuration.archiveProject, (PachnoApplication, data) => {
      const url = data.url;
      Pachno.UI.Dialog.setSubmitting();

      Pachno.fetch(url, {
        method: 'POST'
      })
        .then((json) => {
            Pachno.UI.Dialog.dismiss();
            $('#project_box_' + data.project_id).remove();
            if ($('#project-active-list').children().length == 0) {
                $('#noprojects_tr').show();
            }
            $('#project-archived-list').prepend(json.box);
          $('#noprojects_tr_archived').hide();
        })
    });

    Pachno.on(Pachno.EVENTS.configuration.unarchiveProject, (PachnoApplication, data) => {
      const url = data.url;
      Pachno.UI.Dialog.setSubmitting();

      Pachno.fetch(url, {
        method: 'POST'
      })
        .then((json) => {
            Pachno.UI.Dialog.dismiss();
            $('#project_box_' + data.project_id).remove();
            $('#noprojects_tr').hide();
            if ($('#project-archived-list').children().length == 0) {
              $('#noprojects_tr_archived').show();
            }
            $('#project-active-list').append(json.box);
        })
    });

  })
</script>
