<?php

    use pachno\core\entities\WorkflowTransitionValidationRule;
    use pachno\core\entities\User;
    use pachno\core\entities\Build;
    use pachno\core\entities\Component;
    use pachno\core\entities\common\Identifiable;
    use pachno\core\entities\Milestone;

    /** @var WorkflowTransitionValidationRule $rule */

?>
<div class="configurable-component form-container workflow-transition-validation-rule" data-workflow-transition-validation-rule data-id="<?php echo $rule->getID(); ?>">
    <form class="row" action="<?php echo make_url('configure_workflow_transition_validation_rule_post', ['workflow_id' => $rule->getWorkflow()->getID(), 'transition_id' => $rule->getTransition()->getID(), 'rule_id' => $rule->getID()]); ?>" onsubmit="Pachno.Config.Workflows.Transition.Validations.update(this);return false;" id="workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_form" data-interactive-form>
        <?php if ($rule->getRule() == WorkflowTransitionValidationRule::RULE_MAX_ASSIGNED_ISSUES): ?>
            <div class="name with-dropdown">
                <div class="form-row">
                    <div class="fancy-dropdown-container">
                        <div class="fancy-dropdown">
                            <label><?= __('Max number of assigned issues for current user'); ?></label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode">
                                <?php foreach (range(0, 10) as $option): ?>
                                    <input type="radio" name="rule_value" class="fancy-checkbox" id="edit-transition-rule-<?= $rule->getId(); ?>-<?= $option; ?>" value="<?= $option; ?>" <?php if ($rule->getRuleValue() == $option) echo ' checked'; ?>>
                                    <label for="edit-transition-rule-<?= $rule->getId(); ?>-<?= $option; ?>" class="list-item">
                                        <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                        <span class="name value"><?= __('%number_of_issues issue(s)', ['%number_of_issues' => $option]); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="name with-dropdown">
                <div class="form-row">
                    <div class="fancy-dropdown-container">
                        <div class="fancy-dropdown invisible">
                            <label>
                                <?php if ($rule->getRule() == WorkflowTransitionValidationRule::RULE_STATUS_VALID): ?>
                                    <?php echo __('Status is any of %list', ['%list' => '']); ?>
                                <?php elseif ($rule->getRule() == WorkflowTransitionValidationRule::RULE_PRIORITY_VALID): ?>
                                    <?php echo __('Priority is any of %list', ['%list' => '']); ?>
                                <?php elseif ($rule->getRule() == WorkflowTransitionValidationRule::RULE_RESOLUTION_VALID): ?>
                                    <?php echo __('Resolution is any of %list', ['%list' => '']); ?>
                                <?php elseif ($rule->getRule() == WorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID): ?>
                                    <?php echo __('Reproducability is any of %list', ['%list' => '']); ?>
                                <?php elseif ($rule->isCustom()): ?>
                                    <?php echo __('Custom field %customfield is any of %list', ['%customfield' => $rule->getCustomFieldname(), '%list' => '']); ?>
                                <?php elseif ($rule->getRule() == WorkflowTransitionValidationRule::RULE_TEAM_MEMBERSHIP_VALID && $rule->isPost()): ?>
                                    <?php echo __('Assignee must be member of %list', ['%list' => '']); ?>
                                <?php elseif ($rule->getRule() == WorkflowTransitionValidationRule::RULE_TEAM_MEMBERSHIP_VALID && $rule->isPre()): ?>
                                    <?php echo __('User must be member of %list', ['%list' => '']); ?>
                                <?php elseif ($rule->getRule() == WorkflowTransitionValidationRule::RULE_ISSUE_IN_MILESTONE_VALID && $rule->isPre()): ?>
                                    <?php echo __('Issue must be in any of these milestones %list', ['%list' => '']); ?>
                                <?php endif; ?>
                            </label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode">
                                <?php foreach ($rule->getRuleOptions() as $option): ?>
                                    <input type="radio" name="rule_value[<?= $option->getId(); ?>]" class="fancy-checkbox" id="edit-transition-rule-<?= $rule->getId(); ?>-<?= $option->getID(); ?>" value="<?= $option->getID(); ?>" <?php if ($rule->getRuleValue() == $option->getID()) echo ' checked'; ?>>
                                    <label for="edit-transition-rule-<?= $rule->getId(); ?>-<?= $option->getID(); ?>" class="list-item">
                                        <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                        <span class="name value">
                                            <?php if ($option instanceof User): ?>
                                                <?php echo $option->getNameWithUsername(); ?>
                                            <?php elseif ($option instanceof Milestone || $option instanceof Build || $option instanceof Component): ?>
                                                <?php echo $option->getProject()->getName() . ' - ' . $option->getName(); ?>
                                            <?php elseif ($option instanceof Identifiable): ?>
                                                <?php echo $option->getName(); ?>
                                            <?php else: ?>
                                                <?php echo $option; ?>
                                            <?php endif; ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="icon">
            <button class="secondary icon" onclick="Pachno.UI.Dialog.show('<?php echo __('Do you really want to delete this transition validation rule?'); ?>', '<?php echo __('Please confirm that you really want to delete this transition validation rule.'); ?>', {yes: {click: function() {Pachno.Config.Workflows.Transition.Validations.remove('<?php echo make_url('configure_workflow_transition_validation_rule_delete', array('workflow_id' => $rule->getWorkflow()->getID(), 'transition_id' => $rule->getTransition()->getID(), 'rule_id' => $rule->getID())); ?>', <?php echo $rule->getID(); ?>, '<?php echo $rule->isPreOrPost(); ?>', '<?php echo $rule->getRule(); ?>'); }}, no: { click: Pachno.UI.Dialog.dismiss }});return false;"><?php echo fa_image_tag('trash', ['class' => 'icon']); ?></button>
        </div>
    </form>
</div>
