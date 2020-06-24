<?php

    use pachno\core\entities\Status;
    use pachno\core\entities\WorkflowStep;
    use pachno\core\entities\WorkflowTransition;
    use pachno\core\entities\WorkflowTransitionValidationRule;

    /**
     * @var WorkflowTransition $transition
     * @var Status[] $statuses
     */

?>
<?php if ($transition->getOutgoingStep() instanceof WorkflowStep && $transition->getOutgoingStep()->getLinkedStatus() instanceof Status): ?>
    <span class="status-badge" style="background-color: <?php echo $transition->getOutgoingStep()->getLinkedStatus()->getColor(); ?>; color: <?php echo $transition->getOutgoingStep()->getLinkedStatus()->getTextColor(); ?>;">
        <span class="value"><?php echo $transition->getOutgoingStep()->getLinkedStatus()->getName(); ?></span>
    </span>
<?php elseif (!$transition->hasPostValidationRule(WorkflowTransitionValidationRule::RULE_STATUS_VALID)): ?>
    <span class="status-badge">
        <span class="value"><?php echo __('Any status'); ?></span>
    </span>
<?php else: ?>
    <?php foreach ($statuses as $status): ?>
        <?php if ($transition->getPostValidationRule(WorkflowTransitionValidationRule::RULE_STATUS_VALID)->isValueValid($status)): ?>
            <span class="status-badge" style="background-color: <?php echo $status->getColor(); ?>; color: <?php echo $status->getTextColor(); ?>;">
                <span class="value"><?php echo $status->getName(); ?></span>
            </span>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
