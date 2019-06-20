<?php

    use pachno\core\entities\Workflow;
    use pachno\core\entities\WorkflowTransition;
    use pachno\core\entities\WorkflowTransitionValidationRule;
    use pachno\core\entities\WorkflowTransitionAction;
    use pachno\core\framework\Context;

    /**
     * @var WorkflowTransition $transition
     * @var Workflow $workflow
     */

?>
<div class="backdrop_box large edit-workflow-transition">
    <div class="backdrop_detail_header">
        <span><?= ($transition->getId()) ? __('Edit workflow transition') : __('Create new transition'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="Pachno.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <form accept-charset="<?php echo Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_workflow_transition_post', ['workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID()]); ?>" onsubmit="Pachno.Config.Workflows.Transition.save(this);return false;" data-interactive-form>
                <div class="form-row">
                    <input type="text" name="name" id="workflow_transition_step_<?php echo $transition->getID(); ?>_name_input_popup" value="<?php echo $transition->getName(); ?>" class="name-input-enhance">
                    <label for="workflow_transition_step_<?php echo $transition->getID(); ?>_name_input_popup"><?= __('Transition name'); ?></label>
                </div>
                <div class="form-row">
                    <div class="fancydropdown-container">
                        <div class="fancydropdown">
                            <label><?= __('Type of transition'); ?></label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode">
                                <?php $template_counter = 0; ?>
                                <?php foreach (WorkflowTransition::getTemplates() as $template_key => $template_name): ?>
                                    <input type="radio" class="fancycheckbox" id="workflow_transition_step_<?= $transition->getId(); ?>_template_<?= $template_counter; ?>" name="template" value="<?= $template_key; ?>" <?php if ($transition->getTemplate() == $template_key) echo ' checked'; ?>>
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
                <?php if ($transition->getNumberOfIncomingSteps() == 0 && $transition->getID() !== $workflow->getInitialTransition()->getID()): ?>
                    <div class="form-row"><?php echo __("This transaction doesn't have any originating step"); ?></div>
                <?php else: ?>
                    <div class="form-row">
                        <div class="workflow-transition-map">
                            <div class="incoming-step step-list">
                                <?php if ($transition === $workflow->getInitialTransition()): ?>
                                    <span class="status-badge"><?= fa_image_tag('edit'); ?><span><?php echo __("Issue is created"); ?></span></span>
                                <?php endif; ?>
                            <?php foreach ($transition->getIncomingSteps() as $step): ?>
                                <span class="status-badge" style="background-color: <?php echo $step->getLinkedStatus()->getColor(); ?>;">
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
                                <div class="fancydropdown-container">
                                    <div class="fancydropdown">
                                        <span class="value"></span>
                                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                        <div class="dropdown-container list-mode">
                                            <?php foreach ($transition->getWorkflow()->getSteps() as $workflow_step): ?>
                                                <input type="radio" class="fancycheckbox" id="workflow_transition_step_<?= $transition->getId(); ?>_outgoing_step" name="outgoing_step_id" value="<?= $workflow_step->getId(); ?>" <?php if ($transition->getOutgoingStep()->getID() == $workflow_step->getID()) echo ' checked'; ?>>
                                                <label for="workflow_transition_step_<?= $transition->getId(); ?>_outgoing_step" class="list-item">
                                                    <span class="name value">
                                                        <?php if ($workflow_step->getLinkedStatus() instanceof \pachno\core\entities\Status): ?>
                                                            <span class="status-badge" style="background-color: <?php echo $workflow_step->getLinkedStatus()->getColor(); ?>;">
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
            </form>
            <div class="form-row header">
                <h3><?= __('Validation rules'); ?></h3>
            </div>
            <div class="column">
                <div class="form-row">
                    <h5>
                        <span class="name"><?php echo __('Before transitioning'); ?></span>
                        <a href="javascript:void(0);" class="button dropper">Add rule</a>
                        <ul class="simple-list rounded_box white shadowed popup_box more_actions_dropdown" onclick="$(this).previous().toggleClassName('button-pressed');$(this).toggle();" id="add_pre_validation_rule">
                            <?php foreach (WorkflowTransitionValidationRule::getAvailablePreValidationRules() as $key => $description): ?>
                                <li <?php if ($transition->hasPreValidationRule($key)) echo ' style="display: none;"'; ?> id="add_workflowtransitionprevalidationrule_<?php echo $key; ?>">
                                    <a href="javascript:void(0);" onclick="Pachno.Config.Workflows.Transition.Validations.add('<?php echo make_url('configure_workflow_transition_validation_rule_post', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'postorpre' => 'pre', 'rule' => $key)); ?>', 'pre', '<?php echo $key; ?>');"><?php echo $description; ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </h5>
                    <div id="pre_validation_tab_pane">
                        <?php if ($transition !== $workflow->getInitialTransition()): ?>
                            <div class="content" style="padding: 5px 0 10px 2px;">
                                <?php echo __('The following validation rules has to be fullfilled for the transition to be available to the user'); ?>
                            </div>
                            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                <tbody class="hover_highlight" id="workflowtransitionprevalidationrules_list">
                                <?php foreach ($transition->getPreValidationRules() as $rule): ?>
                                    <?php include_component('configuration/workflowtransitionvalidationrule', array('rule' => $rule)); ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <span class="faded_out" id="no_workflowtransitionprevalidationrules"<?php if ($transition->hasPreValidationRules()): ?> style="display: none;"<?php endif; ?>><?php echo __('This transition has no pre-validation rules'); ?></span>
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
                        <a href="javascript:void(0);" class="button dropper">Add rule</a>
                        <ul class="simple-list rounded_box white shadowed popup_box more_actions_dropdown" onclick="$(this).previous().toggleClassName('button-pressed');$(this).toggle();" id="add_post_validation_rule">
                            <?php foreach (WorkflowTransitionValidationRule::getAvailablePostValidationRules() as $key => $description): ?>
                                <li <?php if ($transition->hasPostValidationRule($key)) echo ' style="display: none;"'; ?> id="add_workflowtransitionpostvalidationrule_<?php echo $key; ?>"><a href="javascript:void(0);" onclick="Pachno.Config.Workflows.Transition.Validations.add('<?php echo make_url('configure_workflow_transition_validation_rule_post', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'postorpre' => 'post', 'rule' => $key)); ?>', 'post', '<?php echo $key; ?>');"><?php echo $description; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </h5>
                    <div class="content" style="padding: 5px 0 10px 2px;">
                        <?php echo __('The following validation rules will be applied to the input given by the user in the transition view. If the validation fails, the transition will not take place.'); ?>
                    </div>
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <tbody class="hover_highlight" id="workflowtransitionpostvalidationrules_list">
                        <?php foreach ($transition->getPostValidationRules() as $rule): ?>
                            <?php include_component('configuration/workflowtransitionvalidationrule', array('rule' => $rule)); ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <span class="faded_out" id="no_workflowtransitionpostvalidationrules"<?php if ($transition->hasPostValidationRules()): ?> style="display: none;"<?php endif; ?>><?php echo __('This transition has no post validation rules'); ?></span>
                </div>
            </div>
            <div class="form-row header">
                <h3>
                    <span class="name"><?= __('Actions applied during this transition'); ?></span>
                    <a href="javascript:void(0);" class="button dropper">Add transition action</a>
                    <div class="rounded_box white shadowed popup_box more_actions_dropdown" onclick="$(this).previous().toggleClassName('button-pressed');$(this).toggle();" id="add_post_action">
                        <div class="column">
                            <h1><?php echo __('Set issue fields'); ?></h1>
                            <ul class="simple-list">
                                <?php foreach (WorkflowTransitionAction::getAvailableTransitionActions('set') as $key => $description): ?>
                                    <li <?php if ($transition->hasAction($key)) echo ' style="display: none;"'; ?> id="add_workflowtransitionaction_<?php echo $key; ?>"><a href="javascript:void(0);" onclick="Pachno.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_action_post', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => $key)); ?>', '<?php echo $key; ?>');" title="<?php echo $description; ?>"><?php echo $description; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="column">
                            <h1><?php echo __('Clear issue fields'); ?></h1>
                            <ul class="simple-list">
                                <?php foreach (WorkflowTransitionAction::getAvailableTransitionActions('clear') as $key => $description): ?>
                                    <li <?php if ($transition->hasAction($key)) echo ' style="display: none;"'; ?> id="add_workflowtransitionaction_<?php echo $key; ?>"><a href="javascript:void(0);" onclick="Pachno.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_action_post', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => $key)); ?>', '<?php echo $key; ?>');" title="<?php echo $description; ?>"><?php echo $description; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="column">
                            <h1><?php echo __('Special actions'); ?></h1>
                            <ul class="simple-list">
                                <?php foreach (WorkflowTransitionAction::getAvailableTransitionActions('special') as $key => $description): ?>
                                    <li <?php if ($transition->hasAction($key)) echo ' style="display: none;"'; ?> id="add_workflowtransitionaction_<?php echo $key; ?>"><a href="javascript:void(0);" onclick="Pachno.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_action_post', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => $key)); ?>', '<?php echo $key; ?>');" title="<?php echo $description; ?>"><?php echo $description; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </h3>
            </div>
            <div class="form-row">
                <div class="content" style="padding: 5px 0 10px 2px;">
                    <?php echo __('The following actions will be applied to the issue during this transition.'); ?>
                </div>
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <tbody class="hover_highlight" id="workflowtransitionactions_list">
                    <?php if ($transition->hasTemplate()): ?>
                        <tr>
                            <td colspan="2"><?php echo __('Add a comment if one is specified'); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($transition->getActions() as $action): ?>
                        <?php include_component('configuration/workflowtransitionaction', array('action' => $action)); ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <span class="faded_out" id="no_workflowtransitionactions"<?php if ($transition->hasActions() || $transition->hasTemplate()): ?> style="display: none;"<?php endif; ?>><?php echo __('This transition has no actions'); ?></span>
            </div>
        </div>
    </div>
</div>
