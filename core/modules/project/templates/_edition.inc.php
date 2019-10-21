<?php

/**
 * @var \pachno\core\entities\Edition $edition
 */

?>
<div id="edition_<?php echo $edition->getID(); ?>_box" class="row" data-edition-id="<?= $edition->getId(); ?>">
    <div class="column name-container">
        <div class="title"><?php echo $edition->getName(); ?></div>
        <?php if ($edition->hasDescription()): ?>
            <div class="description"><?php print $edition->getDescription(); ?></div>
        <?php endif; ?>
    </div>
    <div class="column actions">
        <div class="dropper-container">
            <button class="dropper button secondary">
                <span><?= __('Actions'); ?></span>
                <?= fa_image_tag('angle-down', ['class' => 'icon']); ?>
            </button>
            <div class="dropdown-container">
                <div class="list-mode">
                    <a class="list-item" href="javascript:void(0);" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_edition', 'edition_id' => $edition->getId()]); ?>');">
                        <span class="icon"><?= fa_image_tag('edit'); ?></span>
                        <span class="name"><?= __('Edit edition'); ?></span>
                    </a>
                    <a href="javascript:void(0);" onclick="$('edition_<?php echo $edition->getID(); ?>_permissions').toggle();" class="list-item">
                        <span class="icon"><?= fa_image_tag('lock'); ?></span>
                        <span class="name"><?= __('Configure permissions'); ?></span>
                    </a>
                    <div class="separator"></div>
                    <a class="list-item danger" href="javascript:void(0);" onclick="Pachno.Main.Helpers.Dialog.show('<?php echo __('Do you really want to delete this edition?'); ?>', '<?php echo __('Please confirm that you want to completely remove this edition.'); ?>', {yes: {click: function() { Pachno.Project.Edition.remove('<?= make_url('configure_delete_edition', array('project_id' => $edition->getProject()->getID(), 'edition_id' => $edition->getID())); ?>', <?= $edition->getID(); ?>);}}, no: { click: Pachno.Main.Helpers.Dialog.dismiss }});">
                        <span class="icon"><?php echo fa_image_tag('times'); ?></span>
                        <span class="name"><?= __('Delete edition'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="edition_<?php echo $edition->getID(); ?>_permissions" style="display: none;" class="backdrop_box large">
    <div class="backdrop_detail_header">
        <span><?php echo __('Permission details for "%itemname"', array('%itemname' => $edition->getName())); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="$('edition_<?php echo $edition->getID(); ?>_permissions').toggle();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?php echo __('Specify who can access this edition.'); ?>
        <?php include_component('configuration/permissionsinfo', array('key' => 'canseeedition', 'mode' => 'project_hierarchy', 'target_id' => $edition->getID(), 'module' => 'core', 'access_level' => $access_level)); ?>
    </div>
</div>
