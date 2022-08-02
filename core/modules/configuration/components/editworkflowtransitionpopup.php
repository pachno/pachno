<?php
    
    use pachno\core\entities\Status;
    use pachno\core\entities\Workflow;
    use pachno\core\entities\WorkflowStep;
    use pachno\core\entities\WorkflowTransition;
    use pachno\core\entities\WorkflowTransitionValidationRule;
    use pachno\core\entities\WorkflowTransitionAction;
    use pachno\core\framework\Context;

    /**
     * @var WorkflowTransition $transition
     * @var Workflow $workflow
     * @var ?WorkflowStep $step
     */

?>
<div class="backdrop_box large edit-workflow-transition">
    <div class="backdrop_detail_header">
        <span><?= ($transition->getId()) ? __('Edit workflow transition') : __('Create new transition'); ?></span>
        <?php if (!$transition->getID()): ?>
            <a href="javascript:void(0);" class="closer"><?= fa_image_tag('times'); ?></a>
        <?php endif; ?>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <form accept-charset="<?php echo Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_workflow_transition', ['workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID()]); ?>" <?= $transition->getID() ? 'data-interactive-form' : 'data-simple-submit data-auto-close'; ?> id="edit-workflow-transition-<?= (int) $transition->getID(); ?>-form">
                <input type="hidden" name="step_id" value="<?= $step->getID(); ?>">
                <div class="form-row">
                    <div class="message-box type-info">
                        <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                        <span><?= __('These settings apply to all steps in the workflow using this transition.'); ?></span>
                    </div>
                </div>
                <div class="form-row">
                    <input type="text" name="name" id="workflow_transition_step_<?php echo $transition->getID(); ?>_name_input_popup" value="<?php echo $transition->getName(); ?>" class="name-input-enhance">
                    <label for="workflow_transition_step_<?php echo $transition->getID(); ?>_name_input_popup"><?= __('Transition name'); ?></label>
                </div>
                <div class="form-row">
                    <div class="fancy-dropdown-container">
                        <div class="fancy-dropdown">
                            <label><?= __('Type of transition'); ?></label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode">
                                <?php $template_counter = 0; ?>
                                <?php foreach (WorkflowTransition::getTemplates() as $template_key => $template_name): ?>
                                    <input type="radio" class="fancy-checkbox" id="workflow_transition_step_<?= $transition->getId(); ?>_template_<?= $template_counter; ?>" name="template" value="<?= $template_key; ?>" <?php if ($transition->getTemplate() == $template_key) echo ' checked'; ?>>
                                    <label for="workflow_transition_step_<?= $transition->getId(); ?>_template_<?= $template_counter; ?>" class="list-item">
                                        <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                        <span class="name value"><?= $template_name; ?></span>
                                    </label>
                                    <?php $template_counter += 1; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <input type="text" name="description" id="workflow_transition_step_<?php echo $transition->getID(); ?>_description_input_popup" value="<?php echo $transition->getDescription(); ?>" placeholder="<?= __('Add an optional description for this transition'); ?>">
                    <label for="workflow_transition_step_<?php echo $transition->getID(); ?>_description_input_popup"><?= __('Description (optional)'); ?></label>
                </div>
                <?php if ($transition->getID() && $transition->getNumberOfIncomingSteps() == 0 && $transition->getID() !== $workflow->getInitialTransition()->getID()): ?>
                    <div class="form-row"><?php echo __("This transaction doesn't have any originating step"); ?></div>
                <?php else: ?>
                    <div class="form-row">
                        <div class="workflow-transition-map">
                            <div class="incoming-step step-list">
                                <?php if ($transition === $workflow->getInitialTransition()): ?>
                                    <span class="status-badge"><?= fa_image_tag('edit'); ?><span><?php echo __("Issue is created"); ?></span></span>
                                <?php elseif (!$transition->getID()): ?>
                                    <span class="status-badge" style="background-color: <?php echo $step->getLinkedStatus()->getColor(); ?>; color: <?php echo $step->getLinkedStatus()->getTextColor(); ?>;">
                                        <span class="value"><?php echo $step->getLinkedStatus()->getName(); ?></span>
                                    </span>
                                <?php endif; ?>
                                <?php foreach ($transition->getIncomingSteps() as $step): ?>
                                    <?php if (!$step->getLinkedStatus() instanceof Status) continue; ?>
                                    <span class="status-badge" style="background-color: <?php echo $step->getLinkedStatus()->getColor(); ?>; color: <?php echo $step->getLinkedStatus()->getTextColor(); ?>;">
                                        <span class="value"><?php echo $step->getLinkedStatus()->getName(); ?></span>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                            <div class="current-transition">
                                <span class="icon"><?= fa_image_tag('arrow-right'); ?></span>
                                <span class="transition-name"><?= $transition->getName(); ?></span>
                                <span class="icon"><?= fa_image_tag('arrow-right'); ?></span>
                            </div>
                            <div class="outgoing-step step-list">
                                <div class="fancy-dropdown-container">
                                    <div class="fancy-dropdown" data-default-label="<?= __('Select a workflow step'); ?>">
                                        <span class="value"></span>
                                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                        <div class="dropdown-container list-mode">
                                            <?php foreach ($transition->getWorkflow()->getSteps() as $workflow_step): ?>
                                                <input type="radio" class="fancy-checkbox" id="workflow_transition_step_<?= $transition->getId(); ?>_outgoing_step" name="outgoing_step_id" value="<?= $workflow_step->getId(); ?>" <?php if ($transition->getOutgoingStep() instanceof WorkflowStep && $transition->getOutgoingStep()->getID() == $workflow_step->getID()) echo ' checked'; ?>>
                                                <label for="workflow_transition_step_<?= $transition->getId(); ?>_outgoing_step" class="list-item">
                                                    <span class="name value">
                                                        <?php if ($workflow_step->getLinkedStatus() instanceof \pachno\core\entities\Status): ?>
                                                            <span class="status-badge" style="background-color: <?php echo $workflow_step->getLinkedStatus()->getColor(); ?>; color: <?php echo $workflow_step->getLinkedStatus()->getTextColor(); ?>;">
                                                                <span class="value"><?php echo $workflow_step->getLinkedStatus()->getName(); ?></span>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="status-badge">
                                                                <span class="value"><?php echo $workflow_step->getName(); ?></span>
                                                            </span>
                                                        <?php endif; ?>
                                                    </span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (!$transition->getID()): ?>
                    <div class="form-row submit-container">
                        <button type="submit" class="button primary">
                            <?= fa_image_tag('spinner', ['class' => 'fa-spin submit-indicator icon']); ?>
                            <span><?= __('Save'); ?></span>
                        </button>
                    </div>
                <?php endif; ?>
            </form>
            <?php if ($transition->getID()): ?>
                <div class="form-row header">
                    <h3><?= __('Validation rules'); ?></h3>
                </div>
                <div class="column">
                    <div class="form-row">
                        <h5>
                            <span class="name"><?php echo __('Before transitioning'); ?></span>
                            <span class="dropper-container">
                                <button class="button secondary dropper"><?= __('Add rule'); ?></button>
                                <span class="dropdown-container add-validation-rule-list">
                                    <span class="list-mode">
                                        <?php foreach (WorkflowTransitionValidationRule::getAvailablePreValidationRules() as $key => $description): ?>
                                            <a class="list-item <?php if ($transition->hasPreValidationRule($key)) echo ' disabled'; ?>" href="javascript:void(0);" data-add-workflow-validation-rule data-id="<?= $key; ?>" data-url="<?php echo make_url('configure_workflow_transition_validation_rule', ['workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'rule_id' => 0]); ?>?postorpre=pre&rule=<?= $key; ?>" data-rule-type="pre">
                                                <span class="icon"><?= fa_image_tag('edit'); ?></span>
                                                <span class="name"><?php echo $description; ?></span>
                                            </a>
                                        <?php endforeach; ?>
                                    </span>
                                </span>
                            </span>
                        </h5>
                        <div id="pre_validation_tab_pane">
                            <?php if ($transition !== $workflow->getInitialTransition()): ?>
                                <div class="configurable-components-list" id="workflowtransitionprevalidationrules_list" data-placeholder="<?= __('No validation rules set up'); ?>"><?php foreach ($transition->getPreValidationRules() as $rule) include_component('configuration/workflowtransitionvalidationrule', ['rule' => $rule]); ?></div>
                            <?php else: ?>
                                <span class="faded_out"><?php echo __('This is the initial transition, so no pre-transition validation is performed'); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="form-row">
                        <h5>
                            <span class="name"><?php echo __('After transitioning'); ?></span>
                            <span class="dropper-container">
                                <button class="button secondary dropper"><?= __('Add rule'); ?></button>
                                <span class="dropdown-container">
                                    <span class="list-mode add-validation-rule-list">
                                        <?php foreach (WorkflowTransitionValidationRule::getAvailablePostValidationRules() as $key => $description): ?>
                                            <a class="list-item <?php if ($transition->hasPostValidationRule($key)) echo ' disabled'; ?>" href="javascript:void(0);" data-add-workflow-validation-rule data-id="<?= $key; ?>" data-url="<?php echo make_url('configure_workflow_transition_validation_rule', ['workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'rule_id' => 0]); ?>?postorpre=post&rule=<?= $key; ?>" data-rule-type="post">
                                                <span class="icon"><?= fa_image_tag('edit'); ?></span>
                                                <span class="name"><?php echo $description; ?></span>
                                            </a>
                                        <?php endforeach; ?>
                                    </span>
                                </span>
                            </span>

                        </h5>
                        <div class="configurable-components-list" id="workflowtransitionpostvalidationrules_list" data-placeholder="<?= __('No validation rules set up'); ?>"><?php foreach ($transition->getPostValidationRules() as $rule) include_component('configuration/workflowtransitionvalidationrule', array('rule' => $rule)); ?></div>
                    </div>
                </div>
                <div class="form-row header">
                    <h3>
                        <span class="name"><?= __('Actions applied during this transition'); ?></span>
                        <span class="dropper-container">
                            <button class="button secondary dropper"><?= __('Add transition action'); ?></button>
                            <span class="dropdown-container list-mode columns three-columns from-bottom" id="add-transition-action">
                                <?php foreach (['set', 'clear', 'special'] as $category): ?>
                                    <span class="column">
                                        <span class="list-item header">
                                            <?php if ($category == 'set'): ?>
                                                <?= __('Set issue fields'); ?>
                                            <?php elseif ($category == 'clear'): ?>
                                                <?= __('Clear issue fields'); ?>
                                            <?php else: ?>
                                                <?= __('Special actions'); ?>
                                            <?php endif; ?>
                                        </span>
                                        <?php foreach (WorkflowTransitionAction::getAvailableTransitionActions($category) as $key => $description): ?>
                                            <?php if ($key == WorkflowTransitionAction::ACTION_SET_STATUS) continue; ?>
                                            <a class="list-item <?php if ($transition->hasAction($key)) echo ' disabled'; ?>" href="javascript:void(0);" data-add-workflow-transition-action data-id="<?= $key; ?>" data-url="<?php echo make_url('configure_workflow_transition_action', ['workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID()]); ?>?action_type=<?= $key; ?>">
                                                <span class="icon"><?= fa_image_tag('edit'); ?></span>
                                                <span class="name"><?php echo $description; ?></span>
                                            </a>
                                        <?php endforeach; ?>
                                    </span>
                                <?php endforeach; ?>
                            </span>
                        </span>
                    </h3>
                </div>
                <div class="form-row">
                    <div class="content" style="padding: 5px 0 10px 2px;">
                        <?php echo __('The following actions will be applied to the issue during this transition.'); ?>
                    </div>
                    <div class="configurable-components-list" id="workflow-transition-actions-list" data-placeholder="<?= __('No actions performed during this transition'); ?>"><?php foreach ($transition->getActions() as $action) include_component('configuration/workflowtransitionaction', ['action' => $action]); ?></div>
                </div>
                <div class="form-row submit-container">
                    <a href="javascript:void(0);" class="closer button primary" onclick="Pachno.UI.Backdrop.reset();"><?= __('Done'); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
