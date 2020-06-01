<?php

    /** @var \pachno\core\entities\WorkflowScheme $scheme */

?>
<div id="workflow_scheme_<?php echo $scheme->getID(); ?>" class="row" data-workflow-scheme data-id="<?= $scheme->getID(); ?>">
    <div class="column name-container multiline">
        <div class="title"><?php echo $scheme->getName(); ?></div>
        <?php if ($scheme->getDescription()): ?>
            <div class="description"><?php echo $scheme->getDescription(); ?></div>
        <?php endif; ?>
    </div>
    <div class="column">
        <?php echo __('Issue types: %number_of_associated_issuetypes', array('%number_of_associated_issuetypes' => '<span>'.$scheme->getNumberOfAssociatedWorkflows().'</span>')); ?>
    </div>
    <div class="column actions">
        <?php if (isset($embed)): ?>
            <button class="button secondary" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_workflow_scheme', 'scheme_id' => $scheme->getId()]); ?>');">
                <?= fa_image_tag('edit', ['class' => 'icon']); ?>
                <span><?= __('Edit'); ?></span>
            </button>
        <?php else: ?>
            <div class="dropper-container">
                <button class="dropper button secondary">
                    <span><?= __('Actions'); ?></span>
                    <?= fa_image_tag('angle-down', ['class' => 'icon']); ?>
                </button>
                <div class="dropdown-container">
                    <div class="list-mode">
                        <a class="list-item" href="javascript:void(0);" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_workflow_scheme', 'scheme_id' => $scheme->getId()]); ?>');">
                            <span class="icon"><?= fa_image_tag('edit'); ?></span>
                            <span class="name"><?= __('Edit workflow scheme'); ?></span>
                        </a>
                        <?php if (\pachno\core\framework\Context::getScope()->isCustomWorkflowsEnabled()): ?>
                            <a class="list-item" href="javascript:void(0);" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_workflow_scheme', 'scheme_id' => $scheme->getId(), 'clone' => 'yes']); ?>');">
                                <span class="icon"><?php echo fa_image_tag('clone'); ?></span>
                                <span class="name"><?= __('Copy workflow scheme'); ?></span>
                            </a>
                        <?php endif; ?>
                        <div class="list-item separator"></div>
                        <?php if ($scheme->isInUse()): ?>
                            <a class="list-item disabled danger" href="javascript:void(0);" onclick="Pachno.UI.Message.error('<?php echo __('Cannot delete workflow scheme'); ?>', '<?php echo __('This workflow scheme can not be deleted as it is being used by %number_of_projects project(s)', array('%number_of_projects' => $scheme->getNumberOfProjects())); ?>');">
                                <span class="icon"><?php echo fa_image_tag('times'); ?></span>
                                <span class="name"><?= __('Delete workflow scheme'); ?></span>
                            </a>
                        <?php else: ?>
                            <a class="list-item danger" href="javascript:void(0);" onclick="Pachno.UI.Dialog.show('<?php echo __('Do you really want to delete this workflow scheme?'); ?>', '<?php echo __('Please confirm that you want to completely remove this workflow scheme.'); ?>', {yes: {click: function() { Pachno.Config.Workflows.Scheme.remove('<?php echo make_url('configure_workflow_delete_scheme', array('scheme_id' => $scheme->getID())); ?>', <?php echo $scheme->getID(); ?>); }}, no: { click: Pachno.UI.Dialog.dismiss }});">
                                <span class="icon"><?php echo fa_image_tag('times'); ?></span>
                                <span class="name"><?= __('Delete workflow scheme'); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
