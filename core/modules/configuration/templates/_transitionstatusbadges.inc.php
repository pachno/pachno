<?php if ($transition->getOutgoingStep() instanceof \pachno\core\entities\Status): ?>
    <span class="status-badge" style="background-color: <?php echo $transition->getOutgoingStep()->getLinkedStatus()->getColor(); ?>;">
        <span class="value"><?php echo $transition->getOutgoingStep()->getLinkedStatus()->getName(); ?></span>
    </span>
<?php elseif (!$transition->hasPostValidationRule(\pachno\core\entities\WorkflowTransitionValidationRule::RULE_STATUS_VALID)): ?>
    <span class="status-badge">
        <span class="value"><?php echo __('Any status'); ?></span>
    </span>
<?php else: ?>
    <?php foreach ($statuses as $status): ?>
        <?php if ($transition->getPostValidationRule(\pachno\core\entities\WorkflowTransitionValidationRule::RULE_STATUS_VALID)->isValueValid($status)): ?>
            <span class="status-badge" style="background-color: <?php echo $status->getColor(); ?>;">
                <span class="value"><?php echo $status->getName(); ?></span>
            </span>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
