<?php

    /** @var \pachno\core\entities\Issuetype $type */

?>
<div class="row" id="issuetype_<?php echo $type->getID(); ?>">
    <div class="column info-icons"><?= fa_image_tag($type->getFontAwesomeIcon(), ['class' => 'issuetype-icon issuetype-' . $type->getType()]); ?></div>
    <div class="column name-container">
        <?php echo $type->getName(); ?>
    </div>
    <div class="column actions">
        <div class="dropper-container">
            <button class="dropper button secondary">
                <span><?= __('Actions'); ?></span>
                <?= fa_image_tag('angle-down', ['class' => 'icon']); ?>
            </button>
            <div class="dropdown-container">
                <div class="list-mode">
                    <a class="list-item" title="<?php echo __('Show / edit issue type settings'); ?>" href="javascript:void(0);" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_issuetype', 'issuetype_id' => $type->getID()]); ?>');">
                        <?php echo fa_image_tag('edit', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Edit'); ?></span>
                    </a>
                    <div class="list-item separator"></div>
                    <a class="list-item danger" title="<?php echo __('Remove issuetype'); ?>" onclick="<?php if (!$type->isAssociatedWithAnySchemes()): ?>Pachno.Main.Helpers.Dialog.show('<?php echo __('Delete this issue type?'); ?>', '<?php echo __('Do you really want to delete this issue type? Issues with this issue type will be unavailable.').'<br><b>'.__('This action cannot be reverted').'</b>'; ?>', {yes: {click: function() {Pachno.Config.Issuetype.remove('<?php echo make_url('configure_issuetypes_delete', array('id' => $type->getID())); ?>', <?php echo $type->getID(); ?>);}}, no: {click: Pachno.Main.Helpers.Dialog.dismiss}});<?php else: ?>Pachno.Main.Helpers.Message.error('<?php echo __('Cannot remove this issue type'); ?>', '<?php echo __('Issue types associated with an issue type scheme cannot be removed'); ?>');<?php endif; ?>">
                        <?php echo fa_image_tag('times', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Delete'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
