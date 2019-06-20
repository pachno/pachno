<?php

    /** @var \pachno\core\entities\WorkflowStep $step */

?>
<div class="form-container">
    <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_workflow_step_post', ['step_id' => $step->getID(), 'workflow_id' => $step->getWorkflow()->getID()]); ?>" onsubmit="Pachno.Config.Workflows.Workflow.Step.save(this);return false;" data-interactive-form>
        <div class="form-row header">
            <h4><?= __('Step settings'); ?></h4>
        </div>
        <div class="form-row">
            <div class="fancydropdown-container">
                <div class="fancydropdown invisible embedded">
                    <label><?php echo __('Issue status'); ?></label>
                    <span class="value"></span>
                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                    <div class="dropdown-container list-mode">
                        <?php foreach (\pachno\core\entities\Status::getAll() as $status): ?>
                            <input type="radio" name="itemdata" value="<?= $status->getId(); ?>" id="edit_step_details_<?= $step->getId(); ?>_status_<?= $status->getID(); ?>" class="fancycheckbox" <?php if ($step->getLinkedStatusID() == $status->getId()) echo 'checked'; ?>>
                            <label class="list-item" for="edit_step_details_<?= $step->getId(); ?>_status_<?= $status->getID(); ?>">
                                <span class="name"><span class="status-badge" style="background-color: <?php echo $status->getColor(); ?>;"><span class="value"><?php echo $status->getName(); ?></span></span></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-row">
            <input type="checkbox" value="1" name="editable" id="edit_step_<?= $step->getID(); ?>_editable_yes" class="fancycheckbox" <?php if ($step->isEditable()) echo 'checked'; ?>>
            <label for="edit_step_<?= $step->getID(); ?>_editable_yes">
                <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                <span class="name value"><?php echo __('Issue details can be edited when in this step'); ?></span>
            </label>
        </div>
        <div class="form-row">
            <input type="checkbox" value="1" name="editable" id="edit_step_<?= $step->getID(); ?>_closed_yes" class="fancycheckbox" <?php if ($step->isClosed()) echo 'checked'; ?>>
            <label for="edit_step_<?= $step->getID(); ?>_closed_yes">
                <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                <span class="name value"><?php echo __('Issues are closed when reaching this step'); ?></span>
            </label>
        </div>
        <div class="form-row submit-container">
            <a class="button secondary" href="javascript:void(0);" onclick="Pachno.Main.Helpers.Dialog.show('<?= __('Do you really want to delete this workflow step?'); ?>', '<?=__('This will set the status of any issues currently in this step back to the initial workflow step. It will also remove any transitions to / from this step.'); ?>', {yes: {click: function() {Pachno.Config.Workflows.Workflow.Step.remove('<?= make_url('configure_workflow_delete_step', ['workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID()]); ?>')}}, no: { click: Pachno.Main.Helpers.Dialog.dismiss }});">
                <span class="icon"><?= fa_image_tag('times'); ?></span>
                <span class="name"><?= __('Remove step'); ?></span>
            </a>
        </div>
        <div class="form-row error-container">
            <div class="error"></div>
            <?= fa_image_tag('spinner', ['class' => 'fa-spin submit-indicator icon']); ?>
        </div>
    </form>
