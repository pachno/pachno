<?php

    /** @var \pachno\core\entities\Issue $issue */

    if (in_array($field, array('priority'))) $primary = true;
    $canEditField = "canEdit".ucfirst($field);

?>
<li id="<?php echo $field; ?>_field" <?php if (!$info['visible']): ?> style="display: none;"<?php endif; ?>>
    <div class="label" id="<?php echo $field; ?>_header">
        <?php echo $info['title']; ?>
    </div>
    <div id="<?php echo $field; ?>_content" class="<?php if (isset($info['extra_classes'])) echo $info['extra_classes']; ?> value <?php if (count($info['choices']) && $issue->$canEditField()): ?>dropper-container<?php endif; ?>">
        <div class="value-container <?php if (count($info['choices']) && $issue->$canEditField()): ?>dropper<?php endif; ?>">
            <?php if (array_key_exists('url', $info) && $info['url']): ?>
                <a id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?> target="_new" href="<?php echo $info['current_url']; ?>"><?php echo $info['name']; ?></a>
            <?php else: ?>
                <span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>>
                    <?php if (isset($info['fa_icon'])) echo fa_image_tag($info['fa_icon'], [], $info['fa_icon_style']); ?>
                    <?php echo __($info['name']); ?>
                </span>
            <?php endif; ?>
            <span class="no-value" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>><?php echo __('Not determined'); ?></span>
            <?php if (array_key_exists('choices', $info) && count($info['choices']) && $issue->$canEditField()): ?>
                <?php echo fa_image_tag('angle-down', ['class' => 'dropdown-indicator']); ?>
            <?php endif; ?>
        </div>
        <?php if (array_key_exists('choices', $info) && count($info['choices']) && $issue->$canEditField()): ?>
            <div class="dropdown-container" id="<?php echo $field; ?>_change">
                <div class="list-mode">
                    <div class="header"><?php echo $info['change_header']; ?></div>
                    <div href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_id' => 0)); ?>', '<?php echo $field; ?>');">
                        <span class="icon"><?php echo fa_image_tag('times'); ?></span>
                        <span class="name"><?php echo $info['clear']; ?></span>
                    </div>
                    <?php if (count($info['choices'])): ?>
                        <div class="list-item separator"></div>
                        <?php foreach ($info['choices'] as $choice): ?>
                            <?php if ($choice instanceof \pachno\core\entities\DatatypeBase && !$choice->canUserSet($pachno_user)) continue; ?>
                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_id' => $choice->getID())); ?>', '<?php echo $field; ?>');" <?php if ($choice instanceof \pachno\core\entities\Priority): ?>class="priority priority_<?= $choice->getValue(); ?>"<?php endif; ?>>
                                <span class="icon">
                                    <?php if ($choice->getFontAwesomeIcon()): ?>
                                        <?php echo fa_image_tag($choice->getFontAwesomeIcon(), [], $choice->getFontAwesomeIconStyle()); ?>
                                    <?php elseif (isset($info['fa_icon'])): ?>
                                        <?php echo fa_image_tag($info['fa_icon'], [], $info['fa_icon_style']); ?>
                                    <?php else: ?>
                                        <?php echo image_tag('icon_' . $field . '.png'); ?>
                                    <?php endif; ?>
                                </span>
                                <span class="name"><?= __($choice->getName()); ?></span>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-item disabled"><?php echo __('No choices available'); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</li>
