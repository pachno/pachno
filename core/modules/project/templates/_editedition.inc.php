<?php

/**
 * @var \pachno\core\entities\Edition $edition
 */

?>
<div class="form-container">
    <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_project_edition_post', ['project_id' => $edition->getProject()->getId(), 'edition_id' => $edition->getID()]); ?>" onsubmit="Pachno.Project.Edition.save(this);return false;">
        <div class="form-row">
            <input type="text" name="name" id="edition_<?= $edition->getID(); ?>_name_input" value="<?= $edition->getName(); ?>" class="invisible title with-label" placeholder="<?= __('Enter a name for this edition'); ?>">
            <label for="edition_<?= $edition->getID(); ?>_name_input"><?= __('Name'); ?></label>
        </div>
        <div class="form-row">
            <input type="text" name="description" id="edition_<?= $edition->getID(); ?>_description_input" class="invisible with-label" placeholder="<?= __('Click to add description for this edition'); ?>" value="<?= __e($edition->getDescription()); ?>">
            <label for="edition_<?= $edition->getID(); ?>_description_input"><?= __('Description (optional)'); ?></label>
        </div>
        <div class="form-row">
            <input type="url" name="documentation_url" id="edition_<?= $edition->getID(); ?>_documentation_url_input" value="<?= $edition->getDocumentationURL(); ?>" class="invisible title with-label" placeholder="<?= __('Enter a url for this edition (optional)'); ?>">
            <label for="edition_<?= $edition->getID(); ?>_documentation_url_input"><?= __('Url (optional)'); ?></label>
        </div>
        <div class="form-row error-container">
            <div class="error"></div>
            <?= fa_image_tag('spinner', ['class' => 'fa-spin submit-indicator icon']); ?>
        </div>
        <h5>
            <span><?= __('Edition components'); ?></span>
        </h5>
        <div class="configurable-components-list" id="edition-components-list">
            <?php foreach ($edition->getProject()->getComponents() as $component): ?>
                <?php include_component('project/editioncomponent', array('component' => $component, 'edition' => $edition)); ?>
            <?php endforeach; ?>
        </div>
        <div class="form-row submit-container">
            <button type="submit" class="button primary">
                <span class="name"><?= __('Save'); ?></span>
                <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
            </button>
        </div>
    </form>
    <div class="separator"></div>
    <div class="form-row submit-container">
        <button class="secondary danger" onclick="Pachno.Main.Helpers.Dialog.show('<?php echo __('Do you really want to delete this edition?'); ?>', '<?php echo __('Please confirm that you want to completely remove this edition.'); ?>', {yes: {click: function() { Pachno.Project.Edition.remove('<?= make_url('configure_project_edition_delete', array('project_id' => $edition->getProject()->getID(), 'edition_id' => $edition->getID())); ?>', <?= $edition->getID(); ?>);}}, no: { click: Pachno.Main.Helpers.Dialog.dismiss }});">
            <span class="icon"><?= fa_image_tag('trash-alt', [], 'far'); ?></span>
            <span class="name"><?= __('Remove this edition'); ?></span>
        </button>
    </div>
</div>
