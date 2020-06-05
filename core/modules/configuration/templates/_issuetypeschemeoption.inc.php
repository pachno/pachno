<?php

    use pachno\core\entities\tables\IssueFields;

?>
<?php if (is_object($item) || !in_array($item, ['description', 'status'])): ?>
    <div class="configurable-component" id="item_<?= $key; ?>_<?= $issue_type->getID(); ?>" data-issue-field data-id="<?= $key; ?>">
        <input type="hidden" name="field[<?= $key; ?>][visible]" value="1">
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
            <?php if (in_array($key, ['build', 'component', 'edition'])): ?>
                <div class="icon tooltip-container">
                    <button type="button" class="button icon secondary" data-target="#f_<?= $issue_type->getID(); ?>_<?= $key; ?>_options" data-exclusive><?= fa_image_tag('info-circle'); ?></button>
                    <div class="tooltip">
                        <span><?= __('This field is only shown for projects with this setting enabled'); ?></span>
                    </div>
                </div>
            <?php endif; ?>
            <div class="icon">
                <?php if (in_array($key, ['votes'])): ?>
                    <button class="button icon secondary remove-item"><?= fa_image_tag('times'); ?></button>
                <?php else: ?>
                    <button type="button" class="button icon secondary collapser <?php if (isset($expanded) && $expanded) echo 'active'; ?>" data-target="#f_<?= $issue_type->getID(); ?>_<?= $key; ?>_options" data-exclusive><?= fa_image_tag('angle-down'); ?></button>
                <?php endif; ?>
            </div>
        </div>
        <?php if (!in_array($key, ['votes'])): ?>
            <div class="row options-container collapse-target <?php if (isset($expanded) && $expanded) echo 'active'; ?>" id="f_<?= $issue_type->getID(); ?>_<?= $key; ?>_options">
                <?php if (in_array($key, ['build', 'component', 'edition'])): ?>
                    <div class="message-box type-warning">
                        <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                        <span class="message">
                            <span><?= __('This field is only shown for projects with this setting enabled'); ?></span>
                        </span>
                    </div>
                <?php endif; ?>
                <?php if (!in_array($key, array('votes', 'owner', 'assignee'))): ?>
                    <div class="form-row">
                        <input type="checkbox" class="fancy-checkbox" id="f_<?= $issue_type->getID(); ?>_<?= $key; ?>_reportable" onclick="if ($(this).checked) { $('#f_<?= $issue_type->getID(); ?>_<?= $key; ?>_required').prop('disabled', false); } else { $('#f_<?= $issue_type->getID(); ?>_<?= $key; ?>_required').prop('disabled', true); }" name="field[<?= $key; ?>][reportable]" value="1"<?php if (array_key_exists($key, $visible_fields) && $visible_fields[$key]['reportable']): ?> checked<?php endif; ?><?php if (!array_key_exists($key, $visible_fields) && !in_array($key, array('status'))): ?> disabled<?php endif; ?>>
                        <label for="f_<?= $issue_type->getID(); ?>_<?= $key; ?>_reportable">
                            <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                            <span class="name"><?= __('Show field when creating a new issue'); ?></span>
                        </label>
                    </div>
                    <div class="form-row">
                        <input type="checkbox" class="fancy-checkbox" id="f_<?= $issue_type->getID(); ?>_<?= $key; ?>_required" name="field[<?= $key; ?>][required]" value="1" <?php if (array_key_exists($key, $visible_fields) && $visible_fields[$key]['required']) echo 'checked'; ?><?php if ((!array_key_exists($key, $visible_fields) || !$visible_fields[$key]['reportable']) || in_array($key, array('votes', 'owner', 'assignee'))) echo 'disabled'; ?>>
                        <label for="f_<?= $issue_type->getID(); ?>_<?= $key; ?>_required">
                            <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                            <span class="name"><?= __('Make field required when creating a new issue'); ?></span>
                        </label>
                    </div>
                <?php endif; ?>
                <div class="form-row submit-container">
                    <button class="button secondary remove-item">
                        <?= fa_image_tag('times', ['class' => 'icon']); ?>
                        <span><?= __('Remove field'); ?></span>
                    </button>
                    <?php if (!in_array($key, ['owner', 'description', 'reproduction_steps'])): ?>
                        <button class="button secondary" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_issuefield', 'type' => $key]); ?>');return false;">
                            <?= fa_image_tag('edit', ['class' => 'icon'], 'far'); ?>
                            <span><?= __('Edit field options'); ?></span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
