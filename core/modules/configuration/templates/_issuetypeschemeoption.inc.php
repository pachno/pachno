<?php

    use pachno\core\entities\tables\IssueFields;

?>
<input type="hidden" name="field[<?= $key; ?>][visible]" value="1">
<?php if (is_object($item) || !in_array($item, ['description', 'status'])): ?>
    <div class="configurable-component" id="item_<?= $key; ?>_<?= $issue_type->getID(); ?>">
        <div class="row">
            <div class="icon">
                <?= (is_object($item)) ? fa_image_tag('tag') : fa_image_tag(IssueFields::getFieldFontAwesomeImage($item), [], IssueFields::getFieldFontAwesomeImageStyle($item)); ?>
            </div>
            <?php if (!$item instanceof \pachno\core\entities\CustomDatatype): ?>
                <div class="information">
                    <span class="count-badge"><?= __('Built-in'); ?></span>
                </div>
            <?php endif; ?>
            <div class="name">
                <?= (is_object($item)) ? $item->getDescription() : IssueFields::getFieldDescription($item); ?>
            </div>
            <div class="icon">
                <button class="button icon secondary collapser" data-target="#f_<?= $issue_type->getID(); ?>_<?= $key; ?>_options" data-exclusive><?= fa_image_tag('angle-down'); ?></button>
            </div>
        </div>
        <div class="row options-container collapse-target" id="f_<?= $issue_type->getID(); ?>_<?= $key; ?>_options">
            <?php if (!in_array($key, array('votes', 'owner', 'assignee'))): ?>
                <div class="form-row">
                    <input type="checkbox" class="fancycheckbox" id="f_<?= $issue_type->getID(); ?>_<?= $key; ?>_reportable" onclick="if (this.checked) { $('f_<?= $issue_type->getID(); ?>_<?= $key; ?>_required').enable();$('f_<?= $issue_type->getID(); ?>_<?= $key; ?>_required').enable(); } else { $('f_<?= $issue_type->getID(); ?>_<?= $key; ?>_required').disable();$('f_<?= $issue_type->getID(); ?>_<?= $key; ?>_required').disable(); }" name="field[<?= $key; ?>][reportable]" value="1"<?php if (array_key_exists($key, $visible_fields) && $visible_fields[$key]['reportable']): ?> checked<?php endif; ?><?php if (!array_key_exists($key, $visible_fields) && !in_array($key, array('status'))): ?> disabled<?php endif; ?>>
                    <label for="f_<?= $issue_type->getID(); ?>_<?= $key; ?>_reportable">
                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                        <span class="name"><?= __('Show field when creating a new issue'); ?></span>
                    </label>
                </div>
                <div class="form-row">
                    <input type="checkbox" class="fancycheckbox" id="f_<?= $issue_type->getID(); ?>_<?= $key; ?>_required" name="field[<?= $key; ?>][required]" value="1" <?php if (array_key_exists($key, $visible_fields) && $visible_fields[$key]['required']) echo 'checked'; ?><?php if ((!array_key_exists($key, $visible_fields) || !$visible_fields[$key]['reportable']) || in_array($key, array('votes', 'owner', 'assignee'))) echo 'disabled'; ?>>
                    <label for="f_<?= $issue_type->getID(); ?>_<?= $key; ?>_required">
                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                        <span class="name"><?= __('Make field required when creating a new issue'); ?></span>
                    </label>
                </div>
            <?php endif; ?>
            <div class="form-row submit-container">
                <button class="button secondary">
                    <?= fa_image_tag('times', ['class' => 'icon']); ?>
                    <span><?= __('Remove field'); ?></span>
                </button>
                <?php if (!in_array($key, ['votes', 'owner', 'description', 'reproduction_steps'])): ?>
                    <button class="button secondary" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_issuefield', 'type' => $key]); ?>');">
                        <?= fa_image_tag('edit', ['class' => 'icon'], 'far'); ?>
                        <span><?= __('Edit field options'); ?></span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
