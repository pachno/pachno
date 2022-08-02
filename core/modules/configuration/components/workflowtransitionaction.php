<?php

    use pachno\core\entities\Build;
use pachno\core\entities\common\Identifiable;
use pachno\core\entities\Component;
    use pachno\core\entities\CustomDatatype;
use pachno\core\entities\DatatypeBase;
use pachno\core\entities\Milestone;
    use pachno\core\entities\Team;
    use pachno\core\entities\User;
    use pachno\core\entities\WorkflowTransitionAction;

    /**
     * @var WorkflowTransitionAction $action
     * @var User[] $available_assignees_users
     * @var Team[] $available_assignees_teams
     */

?>
<div id="workflowtransitionaction_<?= $action->getID(); ?>" class="configurable-component" data-workflow-transition-action data-id="<?= $action->getId(); ?>">
    <div class="row">
        <?php if (!$action->hasEdit()): ?>
            <div class="name"><?= $action->getDescription(); ?></div>
        <?php elseif ($action->isCustomSetAction() && in_array($action->getCustomField()->getType(), [DatatypeBase::USER_CHOICE, DatatypeBase::TEAM_CHOICE, DatatypeBase::CLIENT_CHOICE])): ?>
            <div class="name"><?= $action->getDescription(); ?></div>
            <div class="icon">
                <button class="dropper icon secondary"><?= fa_image_tag('user-edit'); ?></button>
                <?php if ($action->getCustomField()->getType() == DatatypeBase::USER_CHOICE): ?>
                    <?php include_component('main/identifiableselector', [
                        'html_id'        => 'workflowtransitionaction_'. $action->getID().'_edit',
                        'header'          => __('Select a user'),
                        'callback' => "Pachno.Config.Workflows.Transition.Actions.update('". make_url('configure_workflow_transition_action', ['workflow_id' => $action->getWorkflow()->getID(), 'transition_id' => $action->getTransition()->getID(), 'action_id' => $action->getID()]) ."?target_value=%identifiable_value', '". $action->getID() ."')",
                        'clear_link_text' => __('Clear currently selected user'),
                        'base_id'         => 'workflowtransitionaction_'. $action->getID(),
                        'include_users'   => true,
                        'include_teams'   => false,
                        'include_clients' => false,
                        'absolute'        => true,
                        'hidden'          => false,
                        'classes'         => 'leftie popup_box more_actions_dropdown'
                    ]); ?>
                <?php elseif ($action->getCustomField()->getType() == DatatypeBase::TEAM_CHOICE): ?>
                    <?php include_component('main/identifiableselector', [
                        'html_id'        => 'workflowtransitionaction_'. $action->getID().'_edit',
                        'header'          => __('Select a team'),
                        'callback' => "Pachno.Config.Workflows.Transition.Actions.update('". make_url('configure_workflow_transition_action', ['workflow_id' => $action->getWorkflow()->getID(), 'transition_id' => $action->getTransition()->getID(), 'action_id' => $action->getID()]) ."?target_value=%identifiable_value', '". $action->getID() ."')",
                        'clear_link_text' => __('Clear currently selected team'),
                        'base_id'         => 'workflowtransitionaction_'. $action->getID(),
                        'include_users'   => false,
                        'include_teams'   => true,
                        'include_clients' => false,
                        'absolute'        => true,
                        'hidden'          => false,
                        'classes'         => 'leftie popup_box more_actions_dropdown'
                    ]); ?>
                <?php elseif ($action->getCustomField()->getType() == DatatypeBase::CLIENT_CHOICE): ?>
                    <?php include_component('main/identifiableselector', [
                        'html_id'        => 'workflowtransitionaction_'. $action->getID().'_edit',
                        'header'          => __('Select a client'),
                        'callback' => "Pachno.Config.Workflows.Transition.Actions.update('". make_url('configure_workflow_transition_action', ['workflow_id' => $action->getWorkflow()->getID(), 'transition_id' => $action->getTransition()->getID(), 'action_id' => $action->getID()]) ."?target_value=%identifiable_value', '". $action->getID() ."')",
                        'clear_link_text' => __('Clear currently selected client'),
                        'base_id'         => 'workflowtransitionaction_'. $action->getID(),
                        'include_users'   => false,
                        'include_teams'   => false,
                        'include_clients' => true,
                        'absolute'        => true,
                        'hidden'          => false,
                        'classes'         => 'leftie popup_box more_actions_dropdown'
                    ]); ?>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="name with-dropdown">
                <form action="<?= make_url('configure_workflow_transition_action', ['workflow_id' => $action->getWorkflow()->getID(), 'transition_id' => $action->getTransition()->getID(), 'action_id' => $action->getID()]); ?>" onsubmit="Pachno.Config.Workflows.Transition.Actions.update('<?= make_url('configure_workflow_transition_action', ['workflow_id' => $action->getWorkflow()->getID(), 'transition_id' => $action->getTransition()->getID(), 'action_id' => $action->getID()]); ?>', <?= $action->getID(); ?>);return false;" id="workflowtransitionaction_<?= $action->getID(); ?>_form" data-interactive-form data-simple-submit>
                    <input type="hidden" name="action_type" value="<?= $action->getActionType(); ?>">
                    <div class="form-row">
                        <?php if ($action->hasOptions()): ?>
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown invisible <?php if (!$action->getTargetValue()) echo 'active'; ?>">
                                    <label>
                                        <?php if ($action->getActionType() == WorkflowTransitionAction::ACTION_SET_STATUS): ?>
                                            <?= __('Set status to %status', ['%status' => '']); ?>
                                        <?php elseif ($action->getActionType() == WorkflowTransitionAction::ACTION_SET_PRIORITY): ?>
                                            <?= __('Set priority to %priority', ['%priority' => '']); ?>
                                        <?php elseif ($action->getActionType() == WorkflowTransitionAction::ACTION_SET_SEVERITY): ?>
                                            <?= __('Set severity to %severity', ['%severity' => '']); ?>
                                        <?php elseif ($action->getActionType() == WorkflowTransitionAction::ACTION_SET_CATEGORY): ?>
                                            <?= __('Set category to %category', ['%category' => '']); ?>
                                        <?php elseif ($action->getActionType() == WorkflowTransitionAction::ACTION_SET_PERCENT): ?>
                                            <?= __('Set percent completed to %percentcompleted', ['%percentcompleted' => '']); ?>
                                        <?php elseif ($action->getActionType() == WorkflowTransitionAction::ACTION_SET_RESOLUTION): ?>
                                            <?= __('Set resolution to %resolution', ['%resolution' => '']); ?>
                                        <?php elseif ($action->getActionType() == WorkflowTransitionAction::ACTION_SET_REPRODUCABILITY): ?>
                                            <?= __('Set reproducability to %reproducability', ['%reproducability' => '']); ?>
                                        <?php elseif ($action->getActionType() == WorkflowTransitionAction::ACTION_ASSIGN_ISSUE): ?>
                                            <?= __('Assign issue to %user', ['%user' => '']); ?>
                                        <?php elseif ($action->isCustomSetAction()): ?>
                                            <?= __('Set issue field %key to %value', ['%key' => $action->getCustomActionType(), '%value' => '']); ?>
                                        <?php endif; ?>
                                        <span class="submit-indicator"><?= fa_image_tag('spinner', ['class' => 'fa-spin']); ?></span>
                                    </label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode from-bottom">
                                        <input type="radio" name="target_value" class="fancy-checkbox" id="edit-transition-action-<?= $action->getId(); ?>-0" value="0" <?php if ($action->getTargetValue() == 0) echo ' checked'; ?>>
                                        <label for="edit-transition-action-<?= $action->getId(); ?>-0" class="list-item">
                                            <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                            <span class="name value">
                                                <?php if ($action->getActionType() == WorkflowTransitionAction::ACTION_ASSIGN_ISSUE): ?>
                                                    <?= __('User or team specified during transition'); ?>
                                                <?php else: ?>
                                                    <?= __('Value provided by user'); ?>
                                                <?php endif; ?>
                                            </span>
                                        </label>
                                        <div class="list-item separator"></div>
                                        <?php if ($action->getActionType() == WorkflowTransitionAction::ACTION_ASSIGN_ISSUE): ?>
                                            <div class="list-item header"><?= __('Available users'); ?></div>
                                            <?php foreach ($available_assignees_users as $option): ?>
                                                <input type="radio" name="target_value" class="fancy-checkbox" id="edit-transition-action-user-<?= $action->getId(); ?>-<?= $option->getId() ?>" value="<?= $option->getId() ?>" <?php if (isset($target_details) && (int) $target_details[1] == $option->getID()) echo ' checked'; ?>>
                                                <label for="edit-transition-action-user-<?= $action->getId(); ?>-<?= $option->getId() ?>" class="list-item">
                                                    <span class="icon"><?= fa_image_tag('user'); ?></span>
                                                    <span class="name value"><?= $option->getNameWithUsername(); ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                            <div class="list-item header"><?= __('Available teams'); ?></div>
                                            <?php foreach ($available_assignees_teams as $option): ?>
                                                <input type="radio" name="target_value" class="fancy-checkbox" id="edit-transition-action-team-<?= $action->getId(); ?>-<?= $option->getId() ?>" value="<?= $option->getId() ?>" <?php if (isset($target_details) && (int) $target_details[1] == $option->getID()) echo ' checked'; ?>>
                                                <label for="edit-transition-action-team-<?= $action->getId(); ?>-<?= $option->getId() ?>" class="list-item">
                                                    <span class="icon"><?= fa_image_tag('users'); ?></span>
                                                    <span class="name value"><?= $option->getNameWithUsername(); ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <?php foreach ($action->getOptions() as $option): ?>
                                                <?php $value = ($option instanceof Identifiable) ? $option->getID() : $option; ?>
                                                <input type="radio" name="target_value" class="fancy-checkbox" id="edit-transition-action-<?= $action->getId(); ?>-<?= $value ?>" value="<?= $value ?>" <?php if ($action->getTargetValue() == $value) echo ' checked'; ?>>
                                                <label for="edit-transition-action-<?= $action->getId(); ?>-<?= $value ?>" class="list-item">
                                                    <?php if ($option instanceof User): ?>
                                                        <span class="icon"><?= fa_image_tag('user'); ?></span>
                                                        <span class="name value"><?= $option->getNameWithUsername(); ?></span>
                                                    <?php elseif ($option instanceof Milestone || $option instanceof Build || $option instanceof Component): ?>
                                                        <span class="icon"><?= fa_image_tag('boxes'); ?></span>
                                                        <span class="name value"><?= $option->getProject()->getName() . ' - ' . $option->getName(); ?></span>
                                                    <?php elseif ($option instanceof Identifiable): ?>
                                                        <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                                        <span class="name value"><?= $option->getName(); ?></span>
                                                    <?php else: ?>
                                                        <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                                        <span class="name value"><?= $option; ?></span>
                                                    <?php endif; ?>
                                                </label>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php elseif ($action->getCustomActionType()): ?>
                            <span><?= __('Set issue field %key to %value', array('%key' => $action->getCustomActionType(), '%value' => '')); ?></span>
                            <?php switch (CustomDatatype::getByKey($action->getCustomActionType())->getType()) {
                                case DatatypeBase::INPUT_TEXTAREA_MAIN:
                                case DatatypeBase::INPUT_TEXTAREA_SMALL:
                                    include_component('main/textarea', array('area_name' => 'target_value', 'target_type' => 'workflowtransitionaction', 'target_id' => $action->getID(), 'area_id' => 'workflowtransitionaction_'. $action->getID() .'_value', 'class' => 'inline', 'value' => $action->getTargetValue()));
                                    break;
                                case DatatypeBase::DATE_PICKER:
                                case DatatypeBase::DATETIME_PICKER: ?>
                                <input type="hidden" id="workflowtransitionaction_<?= $action->getID(); ?>_value_1" name="target_value" value="<?= ($action->getTargetValue() ? date('Y-m-d' . (CustomDatatype::getByKey($action->getCustomActionType())->getType() == DatatypeBase::DATETIME_PICKER ? ' H:i' : ''), $action->getTargetValue()) : ''); ?>">
                                    <div id="customfield_<?= 'workflowtransitionaction_'. $action->getID(); ?>_calendar_container"></div>
                                    <script type="text/javascript">
                                        Calendar.setup({
                                            dateField: "<?= 'workflowtransitionaction_'. $action->getID(); ?>_value_1",
                                            parentElement: "customfield_<?= 'workflowtransitionaction_'. $action->getID(); ?>_calendar_container"
                                        });
                                    </script>
                                <?php
                                break;
                                case DatatypeBase::INPUT_TEXT:
                                case DatatypeBase::CALCULATED_FIELD: ?>
                                <input type="text" id="workflowtransitionaction_<?= $action->getID(); ?>_value_1" name="target_value" value="<?= ($action->getTargetValue() ?: ''); ?>" class="inline">
                                    <?php
                                    break;
                            } ?>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        <div class="icon">
            <button class="secondary icon" onclick="Pachno.UI.Dialog.show('<?= __('Do you really want to delete this transition action?'); ?>', '<?= __('Please confirm that you really want to delete this transition action.'); ?>', {yes: {click: function() {Pachno.trigger(Pachno.EVENTS.configuration.deleteComponent, { url: '<?= make_url('configure_workflow_transition_action', ['workflow_id' => $action->getWorkflow()->getID(), 'transition_id' => $action->getTransition()->getID()]); ?>?action_id=<?= $action->getId(); ?>', type: 'workflow-transition-action', id: <?= $action->getID(); ?>, close_container: false })}}, no: { click: Pachno.UI.Dialog.dismiss }});"><?= fa_image_tag('trash-alt'); ?></button>
        </div>
    </div>
</div>
