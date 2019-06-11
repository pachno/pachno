<?php if (isset($customtype)): ?>
    <div class="form-container">
        <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuefields_update_customtype', array('type' => $type)); ?>" onsubmit="Pachno.Config.Issuefields.Custom.update('<?php echo make_url('configure_issuefields_update_customtype', array('type' => $type)); ?>', '<?php echo $type; ?>');return false;" id="edit_custom_type_<?php echo $type; ?>_form">
            <div class="form-row">
                <input type="text" name="name" id="custom_type_<?php echo $type; ?>_name" value="<?php echo $customtype->getName(); ?>" class="invisible title">
                <label for="custom_type_<?php echo $type; ?>_name"><?php echo __('Name'); ?></label>
            </div>
            <div class="form-row">
                <input type="text" name="description" id="custom_type_<?php echo $type; ?>_description" value="<?php echo $customtype->getDescription(); ?>" class="invisible">
                <label for="custom_type_<?php echo $type; ?>_description"><?php echo __('Label'); ?></label>
            </div>
            <div class="form-row">
                <textarea name="instructions" id="custom_type_<?php echo $type; ?>_instructions" class="invisible" placeholder="<?= __('Click here to add instructions visible to users when editing this field'); ?>"><?php echo $customtype->getInstructions(); ?></textarea>
                <label for="custom_type_<?php echo $type; ?>_instructions"><?php echo __('Instructions'); ?></label>
            </div>
            <div class="form-row error-container">
                <div class="error"></div>
                <?= fa_image_tag('spinner', ['class' => 'fa-spin submit-indicator icon']); ?>
            </div>
        </form>
    </div>
<?php endif; ?>
<?php if ($showitems): ?>
    <?php if (isset($customtype)): ?>
        <?php if ($customtype->getType() == \pachno\core\entities\CustomDatatype::CALCULATED_FIELD): ?>
            <div class="header_div" style="margin-top: 15px;">
                <?php echo __('Formula'); ?>
            </div>
            <p><?php echo __('To use a custom field in the formula, enter the field key (displayed in light gray text next to the name) between curly braces.'); ?></p>
            <p><?php echo __('Example: ({myfield}+{otherfield})/({thirdfield}*2)'); ?></p>
            <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuefields_add', array('type' => $type)); ?>" onsubmit="Pachno.Config.Issuefields.Options.add('<?php echo make_url('configure_issuefields_add', array('type' => $type)); ?>', '<?php echo $type; ?>');return false;" id="add_<?php echo $type; ?>_form">
                <label for="add_option_<?php echo $type; ?>_itemdata"><?php echo __('Value'); ?></label>
                <input type="hidden" id="add_option_<?php echo $type; ?>_name" name="name" value="Formula">
                <?php $value = (!empty($items) ? array_pop($items)->getValue() : ''); ?>
                <input type="text" id="add_option_<?php echo $type; ?>_itemdata" name="value" value="<?php echo $value ?>" style="width: 400px;">
                <input type="submit" value="<?php echo __('Save'); ?>" style="margin-right: 5px; font-weight: bold;">
                <?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'add_' . $type . '_indicator')); ?>
            </form>
        <?php endif; ?>
    <?php else: ?>
        <h5>
            <span><?php echo __('Existing choices'); ?></span>
            <?php echo fa_image_tag('spinner', ['class' => 'fa-spin indicator', 'id' => $type . '_sort_indicator']); ?>
        </h5>
        <div class="configurable-components-list" id="field-options-list">
            <?php foreach ($items as $item): ?>
                <?php include_component('editissuefieldoption', array('item' => $item, 'type' => $type, 'access_level' => $access_level)); ?>
            <?php endforeach; ?>
        </div>
        <div class="form-container">
            <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuefields_add', array('type' => $type)); ?>" onsubmit="Pachno.Config.Issuefields.Options.add(this);return false;">
                <div class="form-row add-placeholder">
                    <?= fa_image_tag('plus', ['class' => 'icon']); ?>
                    <input type="text" name="name" class="invisible" placeholder="<?= __('Add a choice'); ?>" onblur="this.submit();">
                </div>
                <div class="form-row error-container">
                    <div class="error"></div>
                </div>
            </form>
        </div>
        <script type="text/javascript">
            sortable_options = {constraint: '', onUpdate: function(container) { Pachno.Config.Issuefields.saveOrder(container, '<?php echo $type; ?>', '<?php echo make_url('configure_issuefields_saveorder', array('type' => $type)); ?>'); }};
            Sortable.create('<?php echo $type; ?>_list', sortable_options);
        </script>
    <?php endif; ?>
<?php endif; ?>
