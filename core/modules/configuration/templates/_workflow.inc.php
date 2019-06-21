<?php

    use pachno\core\entities\Workflow,
        pachno\core\entities\Status;

    /** @var Workflow $workflow */

?>
<div id="workflow_<?php echo $workflow->getID(); ?>" class="row" style="margin-bottom: 5px;" data-workflow data-id="<?= $workflow->getId(); ?>">
    <div class="column name-container multiline">
        <div class="title"><?php echo $workflow->getName(); ?><?php if (!$workflow->isActive()): ?><span class="count-badge"><?= __('Draft'); ?></span><?php endif; ?></div>
        <?php if ($workflow->getDescription()): ?>
            <div class="description"><?php echo $workflow->getDescription(); ?></div>
        <?php endif; ?>
    </div>
    <div class="column grid">
        <?php foreach ($workflow->getSteps() as $step): ?>
            <div class="status-badge" style="background-color: <?php echo ($step->getLinkedStatus() instanceof Status) ? $step->getLinkedStatus()->getColor() : '#FFF'; ?>; color: <?php echo ($step->getLinkedStatus() instanceof Status) ? $step->getLinkedStatus()->getTextColor() : 'inherit'; ?>;">
                <span><?php echo ($step->getLinkedStatus() instanceof Status) ? $step->getLinkedStatus()->getName() : __('Unknown'); ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="column actions">
        <div class="dropper-container">
            <button class="dropper button secondary">
                <span><?= __('Actions'); ?></span>
                <?= fa_image_tag('angle-down', ['class' => 'icon']); ?>
            </button>
            <div class="dropdown-container">
                <div class="list-mode">
                    <a class="list-item" href="<?= make_url('configure_workflow', ['workflow_id' => $workflow->getID()]); ?>">
                        <span class="icon"><?= fa_image_tag('edit'); ?></span>
                        <span class="name"><?= __('Edit workflow'); ?></span>
                    </a>
                    <?php if (\pachno\core\framework\Context::getScope()->hasCustomWorkflowsAvailable()): ?>
                        <a class="list-item" href="javascript:void(0);" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_workflow', 'workflow_id' => $workflow->getId(), 'clone' => 'yes']); ?>');">
                            <span class="icon"><?php echo fa_image_tag('clone'); ?></span>
                            <span class="name"><?= __('Copy workflow'); ?></span>
                        </a>
                    <?php endif; ?>
                    <div class="list-item separator"></div>
                    <?php if ($workflow->isInUse()): ?>
                        <a class="list-item danger" href="javascript:void(0);" onclick="Pachno.Main.Helpers.Message.error('<?php echo __('Cannot delete workflow'); ?>', '<?php echo __('This workflow can not be deleted as it is being used by %number_of_schemes workflow scheme(s)', array('%number_of_schemes' => $workflow->getNumberOfSchemes())); ?>');">
                            <span class="icon"><?php echo fa_image_tag('times'); ?></span>
                            <span class="name"><?= __('Delete workflow'); ?></span>
                        </a>
                    <?php else: ?>
                        <a class="list-item danger" href="javascript:void(0);" onclick="$('delete_workflow_<?php echo $workflow->getID(); ?>_popup').toggle();">
                            <span class="icon"><?php echo fa_image_tag('times'); ?></span>
                            <span class="name"><?= __('Delete workflow'); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /* if (\pachno\core\framework\Context::getScope()->hasCustomWorkflowsAvailable()): ?>
    <li class="rounded_box white shadowed copy_workflow_popup" id="copy_workflow_<?php echo $workflow->getID(); ?>_popup" style="margin-bottom: 5px; padding: 10px; display: none;">
        <div class="header"><?php echo __('Copy workflow'); ?></div>
        <div class="content">
            <?php echo __('Please enter the name of the new workflow'); ?><br>
            <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_workflow_copy_workflow', array('workflow_id' => $workflow->getID())); ?>" onsubmit="Pachno.Config.Workflows.Workflow.copy('<?php echo make_url('configure_workflow_copy_workflow', array('workflow_id' => $workflow->getID())); ?>', <?php echo $workflow->getID(); ?>);return false;" id="copy_workflow_<?php echo $workflow->getID(); ?>_form">
                <label for="copy_workflow_<?php echo $workflow->getID(); ?>_new_name"><?php echo __('New name'); ?></label>
                <input type="text" name="new_name" id="copy_workflow_<?php echo $workflow->getID(); ?>_new_name" value="<?php echo __('Copy of %old_name', array('%old_name' => addslashes($workflow->getName()))); ?>" style="width: 300px;">
                <div style="text-align: right;">
                    <?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'copy_workflow_'.$workflow->getID().'_indicator')); ?>
                    <input type="submit" value="<?php echo __('Copy workflow'); ?>">
                </div>
            </form>
        </div>
    </li>
<?php endif; ?>
<?php if (!$workflow->isInUse()): ?>
    <li class="rounded_box white shadowed" id="delete_workflow_<?php echo $workflow->getID(); ?>_popup" style="margin-bottom: 5px; padding: 10px; display: none;">
        <div class="header"><?php echo __('Are you sure?'); ?></div>
        <div class="content">
            <?php echo __('Please confirm that you want to delete this workflow.'); ?><br>
            <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_workflow_delete_workflow', array('workflow_id' => $workflow->getID())); ?>" onsubmit="Pachno.Config.Workflows.Workflow.remove('<?php echo make_url('configure_workflow_delete_workflow', array('workflow_id' => $workflow->getID())); ?>', <?php echo $workflow->getID(); ?>);return false;" id="delete_workflow_<?php echo $workflow->getID(); ?>_form">
                <div style="text-align: right;">
                    <?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'delete_workflow_'.$workflow->getID().'_indicator')); ?>
                    <input type="submit" value="<?php echo __('Yes, delete it'); ?>"><?php echo __('%delete or %cancel', array('%delete' => '', '%cancel' => '<b>'.javascript_link_tag(__('cancel'), array('onclick' => "$('delete_workflow_{$workflow->getID()}_popup').toggle();")).'</b>')); ?>
                </div>
            </form>
        </div>
    </li>
<?php endif; */ ?>
