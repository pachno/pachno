<?php

    /**
     * @var \pachno\core\entities\WorkflowTransition $transition
     * @var \pachno\core\entities\Status[] $statuses
     */

?>
<div data-workflow-transition data-id="<?php echo $transition->getID(); ?>" class="configurable-component workflow-transition form-container">
    <form class="row" accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_workflow_transition_post', ['workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID()]); ?>" onsubmit="Pachno.Config.Workflows.Transition.save(this);return false;" data-interactive-form>
        <?= fa_image_tag('grip-vertical', ['class' => 'icon handle']); ?>
        <div class="name">
            <div class="form-row">
                <input type="text" name="name" id="workflow_transition_step_<?php echo $transition->getID(); ?>_name_input" value="<?php echo $transition->getName(); ?>" class="invisible">
                <label for="workflow_transition_step_<?php echo $transition->getID(); ?>_name_input"><?= __('Transition name'); ?></label>
            </div>
        </div>
        <div class="icon">
            <button class="button secondary" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_workflow_transition', 'transition_id' => $transition->getId(), 'step_id' => $step->getId()]); ?>');return false;">
                <?= fa_image_tag('edit', ['class' => 'icon'], 'far'); ?>
            </button>
            <a href="javascript:void(0);" class="button secondary icon" onclick="Pachno.Main.Helpers.Dialog.show('<?php echo __('Really delete this transition?'); ?>', '<?php echo __('Are you really sure you want to delete this transition?'); ?>', {yes: {click: function() {Pachno.Config.Issuefields.Options.remove('<?php echo make_url('configure_workflow_transition_delete', ['workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID()]); ?>'); }}, no: {click: Pachno.Main.Helpers.Dialog.dismiss}});"><?php echo fa_image_tag('trash-alt', [], 'far'); ?></a>
        </div>
            <?php /*
        <div id="item_option_<?php echo $type; ?>_<?php echo $transition->getID(); ?>_content">
            <?php if (in_array($type, array('status', 'category'))): ?>
                <div style="border: 0; background-color: <?php echo $transition->getItemdata(); ?>; font-size: 1px; width: 16px; border: 1px solid rgba(0, 0, 0, 0.2); height: 16px; margin-right: 2px; float: left;" id="<?php echo $type; ?>_<?php echo $transition->getID(); ?>_itemdata">&nbsp;</div>
            <?php endif; ?>
            <?php if (!$transition->isBuiltin()): ?>
                <div style="width: 50px; display: inline;" id="<?php echo $type; ?>_<?php echo $transition->getID(); ?>_value"><?php echo $transition->getValue(); ?></div>
            <?php endif; ?>
            <div style="padding: 2px; font-size: 12px; display: inline;" id="<?php echo $type; ?>_<?php echo $transition->getID(); ?>_name"><?php echo $transition->getName(); ?></div>
            <div class="button-group" style="float: right;">
                <a href="javascript:void(0);" class="button button-icon" onclick="$('item_option_<?php echo $type; ?>_<?php echo $transition->getID(); ?>_content').hide();$('edit_item_option_<?php echo $transition->getID(); ?>').show();$('<?php echo $type; ?>_<?php echo $transition->getID(); ?>_name_input').focus();" title="<?php echo __('Edit this item'); ?>"><?php echo fa_image_tag('edit'); ?></a>
                <a href="javascript:void(0);" class="button button-icon" onclick="Pachno.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issuefield_permissions', 'item_id' => $transition->getID(), 'item_key' => $transition->getPermissionsKey(), 'access_level' => $access_level)); ?>');" title="<?php echo __('Set permissions for this item'); ?>"><?php echo fa_image_tag('user-lock'); ?></a>
                <?php if ($transition->canBeDeleted()): ?>
                    <a href="javascript:void(0);" class="button button-icon" onclick="Pachno.Main.Helpers.Dialog.show('<?php echo __('Really delete %itemname?', array('%itemname' => $transition->getName())); ?>', '<?php echo __('Are you really sure you want to delete this item?'); ?>', {yes: {click: function() {Pachno.Config.Issuefields.Options.remove('<?php echo make_url('configure_issuefields_delete', array('type' => $type, 'id' => $transition->getID())); ?>', '<?php echo $type; ?>', <?php echo $transition->getID(); ?>); Pachno.Main.Helpers.Dialog.dismiss(); }}, no: {click: Pachno.Main.Helpers.Dialog.dismiss}});" id="delete_<?php echo $transition->getID(); ?>_link"><?php echo fa_image_tag('trash-alt', [], 'far'); ?></a>
                <?php else: ?>
                    <a href="javascript:void(0);" class="button button-icon disabled" onclick="Pachno.Main.Helpers.Message.error('<?php echo __('This item cannot be deleted'); ?>', '<?php echo __('Other items - such as workflow steps - may depend on this item to exist. Remove the dependant item or unlink it from this item to continue.'); ?>');" id="delete_<?php echo $transition->getID(); ?>_link"><?php echo fa_image_tag('trash-alt', [], 'far'); ?></a>
                <?php endif; ?>
            </div>
            <?php echo image_tag('spinning_16.gif', array('id' => 'delete_' . $type . '_' . $transition->getID() . '_indicator', 'style' => 'display: none; float: right; margin: 2px 5px 0 0;')); ?>
        </div>
        <div id="edit_item_option_<?php echo $transition->getID(); ?>" style="display: none;">
            <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuefields_edit', array('type' => $type, 'id' => $transition->getID())); ?>" onsubmit="Pachno.Config.Issuefields.Options.update('<?php echo make_url('configure_issuefields_edit', array('type' => $type, 'id' => $transition->getID())); ?>', '<?php echo $type; ?>', <?php echo $transition->getID(); ?>);return false;" id="edit_<?php echo $type; ?>_<?php echo $transition->getID(); ?>_form">
                <table style="width: 100%; table-layout: fixed;" cellpadding="0" cellspacing="0">
                    <tr>
                        <?php if (in_array($type, array('status', 'category'))): ?>
                            <td style="font-size: 14px; width: 70px;">
                                <input data-cancel-text="<?php echo __('Cancel'); ?>" data-choose-text="<?php echo __('Select this color'); ?>" type="hidden" class="color" name="itemdata" id="<?php echo $type; ?>_<?php echo $transition->getID(); ?>_itemdata_input" value="<?php echo $transition->getColor(); ?>">
                            </td>
                        <?php endif; ?>
                        <?php if ($type == 'priority'): ?>
                            <td style="font-size: 14px; width: 220px;">
                                <?php foreach (\pachno\core\entities\Priority::getAvailableValues() as $value => $icon): ?>
                                    <label><input type="radio" name="itemdata" value="<?php echo $value; ?>"<?php if ($transition->getItemdata() == $value) echo ' checked'; ?>><?php echo fa_image_tag($icon); ?></label>
                                <?php endforeach; ?>
                            </td>
                        <?php endif; ?>
                        <?php if (!array_key_exists($type, \pachno\core\entities\Datatype::getTypes())): ?>
                            <td style="font-size: 14px; width: 70px;">
                                <input type="text" name="value" id="<?php echo $type; ?>_<?php echo $transition->getID(); ?>_value_input" style="width: 45px;" value="<?php echo $transition->getValue(); ?>">
                            </td>
                        <?php endif; ?>
                        <td>
                            <input type="text" name="name" id="<?php echo $type; ?>_<?php echo $transition->getID(); ?>_name_input" style="width: calc(100% - 20px);" value="<?php echo $transition->getName(); ?>">
                        </td>
                        <td style="text-align: right; width: 150px;">
                            <?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'edit_' . $type . '_' . $transition->getID() . '_indicator')); ?>
                            <input type="submit" value="<?php echo __('Update'); ?>" style="margin-right: 5px; font-weight: bold;">
                            <?php echo __('%update or %cancel', array('%update' => '', '%cancel' => '<a href="javascript:void(0);" onclick="$(\'item_option_'.$type.'_'.$transition->getID().'_content\').show();$(\'edit_item_option_'.$transition->getID().'\').hide();"><b>' . __('cancel') . '</b></a>')); ?>
                        </td>
                    </tr>
                </table>
            </form>
        </div> */ ?>
    </form>
    <div class="row">
        <div class="icon">&nbsp;</div>
        <div class="icon"><?= fa_image_tag('arrow-right'); ?></div>
        <div class="name"><?php include_component('configuration/transitionstatusbadges', ['transition' => $transition]); ?></div>
    </div>
</div>
