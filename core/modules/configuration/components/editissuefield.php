<?php use pachno\core\entities\DatatypeBase;

if (isset($customtype)): ?>
    <div class="form-container">
        <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_issuefields_update_customtype', array('type' => $type)); ?>" onsubmit="Pachno.Config.Issuefields.Custom.update('<?= make_url('configure_issuefields_update_customtype', array('type' => $type)); ?>', '<?= $type; ?>');return false;" id="edit_custom_type_<?= $type; ?>_form">
            <div class="form-row">
                <input type="text" name="name" id="custom_type_<?= $type; ?>_name" value="<?= $customtype->getName(); ?>" class="invisible title with-label" placeholder="<?= __('Enter a field name'); ?>">
                <label for="custom_type_<?= $type; ?>_name"><?= __('Name'); ?></label>
            </div>
            <div class="form-row">
                <input type="text" name="instructions" id="custom_type_<?= $type; ?>_instructions" class="invisible with-label" placeholder="<?= __('Click to add instructions for user on how to use this field'); ?>" value="<?= __e($customtype->getInstructions()); ?>">
                <label for="custom_type_<?= $type; ?>_instructions"><?= __('Instructions (optional)'); ?></label>
            </div>
            <div class="form-row submit-container">
                <a class="button secondary" href="javascript:void(0);" onclick="Pachno.UI.Dialog.show('<?= __('Do you really want to delete this issue field?'); ?>', '<?=__('This will also remove the value of this issue field from all issues, along with any possible options this field can have.'); ?>', {yes: {click: function() {Pachno.Config.Issuefields.Custom.remove('<?= make_url('configure_issuefields_delete_customtype', ['type' => $type]); ?>', '<?= $type; ?>'); }}, no: { click: Pachno.UI.Dialog.dismiss }});">
                    <span class="icon"><?= fa_image_tag('times'); ?></span>
                    <span class="name"><?= __('Remove field'); ?></span>
                </a>
            </div>
            <div class="form-row error-container">
                <div class="error"></div>
                <?= fa_image_tag('spinner', ['class' => 'fa-spin submit-indicator icon']); ?>
            </div>
        </form>
    </div>
<?php endif; ?>
<?php if (isset($customtype)): ?>
    <?php if ($customtype->getType() == DatatypeBase::CALCULATED_FIELD): ?>
        <div class="header_div" style="margin-top: 15px;">
            <?= __('Formula'); ?>
        </div>
        <p><?= __('To use a custom field in the formula, enter the field key (displayed in light gray text next to the name) between curly braces.'); ?></p>
        <p><?= __('Example: ({myfield}+{otherfield})/({thirdfield}*2)'); ?></p>
        <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_issuefields_add', array('type' => $type)); ?>" onsubmit="Pachno.Config.Issuefields.Options.save('<?= make_url('configure_issuefields_add', array('type' => $type)); ?>', '<?= $type; ?>');return false;" id="add_<?= $type; ?>_form">
            <label for="add_option_<?= $type; ?>_itemdata"><?= __('Value'); ?></label>
            <input type="hidden" id="add_option_<?= $type; ?>_name" name="name" value="Formula">
            <?php $value = (!empty($items) ? array_pop($items)->getValue() : ''); ?>
            <input type="text" id="add_option_<?= $type; ?>_itemdata" name="value" value="<?= $value ?>" style="width: 400px;">
            <input type="submit" value="<?= __('Save'); ?>" style="margin-right: 5px; font-weight: bold;">
            <?= image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'add_' . $type . '_indicator')); ?>
        </form>
    <?php endif; ?>
<?php endif; ?>
<?php if ($showitems): ?>
    <h5>
        <span><?= __('Existing choices'); ?></span>
        <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator', 'id' => $type . '_sort_indicator', 'style' => 'display: none;']); ?>
    </h5>
    <div class="configurable-components-list" id="field-options-list">
        <?php foreach ($items as $item): ?>
            <?php include_component('configuration/editissuefieldoption', array('item' => $item, 'type' => $type, 'access_level' => $access_level)); ?>
        <?php endforeach; ?>
    </div>
    <div class="form-container">
        <form id="add-field-form" accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_issuefields_add', ['type' => $type]); ?>" onsubmit="Pachno.Config.Issuefields.Options.save(this);return false;" data-interactive-form data-update-container="#field-options-list" data-update-insert data-update-insert-form-list>
            <div class="form-row add-placeholder">
                <?= fa_image_tag('plus', ['class' => 'icon']); ?>
                <input type="text" name="name" class="invisible" placeholder="<?= __('Add a choice'); ?>">
            </div>
            <div class="form-row error-container">
                <div class="error"></div>
            </div>
        </form>
    </div>
    <script type="text/javascript">
        //sortable_options = {constraint: '', handle: 'handle', onUpdate: function(container) { Pachno.Config.Issuefields.saveOrder(container, '<?//= $type; ?>//', '<?//= make_url('configure_issuefields_saveorder', ['type' => $type]); ?>//'); }};
        //Sortable.create('field-options-list', sortable_options);
    </script>
<?php endif; ?>
