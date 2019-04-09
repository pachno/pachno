<?php

    use pachno\core\framework;
    $selected_project = \pachno\core\framework\Context::getCurrentProject();

?>
<div class="project-sidebar-info">
    <?php \pachno\core\framework\Event::createNew('core', 'project/templates/projectheader', $selected_project)->trigger(); ?>
        <div class="project-name">
            <div class="image-container">
                <?php echo image_tag($selected_project->getLargeIconName(), ['alt' => $selected_project->getName()], true); ?>
            </div>
            <span class="project-name-span">
                <span><?php echo $selected_project->getName(); ?></span>
                <span class="project-sub-page"><?php echo ($subpage != '') ? $subpage : __('Project dashboard'); ?></span>
            </span>
        </div>
        <?php \pachno\core\framework\Event::createNew('core', 'project/templates/projectheader/namelabel', $selected_project)->trigger(); ?>
        <?php /* if ($pachno_response->getPage() == 'project_dashboard' && $pachno_user->canEditProjectDetails($selected_project)): ?>
            <div class="project_header_right button-group">
                <a href="javascript:void(0);" id="edit-project-dashboard-button" class="button" onclick="$$('.dashboard').each(function (elm) { elm.toggleClassName('editable');});$(this).toggleClassName('button-pressed');"><?= fa_image_tag('cog'); ?><span><?= __('Edit dashboard'); ?></span></a>
            </div>
        <?php endif; */ ?>
    <div class="button-group">
        <?php if ($pachno_user->canEditProjectDetails(framework\Context::getCurrentProject())): ?>
            <a href="<?= make_url('project_settings', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())); ?>" class="button secondary <?php if ($pachno_response->getPage() == 'project_settings') echo 'active'; ?>">
                <?= fa_image_tag('cog', ['class' => 'icon']); ?>
                <span class="name"><?= __('Settings'); ?></span>
            </a>
        <?php endif; ?>
        <?php if (framework\Context::isProjectContext() && !framework\Context::getCurrentProject()->isArchived() && !framework\Context::getCurrentProject()->isLocked() && $pachno_user->canReportIssues(framework\Context::getCurrentProject())): ?>
            <?= javascript_link_tag(fa_image_tag('plus') . '<span>'.__('Report an issue').'</span>', array('onclick' => "Pachno.Issues.Add('" . make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => framework\Context::getCurrentProject()->getId())) . "');", 'class' => 'button button-lightblue', 'id' => 'reportissue_button')); ?>
            <script type="text/javascript">
                var Pachno;

                require(['domReady', 'pachno/index', 'jquery'], function (domReady, pachno_index_js, jQuery) {
                    domReady(function () {
                        Pachno = pachno_index_js;
                        var hash = window.location.hash;

                        if (hash != undefined && hash.indexOf('report_an_issue') == 1) {
                            jQuery('#reportissue_button').trigger('click');
                        }
                    });
                });
            </script>
        <?php endif; ?>
    </div>
    <?php if ($pachno_response->getPage() == 'project_summary'): ?>
        <div class="project_header_right button-group">
            <?php \pachno\core\framework\Event::createNew('core', 'project_header_buttons')->trigger(); ?>
            <?php if ($selected_project->hasDownloads() && $pachno_response->getPage() != 'project_releases'): ?>
                <?php echo link_tag(make_url('project_releases', ['project_key' => $selected_project->getKey()]), image_tag('icon_download.png').__('Download'), ['class' => 'button button-orange']); ?>
            <?php endif; ?>
            <?php if ($selected_project->hasParent()): ?>
                <?php echo link_tag(make_url('project_dashboard', ['project_key' => $selected_project->getParent()->getKey()]), image_tag($selected_project->getParent()->getSmallIconName(), ['style' => 'width: 16px; height: 16px;'], $selected_project->getParent()->hasSmallIcon()) . __('Up to %parent', array('%parent' => $selected_project->getParent()->getName())), array('class' => 'button')); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
