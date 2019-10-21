<?php

/**
 * @var \pachno\core\entities\Component $component
 */

?>
<div id="component_<?php print $component->getID(); ?>" class="row" data-component-id="<?= $component->getId(); ?>">
    <div class="column name-container">
        <div class="form-container">
            <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_project_component_post', ['project_id' => $component->getProject()->getId(), 'component_id' => $component->getID()]); ?>" onsubmit="Pachno.Config.Workflows.Transition.save(this);return false;" data-interactive-form>
                <div class="form-row">
                    <?php echo fa_image_tag('puzzle-piece', ['class' => 'icon']); ?>
                    <input type="text" name="name" value="<?= $component->getName(); ?>" class="invisible" id="component_<?= $component->getID(); ?>_input">
                    <label for="component_<?= $component->getID(); ?>_input"><?= __('Component name'); ?></label>
                </div>
            </form>
        </div>
    </div>
    <div class="column actions">
        <div class="dropper-container">
            <button class="dropper button secondary">
                <span><?= __('Actions'); ?></span>
                <?= fa_image_tag('angle-down', ['class' => 'icon']); ?>
            </button>
            <div class="dropdown-container">
                <div class="list-mode">
                    <a class="list-item" href="javascript:void(0);" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_component', 'component_id' => $component->getId()]); ?>');">
                        <span class="icon"><?= fa_image_tag('edit'); ?></span>
                        <span class="name"><?= __('Edit component'); ?></span>
                    </a>
                    <a href="javascript:void(0);" onclick="$('component_<?php echo $component->getID(); ?>_permissions').toggle();" class="list-item">
                        <span class="icon"><?= fa_image_tag('lock'); ?></span>
                        <span class="name"><?= __('Configure permissions'); ?></span>
                    </a>
                    <div class="separator"></div>
                    <a class="list-item danger" href="javascript:void(0);" onclick="Pachno.Main.Helpers.Dialog.show('<?php echo __('Do you really want to delete this component?'); ?>', '<?php echo __('Please confirm that you want to completely remove this component.'); ?>', {yes: {click: function() { Pachno.Project.Component.remove('<?= make_url('configure_delete_component', array('project_id' => $component->getProject()->getID(), 'component_id' => $component->getID())); ?>', <?= $component->getID(); ?>);}}, no: { click: Pachno.Main.Helpers.Dialog.dismiss }});">
                        <span class="icon"><?php echo fa_image_tag('times'); ?></span>
                        <span class="name"><?= __('Delete component'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="component_<?php echo $component->getID(); ?>_permissions" style="display: none;" class="backdrop_box large">
    <div class="backdrop_detail_header">
        <span><?php echo __('Permission details for "%itemname"', array('%itemname' => $component->getName())); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="$('component_<?php echo $component->getID(); ?>_permissions').toggle();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?php echo __('Specify who can access this component.'); ?>
        <?php include_component('configuration/permissionsinfo', array('key' => 'canseecomponent', 'mode' => 'project_hierarchy', 'target_id' => $component->getID(), 'module' => 'core', 'access_level' => $access_level)); ?>
    </div>
</div>
