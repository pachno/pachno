<input type="checkbox" class="fancy-checkbox" value="<?php echo $item->getID(); ?>" name="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $item->getID(); ?>" data-text="<?php if (!\pachno\core\framework\Context::isProjectContext()) echo $item->getProject()->getName().'&nbsp;&ndash;&nbsp;'; ?><?php echo $item->getName(); ?>" id="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $item->getID(); ?>" <?php if ($filter->hasValue($item->getID())) echo 'checked'; ?>>
<label for="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $item->getID(); ?>" class="list-item filtervalue unfiltered<?php if ($filter->hasValue($item->getID())) echo ' selected'; ?>">
    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
    <?php if (!\pachno\core\framework\Context::isProjectContext()) echo $item->getProject()->getName().'&nbsp;&ndash;&nbsp;'; ?><?php echo $item->getName(); ?>
</label>
