<?php

    use pachno\core\framework;
    $pachno_response->setTitle(__('Manage projects'));
    
?>
<div class="content-with-sidebar">
    <?php include_component('leftmenu', array('selected_section' => framework\Settings::CONFIGURATION_SECTION_PROJECTS)); ?>
    <div class="configuration-container">
        <h1><?php echo __('Configure projects'); ?></h1>
        <div class="helper-text">
            <?php echo __('More information about projects, editions, builds and components is available from the %wiki_help_section.', array('%wiki_help_section' => link_tag(make_url('publish_article', array('article_name' => 'Category:Help')), '<b>'.__('Wiki help section').'</b>'))); ?>
        </div>
        <?php if (framework\Context::getScope()->getMaxProjects()): ?>
            <div class="message-box type-info">
                <?= fa_image_tag('info-circle'); ?>
                <span><?php echo __('This instance is using %num of max %max projects', array('%num' => '<b id="current_project_num_count">'.\pachno\core\entities\Project::getProjectsCount().'</b>', '%max' => '<b>'.framework\Context::getScope()->getMaxProjects().'</b>')); ?></span>
            </div>
        <?php endif; ?>
        <h2>
            <span><?php echo __('Active projects'); ?></span>
            <?php if (framework\Context::getScope()->hasProjectsAvailable()): ?>
                <button class="button" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'project_config']); ?>');"><?= __('Create project'); ?></button>
            <?php endif; ?>
        </h2>
        <div id="project_table" class="flexible-table">
            <div class="row header">
                <div class="column header info-icons"></div>
                <div class="column header"><?= __('Project key'); ?></div>
                <div class="column header name-container"><?= __('Project name'); ?></div>
                <div class="column header"><?= __('Owner'); ?></div>
                <div class="column header actions"></div>
            </div>
            <div class="body">
                <?php foreach ($active_projects as $project): ?>
                    <?php include_component('projectbox', array('project' => $project, 'access_level' => $access_level)); ?>
                <?php endforeach; ?>
            </div>
        </div>
        <div id="noprojects_tr" style="padding: 3px; color: #AAA;<?php if (count($active_projects) > 0): ?> display: none;<?php endif;?>">
            <?php echo __('There are no projects available'); ?>
        </div>
        <h4 style="margin-top: 30px;"><?php echo __('Archived projects'); ?></h4>
        <div id="project_table_archived" class="flexible-table">
            <div class="row header">
                <div class="column header info-icons"></div>
                <div class="column header"><?= __('Project key'); ?></div>
                <div class="column header name-container"><?= __('Project name'); ?></div>
                <div class="column header"><?= __('Owner'); ?></div>
                <div class="column header actions"></div>
            </div>
            <div class="body">
                <?php foreach ($archived_projects as $project): ?>
                    <?php include_component('projectbox', array('project' => $project, 'access_level' => $access_level)); ?>
                <?php endforeach; ?>
            </div>
        </div>
        <div id="noprojects_tr_archived" style="padding: 3px; color: #AAA;<?php if (count($archived_projects) > 0): ?> display: none;<?php endif;?>">
            <?php echo __('There are no projects available'); ?>
        </div>
    </div>
</div>
