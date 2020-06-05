<?php

/**
 * @var \pachno\core\entities\Edition $edition
 */

?>
<div class="configurable-component project-edition" data-edition data-id="<?= $edition->getID(); ?>" data-options-url="<?= make_url('configure_project_edition', ['project_id' => $edition->getProject()->getID(), 'edition_id' => $edition->getID()]); ?>">
    <div class="row">
        <div class="icon"><?= fa_image_tag('boxes'); ?></div>
        <div class="name">
            <div class="title"><?= $edition->getName(); ?></div>
            <?php if ($edition->hasDescription()): ?>
                <div class="description"><?= $edition->getDescription(); ?></div>
            <?php endif; ?>
        </div>
        <button class="icon open">
            <?= fa_image_tag('angle-right'); ?>
        </button>
    </div>
</div>
<div id="edition_<?php echo $edition->getID(); ?>_permissions" style="display: none;" class="backdrop_box large">
    <div class="backdrop_detail_header">
        <span><?php echo __('Permission details for "%itemname"', array('%itemname' => $edition->getName())); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="$('#edition_<?php echo $edition->getID(); ?>_permissions').toggle();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?php echo __('Specify who can access this edition.'); ?>
        <?php include_component('configuration/permissionsinfo', array('key' => 'canseeedition', 'mode' => 'project_hierarchy', 'target_id' => $edition->getID(), 'module' => 'core', 'access_level' => $access_level)); ?>
    </div>
</div>