</div>
<h5>
    <span class="name">
        <span><?= __('Outgoing transitions'); ?></span>
        <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator', 'id' => 'edit_transitions_indicator', 'style' => 'display: none;']); ?>
    </span>
    <span class="dropper-container">
        <button class="button secondary dropper"><?= __('Add transition'); ?></button>
        <span class="dropdown-container">
            <span class="list-mode">
                <span class="list-item filter-container">
                    <input type="search" placeholder="<?= __('Filter values'); ?>">
                </span>
                <span class="column">
                    <span class="list-item header"><?= __('Add existing transition'); ?></span>
                    <?php foreach ($step->getWorkflow()->getTransitions() as $transition): ?>
                        <?php if ($transition->getOutgoingStep() instanceof \pachno\core\entities\WorkflowStep && $transition->getOutgoingStep()->getID() == $step->getID()) continue; ?>
                        <?php $has_transition = ($step->hasOutgoingTransition($transition)); ?>
                        <a href="javascript:void(0);" class="list-item multiline <?php if ($has_transition) echo 'disabled'; ?>" data-workflow-transition data-id="<?= $transition->getID(); ?>" data-url="<?= make_url('configure_workflow_add_transition', array('workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID())); ?>">
                            <span class="name">
                                <span class="title"><?= $transition->getName(); ?></span>
                                <span class="description">
                                    <span class="icon"><?= fa_image_tag('arrow-right'); ?></span>
                                    <span><?php include_component('configuration/transitionstatusbadges', ['transition' => $transition]); ?></span>
                                </span>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </span>
            </span>
        </span>
    </span>
</h5>
<div class="configurable-components-list" id="outgoing-transitions-list">
    <?php foreach ($step->getOutgoingTransitions() as $transition): ?>
        <?php include_component('configuration/editworkflowtransition', ['transition' => $transition, 'step' => $step]); ?>
    <?php endforeach; ?>
</div>
<?php /*<td>
    <?php if ($step->hasLinkedStatus()): ?>
        <table style="table-layout: auto; width: auto;" cellpadding=0 cellspacing=0>
            <tr class="status">
                <td style="width: 16px; height: 16px;"><div style="border: 1px solid #AAA; background-color: <?php echo ($step->getLinkedStatus() instanceof \pachno\core\entities\Datatype) ? $step->getLinkedStatus()->getColor() : '#FFF'; ?>; font-size: 1px; width: 15px; height: 15px; margin-right: 2px;">&nbsp;</div></td>
                <td style="padding-left: 0px;"><?php echo $step->getLinkedStatus()->getName(); ?></td>
            </tr>
        </table>
    <?php else: ?>
        <span class="faded_out"> - </span>
    <?php endif; ?>
</td>
<td>
    <?php if ($step->getNumberOfOutgoingTransitions() > 0): ?>
        <?php foreach ($step->getOutgoingTransitions() as $transition): ?>
            <div class="workflow_step_transition_name">
                <?php echo link_tag(make_url('configure_workflow_transition', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID())), $transition->getName()); ?>
                <span class="workflow_step_transition_outgoing_step">&rarr; <?php echo $transition->getOutgoingStep()->getName(); ?></span>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="faded_out"> - </div>
    <?php endif; ?>
</td>
<td class="workflow_step_actions">
    <?php echo link_tag(make_url('configure_workflow_step', array('workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID())), __('Edit step')); ?> |
    <?php if ($step->hasIncomingTransitions()): ?>
        <span class="faded_out"><a href="javascript:void(0);" class="disabled" onclick="Pachno.Main.Helpers.Message.error('<?php echo __('You cannot delete a step with incoming transitions'); ?>', '<?php echo __('To delete a step that has incoming transitions, first remove all incoming transitions'); ?>');"><?php echo __('Delete step'); ?></a></span><br>
    <?php elseif ($step->getWorkflow()->getNumberOfSteps() == 1): ?>
        <span class="faded_out"><a href="javascript:void(0);" class="disabled" onclick="Pachno.Main.Helpers.Message.error('<?php echo __('You cannot delete the last step'); ?>', '<?php echo __('To delete this step, make sure there are other steps available'); ?>');"><?php echo __('Delete step'); ?></a></span><br>
    <?php else: ?>
        <?php echo javascript_link_tag(__('Delete step'), array('onclick' => "\$('step_{$step->getID()}_delete').toggle();")); ?><br>
    <?php endif; ?>
    <?php echo javascript_link_tag(__('Add transition'), array('onclick' => "$('step_{$step->getID()}_transition_add').toggle()")); ?> |
    <?php echo javascript_link_tag(__('Delete outgoing transitions'), array('onclick' => "\$('step_{$step->getID()}_transitions_delete').toggle();")); ?>
</td> */ ?>
