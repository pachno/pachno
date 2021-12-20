<?php

/**
 * @var \pachno\core\entities\Component $component
 */

?>
<div class="configurable-component form-container project-component" data-component data-id="<?= $component->getId(); ?>">
    <form class="row" id="project-component-form-<?= $component->getID(); ?>" accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_project_component_post', ['project_id' => $component->getProject()->getId(), 'component_id' => $component->getID()]); ?>" data-interactive-form>
        <div class="icon">
            <?php echo fa_image_tag('puzzle-piece'); ?>
        </div>
        <div class="name">
            <div class="form-row">
                <input type="text" name="name" id="project_component_<?php echo $component->getID(); ?>_name_input" value="<?php echo $component->getName(); ?>" class="invisible">
                <label for="project_component_<?php echo $component->getID(); ?>_name_input"><?= __('Component name'); ?></label>
            </div>
        </div>
        <div class="icon">
            <a class="button secondary icon danger" href="javascript:void(0);" onclick="Pachno.UI.Dialog.show('<?php echo __('Do you really want to delete this component?'); ?>', '<?php echo __('Please confirm that you want to completely remove this component.'); ?>', {yes: {click: function() { Pachno.Project.Component.remove('<?= make_url('configure_project_component_delete', array('project_id' => $component->getProject()->getID(), 'component_id' => $component->getID())); ?>', <?= $component->getID(); ?>);}}, no: { click: Pachno.UI.Dialog.dismiss }});"><?php echo fa_image_tag('trash-alt', [], 'far'); ?></a>
        </div>
    </form>
</div>
