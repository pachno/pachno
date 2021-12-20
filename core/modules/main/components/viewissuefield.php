<?php

    /** @var \pachno\core\entities\Issue $issue */

    if (in_array($field, array('priority'))) $primary = true;
    $canEditField = "canEdit".ucfirst($field);
    $editable = $issue->$canEditField();

?>
<li id="<?php echo $field; ?>_field" class="issue-field <?php if (!$info['visible']): ?> hidden<?php endif; ?> <?php if (array_key_exists('choices', $info) && count($info['choices']) && $editable) echo 'editable'; ?>">
    <div id="<?php echo $field; ?>_content" class="<?php if (isset($info['extra_classes'])) echo $info['extra_classes']; ?> value fancy-dropdown-container">
        <div class="fancy-dropdown" data-default-label="<?= __('Not determined'); ?>">
            <label><?php echo $info['title']; ?></label>
            <span class="value" data-dynamic-field-value data-field="<?= $field; ?>" data-issue-id="<?= $issue->getId(); ?>"></span>
            <?php if (array_key_exists('choices', $info) && count($info['choices']) && $editable): ?>
                <?php echo fa_image_tag('angle-down', ['class' => 'expander']); ?>
                <div class="dropdown-container">
                    <div class="list-mode">
                        <div class="header"><?php echo $info['change_header']; ?></div>
                        <input type="radio" class="fancy-checkbox" id="issue_<?= $issue->getId(); ?>_fields_<?= $field; ?>_0" name="issue[fields][<?= $field; ?>]" value="0" <?php if ($info['value'] == 0) echo ' checked'; ?>  data-trigger-issue-update data-field="<?= $field; ?>" data-issue-id="<?= $issue->getId(); ?>">
                        <label class="list-item" for="issue_<?= $issue->getId(); ?>_fields_<?= $field; ?>_0">
                            <span class="icon"><?php echo fa_image_tag('times'); ?></span>
                            <span class="name"><?php echo $info['clear']; ?></span>
                            <span class="indicator"><?= fa_image_tag('spinner', ['class' => 'fa-spin']); ?></span>
                        </label>
                        <?php if (count($info['choices'])): ?>
                            <div class="list-item separator"></div>
                            <?php foreach ($info['choices'] as $choice): ?>
                                <input type="radio" class="fancy-checkbox" id="issue_<?= $issue->getId(); ?>_fields_<?= $field; ?>_<?= $choice->getId(); ?>" name="issues[<?= $issue->getId(); ?>]fields[<?= $field; ?>]" value="<?= $choice->getId(); ?>" <?php if ($info['value'] == $choice->getId()) echo ' checked'; ?> data-trigger-issue-update data-field="<?= $field; ?>" data-issue-id="<?= $issue->getId(); ?>">
                                <label for="issue_<?= $issue->getId(); ?>_fields_<?= $field; ?>_<?= $choice->getId(); ?>" class="list-item <?php if ($choice instanceof \pachno\core\entities\Priority): ?>priority priority_<?= $choice->getValue(); ?><?php endif; ?>">
                                    <span class="icon">
                                        <?php if ($choice->getFontAwesomeIcon()): ?>
                                            <?php echo fa_image_tag($choice->getFontAwesomeIcon(), [], $choice->getFontAwesomeIconStyle()); ?>
                                        <?php elseif (isset($info['fa_icon'])): ?>
                                            <?php echo fa_image_tag($info['fa_icon'], [], $info['fa_icon_style']); ?>
                                        <?php else: ?>
                                            <?php echo image_tag('icon_' . $field . '.png'); ?>
                                        <?php endif; ?>
                                    </span>
                                    <span class="name value"><?= __($choice->getName()); ?></span>
                                    <span class="indicator"><?= fa_image_tag('spinner', ['class' => 'fa-spin']); ?></span>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="list-item disabled"><?php echo __('No choices available'); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</li>
