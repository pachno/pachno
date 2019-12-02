<?php

    use pachno\core\entities\Branch;

    /** @var Branch[] $branches */
    /** @var \pachno\core\entities\Project $selected_project */
    /** @var \pachno\core\framework\Response $pachno_response */

    $pachno_response->setTitle(__('"%project_name" commits', array('%project_name' => $selected_project->getName())));

?>
<div class="content-with-sidebar">
    <nav class="project-context sidebar <?= (isset($collapsed) && $collapsed) ? 'collapsed' : ''; ?>" id="project-menu" data-project-id="<?= (\pachno\core\framework\Context::isProjectContext()) ? \pachno\core\framework\Context::getCurrentProject()->getId() : ''; ?>">
        <div class="list-mode">
            <?php include_component('project/projectheader', ['subpage' => __('Project commits'), 'show_back' => true]); ?>
            <div class="list-item header"><?php echo __('Branch filters'); ?></div>
            <?php foreach ($branches as $branch): ?>
                <a class="list-item multiline" href="javascript:void(0);" onclick="Pachno.Project.showBranchCommits('<?php echo make_url('livelink_project_commits_post', ['project_key' => $selected_project->getKey()]); ?>', '<?php echo $branch->getName(); ?>'); Pachno.Project.toggleLeftSelection(this);">
                    <span class="icon"><?= fa_image_tag('code-branch'); ?></span>
                    <span class="name">
                        <span class="title"><?php echo $branch->getName(); ?></span>
                        <span class="description">
                            <?php if ($branch->getLatestCommit() instanceof \pachno\core\entities\Commit): ?>
                                <?= __('Last commit: %date', ['%date' => \pachno\core\framework\Context::getI18n()->formatTime($branch->getLatestCommit()->getDate(), 20)]); ?>
                            <?php else: ?>
                                <?= __('No commits yet'); ?>
                            <?php endif; ?>
                        </span>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>
    <div id="project_commits_center_container" class="project_info_container">
        <?php if ($is_importing): ?>
            <div class="message-box type-warning">
                <span class="message">
                    <?= fa_image_tag('spinner', ['class' => 'fa-spin']) . __('This repository is still being imported and may not be fully up-to-date yet.'); ?>
                </span>
            </div>
        <?php endif; ?>
        <div id="project_commits">
            <p class="faded_out"><?php echo __('Choose branch on the left to filter commits for this project'); ?></p>
        </div>
    </div>
</div>
