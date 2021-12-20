<?php

    /** @var \pachno\core\entities\DatatypeBase $item */

?>
<div data-issue-field-option data-id="<?php echo $item->getID(); ?>" class="configurable-component issue-field-option form-container">
    <form class="row" accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuefields_edit', ['type' => $item->getItemType(), 'id' => $item->getID()]); ?>" data-interactive-form id="edit_issue_field_<?= $item->getID(); ?>_form">
        <?= fa_image_tag('grip-vertical', ['class' => 'icon handle']); ?>
        <?php if ($item->getItemtype() == \pachno\core\entities\Datatype::PRIORITY): ?>
            <div class="icon">
                <div class="dropper-container">
                    <button class="button icon secondary dropper"><?= fa_image_tag($item->getFontAwesomeIcon(), [], $item->getFontAwesomeIconStyle()); ?></button>
                    <div class="dropdown-container">
                        <div class="list-mode grid-mode">
                            <?php foreach (\pachno\core\entities\Priority::getAvailableValues() as $value => $icon): ?>
                                <input type="radio" name="itemdata" value="<?= $value; ?>" id="<?= $item->getItemType(); ?>_<?= $item->getID(); ?>_itemdata_input_<?= $value; ?>" class="fancy-checkbox" <?php if ($item->getItemdata() == $value) echo 'checked'; ?>>
                                <label class="list-item" for="<?= $item->getItemType(); ?>_<?= $item->getID(); ?>_itemdata_input_<?= $value; ?>">
                                    <span class="icon"><?= fa_image_tag($icon); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="name">
            <div class="form-row">
                <input type="text" name="name" id="<?php echo $item->getItemType(); ?>_<?php echo $item->getID(); ?>_name_input" value="<?php echo $item->getName(); ?>" class="invisible">
                <label for="<?php echo $item->getItemType(); ?>_<?php echo $item->getID(); ?>_name_input"><?= __('Field value'); ?></label>
            </div>
        </div>
        <div class="icon">
            <?php if ($item->canBeDeleted()): ?>
                <a href="javascript:void(0);" class="button secondary icon" onclick="Pachno.UI.Dialog.show('<?php echo __('Really delete this item?'); ?>', '<?php echo __('Are you really sure you want to delete this item?'); ?>', {yes: {click: function() {Pachno.Config.Issuefields.Options.remove('<?php echo make_url('configure_issuefields_delete', array('type' => $item->getItemType(), 'id' => $item->getID())); ?>', <?php echo $item->getID(); ?>); Pachno.UI.Dialog.dismiss(); }}, no: {click: Pachno.UI.Dialog.dismiss}});"><?php echo fa_image_tag('trash-alt', [], 'far'); ?></a>
            <?php else: ?>
                <a href="javascript:void(0);" class="button secondary icon disabled" onclick="Pachno.UI.Message.error('<?php echo __('This item cannot be deleted'); ?>', '<?php echo __('Other items - such as workflow steps - may depend on this item to exist. Remove the dependant item or unlink it from this item to continue.'); ?>');" id="delete_<?php echo $item->getID(); ?>_link"><?php echo fa_image_tag('trash-alt', [], 'far'); ?></a>
            <?php endif; ?>
        </div>
            <?php /*
        <div id="item_option_<?php echo $item->getItemType(); ?>_<?php echo $item->getID(); ?>_content">
            <?php if (in_array($item->getItemType(), array('status', 'category'))): ?>
                <div style="border: 0; background-color: <?php echo $item->getItemdata(); ?>; font-size: 1px; width: 16px; border: 1px solid rgba(0, 0, 0, 0.2); height: 16px; margin-right: 2px; float: left;" id="<?php echo $item->getItemType(); ?>_<?php echo $item->getID(); ?>_itemdata">&nbsp;</div>
            <?php endif; ?>
            <?php if (!$item->isBuiltin()): ?>
                <div style="width: 50px; display: inline;" id="<?php echo $item->getItemType(); ?>_<?php echo $item->getID(); ?>_value"><?php echo $item->getValue(); ?></div>
            <?php endif; ?>
            <div style="padding: 2px; font-size: 12px; display: inline;" id="<?php echo $item->getItemType(); ?>_<?php echo $item->getID(); ?>_name"><?php echo $item->getName(); ?></div>
            <div class="button-group" style="float: right;">
                <a href="javascript:void(0);" class="button button-icon" onclick="$('#item_option_<?php echo $item->getItemType(); ?>_<?php echo $item->getID(); ?>_content').hide();$('#edit_item_option_<?php echo $item->getID(); ?>').show();$('#<?php echo $item->getItemType(); ?>_<?php echo $item->getID(); ?>_name_input').focus();" title="<?php echo __('Edit this item'); ?>"><?php echo fa_image_tag('edit'); ?></a>
                <a href="javascript:void(0);" class="button button-icon" onclick="Pachno.UI.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issuefield_permissions', 'item_id' => $item->getID(), 'item_key' => $item->getPermissionsKey(), 'access_level' => $access_level)); ?>');" title="<?php echo __('Set permissions for this item'); ?>"><?php echo fa_image_tag('user-lock'); ?></a>
                <?php if ($item->canBeDeleted()): ?>
                    <a href="javascript:void(0);" class="button button-icon" onclick="Pachno.UI.Dialog.show('<?php echo __('Really delete %itemname?', array('%itemname' => $item->getName())); ?>', '<?php echo __('Are you really sure you want to delete this item?'); ?>', {yes: {click: function() {Pachno.Config.Issuefields.Options.remove('<?php echo make_url('configure_issuefields_delete', array('type' => $item->getItemType(), 'id' => $item->getID())); ?>', '<?php echo $item->getItemType(); ?>', <?php echo $item->getID(); ?>); Pachno.UI.Dialog.dismiss(); }}, no: {click: Pachno.UI.Dialog.dismiss}});" id="delete_<?php echo $item->getID(); ?>_link"><?php echo fa_image_tag('trash-alt', [], 'far'); ?></a>
                <?php else: ?>
                    <a href="javascript:void(0);" class="button button-icon disabled" onclick="Pachno.UI.Message.error('<?php echo __('This item cannot be deleted'); ?>', '<?php echo __('Other items - such as workflow steps - may depend on this item to exist. Remove the dependant item or unlink it from this item to continue.'); ?>');" id="delete_<?php echo $item->getID(); ?>_link"><?php echo fa_image_tag('trash-alt', [], 'far'); ?></a>
                <?php endif; ?>
            </div>
            <?php echo image_tag('spinning_16.gif', array('id' => 'delete_' . $item->getItemType() . '_' . $item->getID() . '_indicator', 'style' => 'display: none; float: right; margin: 2px 5px 0 0;')); ?>
        </div>
        <div id="edit_item_option_<?php echo $item->getID(); ?>" style="display: none;">
            <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuefields_edit', array('type' => $item->getItemType(), 'id' => $item->getID())); ?>" onsubmit="Pachno.Config.Issuefields.Options.update('<?php echo make_url('configure_issuefields_edit', array('type' => $item->getItemType(), 'id' => $item->getID())); ?>', '<?php echo $item->getItemType(); ?>', <?php echo $item->getID(); ?>);return false;" id="edit_<?php echo $item->getItemType(); ?>_<?php echo $item->getID(); ?>_form">
                <table style="width: 100%; table-layout: fixed;" cellpadding="0" cellspacing="0">
                    <tr>
                        <?php if (in_array($item->getItemType(), array('status', 'category'))): ?>
                            <td style="font-size: 14px; width: 70px;">
                                <input data-cancel-text="<?php echo __('Cancel'); ?>" data-choose-text="<?php echo __('Select this color'); ?>" type="hidden" class="color" name="itemdata" id="<?php echo $item->getItemType(); ?>_<?php echo $item->getID(); ?>_itemdata_input" value="<?php echo $item->getColor(); ?>">
                            </td>
                        <?php endif; ?>
                        <?php if ($item->getItemType() == 'priority'): ?>
                            <td style="font-size: 14px; width: 220px;">
                                <?php foreach (\pachno\core\entities\Priority::getAvailableValues() as $value => $icon): ?>
                                    <label><input type="radio" name="itemdata" value="<?php echo $value; ?>"<?php if ($item->getItemdata() == $value) echo ' checked'; ?>><?php echo fa_image_tag($icon); ?></label>
                                <?php endforeach; ?>
                            </td>
                        <?php endif; ?>
                        <?php if (!array_key_exists($item->getItemType(), \pachno\core\entities\Datatype::getTypes())): ?>
                            <td style="font-size: 14px; width: 70px;">
                                <input type="text" name="value" id="<?php echo $item->getItemType(); ?>_<?php echo $item->getID(); ?>_value_input" style="width: 45px;" value="<?php echo $item->getValue(); ?>">
                            </td>
                        <?php endif; ?>
                        <td>
                            <input type="text" name="name" id="<?php echo $item->getItemType(); ?>_<?php echo $item->getID(); ?>_name_input" style="width: calc(100% - 20px);" value="<?php echo $item->getName(); ?>">
                        </td>
                        <td style="text-align: right; width: 150px;">
                            <?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'edit_' . $item->getItemType() . '_' . $item->getID() . '_indicator')); ?>
                            <input type="submit" value="<?php echo __('Update'); ?>" style="margin-right: 5px; font-weight: bold;">
                            <?php echo __('%update or %cancel', array('%update' => '', '%cancel' => '<a href="javascript:void(0);" onclick="$(\'item_option_'.$item->getItemType().'_'.$item->getID().'_content\').show();$(\'edit_item_option_'.$item->getID().'\').hide();"><b>' . __('cancel') . '</b></a>')); ?>
                        </td>
                    </tr>
                </table>
            </form>
        </div> */ ?>
    </form>
</div>
