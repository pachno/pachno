<?php

    /**
     * @var \pachno\core\entities\WorkflowTransition $transition
     * @var \pachno\core\entities\WorkflowStep $step
     * @var \pachno\core\entities\Status[] $statuses
     */
    
    use pachno\core\entities\Status;

?>
<div data-workflow-transition data-id="<?php echo $transition->getID(); ?>" class="configurable-component workflow-transition form-container">
    <form class="row" accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_workflow_transition', ['workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID()]); ?>" data-interactive-form id="workflow_transition_form_<?= $transition->getID(); ?>">
        <?= fa_image_tag('grip-vertical', ['class' => 'icon handle']); ?>
        <div class="name">
            <div class="form-row">
                <input type="text" name="name" id="workflow_transition_step_<?php echo $transition->getID(); ?>_name_input" value="<?php echo $transition->getName(); ?>" class="invisible">
                <label for="workflow_transition_step_<?php echo $transition->getID(); ?>_name_input"><?= __('Transition name'); ?></label>
            </div>
        </div>
        <div class="icon">
            <button class="button secondary trigger-backdrop" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'edit_workflow_transition', 'transition_id' => $transition->getId(), 'step_id' => $step->getId()]); ?>">
                <?= fa_image_tag('edit', ['class' => 'icon'], 'far'); ?>
            </button>
            <?php if (!$transition->isInitialTransition()): ?>
                <button class="button secondary icon" onclick="Pachno.UI.Dialog.show('<?php echo __('Really delete this transition?'); ?>', '<?php echo __('Are you really sure you want to delete this transition?'); ?>', {yes: {click: function() {Pachno.trigger(Pachno.EVENTS.configuration.deleteComponent, { url: '<?php echo make_url('configure_workflow_transition', ['workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID()]); ?>?step_id=<?= $step->getID(); ?>', type: 'workflow-transition', id: <?= $transition->getID(); ?>, close_container: false })}}, no: {click: Pachno.UI.Dialog.dismiss}});"><?php echo fa_image_tag('trash-alt', [], 'far'); ?></button>
            <?php endif; ?>
        </div>
    </form>
    <div class="row">
        <?php if ($transition->isInitialTransition()): ?>
            <div class="name"><span class="status-badge"><?= fa_image_tag('edit'); ?><span><?php echo __("Issue is created"); ?></span></span></div>
            <div class="icon"><?= fa_image_tag('arrow-right'); ?></div>
            <div class="name"><?php include_component('configuration/transitionstatusbadges', ['transition' => $transition]); ?></div>
        <?php elseif ($transition->getOutgoingStep()->getID() == $step->getID()): ?>
            <div class="name">
                <?php foreach ($transition->getIncomingSteps() as $step): ?>
                    <?php if (!$step->getLinkedStatus() instanceof Status) continue; ?>
                    <span class="status-badge" style="background-color: <?php echo $step->getLinkedStatus()->getColor(); ?>; color: <?php echo $step->getLinkedStatus()->getTextColor(); ?>;">
                        <span class="value"><?php echo $step->getLinkedStatus()->getName(); ?></span>
                    </span>
                <?php endforeach; ?>
            </div>
            <div class="icon"><?= fa_image_tag('arrow-right'); ?></div>
            <div class="name"><?php include_component('configuration/transitionstatusbadges', ['transition' => $transition]); ?></div>
        <?php else: ?>
            <div class="icon">&nbsp;</div>
            <div class="icon"><?= fa_image_tag('arrow-right'); ?></div>
            <div class="name"><?php include_component('configuration/transitionstatusbadges', ['transition' => $transition]); ?></div>
        <?php endif; ?>
    </div>
</div>
