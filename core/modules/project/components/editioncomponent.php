<?php

/**
 * @var \pachno\core\entities\Edition $edition
 * @var \pachno\core\entities\Component $component
 */

?>
<div class="configurable-component project-edition-component" data-edition-component>
    <div class="row">
        <div class="icon"><?= fa_image_tag('boxes'); ?></div>
        <div class="name"><?= $component->getName(); ?></div>
        <div class="icon open trigger-open-component">
            <input type="checkbox" class="fancy-checkbox" id="edition_component_<?= $component->getId(); ?>" name="components[<?= $component->getId(); ?>]" value="1" <?php if ($edition->hasComponent($component)) echo 'checked'; ?> >
            <label for="edition_component_<?= $component->getId(); ?>"><?php echo fa_image_tag('toggle-on', ['class' => 'checked']) . fa_image_tag('toggle-off', ['class' => 'unchecked']); ?></label>
        </div>
    </div>
</div>
