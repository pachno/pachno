<?php

    /**
     * @var \pachno\core\entities\IssuetypeScheme $scheme
     * @var \pachno\core\entities\Issuetype $type
     */

?>
<div class="configurable-component-container" id="issuetype_<?php echo $type->getID(); ?>_box">
    <div class="configurable-component">
        <div class="row">
            <div class="icon">
                <?= fa_image_tag($type->getFontAwesomeIcon(), ['class' => 'issuetype-icon issuetype-' . $type->getType()]); ?>
            </div>
            <div class="name">
                <span class="title"><?php echo $type->getName(); ?></span>
                <span class="description"><?= $type->getDescription(); ?></span>
            </div>
            <div class="icon">
                <input class="fancycheckbox" type="checkbox" name="enabled" value="1" id="edit_issuetype_scheme_<?= $type->getId(); ?>_enabled" <?php if ($scheme->isSchemeAssociatedWithIssuetype($type)) echo 'checked'; ?>>
                <label for="edit_issuetype_scheme_<?= $type->getId(); ?>_enabled">
                    <?= fa_image_tag('toggle-on', ['class' => 'checked']) . fa_image_tag('toggle-off', ['class' => 'unchecked']); ?>
                </label>
            </div>
            <div class="icon">
                <button class="button secondary icon collapser" data-target="#issuetype_<?= $type->getId(); ?>_options"><?= fa_image_tag('angle-down'); ?></button>
            </div>
            <?php /*
            <a href="#" onclick="Pachno.Config.Issuetype.toggleForScheme('<?php echo make_url('configure_issuetypes_enable_issuetype_for_scheme', array('id' => $type->getID(), 'scheme_id' => $scheme->getID())); ?>', <?php echo $type->getID(); ?>, <?php echo $scheme->getID(); ?>, 'enable');return false;"<?php if ($scheme->isSchemeAssociatedWithIssuetype($type)): ?> style="display: none;"<?php endif; ?> class="issuetype_scheme_associate_link" id="type_toggle_<?php echo $type->getID(); ?>_enable"><?php echo __('Enable issue type for this scheme'); ?></a>
            <a href="#" onclick="Pachno.Config.Issuetype.toggleForScheme('<?php echo make_url('configure_issuetypes_disable_issuetype_for_scheme', array('id' => $type->getID(), 'scheme_id' => $scheme->getID())); ?>', <?php echo $type->getID(); ?>, <?php echo $scheme->getID(); ?>, 'disable');return false;"<?php if (!$scheme->isSchemeAssociatedWithIssuetype($type)): ?> style="display: none;"<?php endif; ?> class="issuetype_scheme_associate_link" id="type_toggle_<?php echo $type->getID(); ?>_disable"><?php echo __('Disable issue type for this scheme'); ?></a>
            */ ?>
        </div>
    </div>
    <div class="configurable-component-options">
        <div id="issuetype_<?php echo $type->getID(); ?>_options" class="collapse-target">
            <h5>
                <span class="name"><?= __('Existing fields'); ?></span>
                <span class="dropper-container">
                    <button class="button primary dropper"><?= __('Add field'); ?></button>
                    <span class="dropdown-container list-mode columns two-columns">
                        <span class="column">
                            <span class="header"><?= __('Built-in fields'); ?></span>
                            <?php foreach ($builtin_fields as $item): ?>
                                <a href="javascript:void(0);" class="list-item">
                                    <span class="name"><?= $item; ?></span>
                                </a>
                            <?php endforeach; ?>
                        </span>
                        <span class="column">
                            <span class="header"><?= __('Custom fields'); ?></span>
                            <?php foreach ($custom_fields as $key => $item): ?>
                                <a href="javascript:void(0);" class="list-item">
                                    <span class="name"><?= $key; ?></span>
                                </a>
                            <?php endforeach; ?>
                        </span>
                    </span>
                </span>
            </h5>
            <div class="configurable-components-list" id="<?php echo $type->getID(); ?>_list">
                <?php foreach ($builtin_fields as $item): ?>
                    <?php if (!array_key_exists($item, $visiblefields)) continue; ?>
                    <?php include_component('issuetypeschemeoption', array('issuetype' => $type, 'scheme' => $scheme, 'key' => $item, 'item' => $item, 'visiblefields' => $visiblefields)); ?>
                <?php endforeach; ?>
                <?php if (count($custom_fields)): ?>
                    <?php foreach ($custom_fields as $key => $item): ?>
                        <?php if (!array_key_exists($key, $visiblefields)) continue; ?>
                        <?php include_component('issuetypeschemeoption', array('issuetype' => $type, 'scheme' => $scheme, 'key' => $key, 'item' => $item, 'visiblefields' => $visiblefields)); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
