<?php

    use pachno\core\entities\WorkflowStep;
    use pachno\core\framework\Context;
    
    /** @var WorkflowStep $step */
    
?>
<div class="form-container">
    <form id="edit-workflow-step-<?= $step->getID(); ?>-form" accept-charset="<?= Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_workflow_step', ['step_id' => $step->getID(), 'workflow_id' => $step->getWorkflow()->getID()]); ?>" data-interactive-form data-update-container="#workflow_step_component_<?= $step->getID(); ?>" data-update-replace>
        <div class="form-row header">
            <h4><?= __('Step settings'); ?></h4>
        </div>
        <div class="form-row">
            <div class="fancy-dropdown-container">
                <div class="fancy-dropdown invisible embedded">
                    <label><?php echo __('Issue status'); ?></label>
                    <span class="value"></span>
                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                    <div class="dropdown-container list-mode">
                        <?php foreach (\pachno\core\entities\Status::getAll() as $status): ?>
                            <?php if ($status->getID() != $step->getLinkedStatusID() && $step->getWorkflow()->hasStatusId($status->getID())): ?>
                                <label class="list-item disabled">
                                    <span class="name"><span class="status-badge disabled"><span class="value"><?php echo $status->getName(); ?></span></span></span>
                                </label>
                            <?php else: ?>
                                <input type="radio" name="status_id" value="<?= $status->getId(); ?>" id="edit_step_details_<?= $step->getId(); ?>_status_<?= $status->getID(); ?>" class="fancy-checkbox" <?php if ($step->getLinkedStatusID() == $status->getId()) echo 'checked'; ?>>
                                <label class="list-item" for="edit_step_details_<?= $step->getId(); ?>_status_<?= $status->getID(); ?>">
                                    <span class="name"><span class="status-badge" style="background-color: <?php echo $status->getColor(); ?>; color: <?php echo $status->getTextColor(); ?>;"><span class="value"><?php echo $status->getName(); ?></span></span></span>
                                </label>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-row">
            <input type="checkbox" value="1" name="is_editable" id="edit_step_<?= $step->getID(); ?>_editable_yes" class="fancy-checkbox" <?php if ($step->isEditable()) echo 'checked'; ?>>
            <label for="edit_step_<?= $step->getID(); ?>_editable_yes">
                <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                <span class="name value"><?php echo __('Issue details can be edited when in this step'); ?></span>
            </label>
        </div>
        <div class="form-row">
            <input type="checkbox" value="1" name="state" id="edit_step_<?= $step->getID(); ?>_closed_yes" class="fancy-checkbox" <?php if ($step->isClosed()) echo 'checked'; ?>>
            <label for="edit_step_<?= $step->getID(); ?>_closed_yes">
                <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                <span class="name value"><?php echo __('Issues are closed when reaching this step'); ?></span>
            </label>
        </div>
        <div class="form-row submit-container">
            <a class="button secondary" href="javascript:void(0);" onclick="Pachno.UI.Dialog.show('<?= __('Do you really want to delete this workflow step?'); ?>', '<?=__('This will set the status of any issues currently in this step back to the initial workflow step. It will also remove any transitions to / from this step.'); ?>', {yes: {click: function() {Pachno.trigger(Pachno.EVENTS.configuration.deleteComponent, { url: '<?= make_url('configure_workflow_step', ['workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID()]); ?>', type: 'workflow-step', id: <?= $step->getID(); ?>, close_container: false })}}, no: { click: Pachno.UI.Dialog.dismiss }});">
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
        <span><?= __('Incoming transitions'); ?></span>
    </span>
</h5>
<div class="configurable-components-list" data-placeholder="<?= __('No other steps transition to this step'); ?>" id="incoming-transitions-list"><?php foreach ($step->getIncomingTransitions() as $transition) include_component('configuration/editworkflowtransition', ['transition' => $transition, 'step' => $step]); ?></div>
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
                <span class="list-item header"><?= __('Add existing transition'); ?></span>
                <span class="filter-values-container" id="add-transition-list">
                    <?php foreach ($step->getWorkflow()->getTransitions() as $transition): ?>
                        <?php if ($transition->getOutgoingStep() instanceof WorkflowStep && $transition->getOutgoingStep()->getID() == $step->getID()) continue; ?>
                        <?php include_component('configuration/workflowtransition', ['transition' => $transition, 'step' => $step]); ?>
                    <?php endforeach; ?>
                </span>
                <span class="list-item separator"></span>
                <a href="javascript:void(0);" class="list-item multiline trigger-backdrop" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'edit_workflow_transition', 'workflow_id' => $step->getWorkflow()->getId(), 'step_id' => $step->getID()]); ?>">
                    <span class="name">
                        <span class="title"><?= __('New transition'); ?></span>
                        <span class="description">
                            <span><?= __('Create a new transition') ?></span>
                        </span>
                    </span>
                </a>
            </span>
        </span>
    </span>
</h5>
<div class="configurable-components-list" data-placeholder="<?= __('This step does not transition to any other steps'); ?>" id="outgoing-transitions-list" data-auto-sortable data-sortable-url="<?= make_url('configure_workflow_step_transition_order', ['workflow_id' => $step->getWorkflow()->getId(), 'step_id' => $step->getID()]); ?>" data-draggable-class="workflow-transition"><?php foreach ($step->getOutgoingTransitions() as $transition) include_component('configuration/editworkflowtransition', ['transition' => $transition, 'step' => $step]); ?></div>
