<?php

    /** @var \pachno\core\entities\IssuetypeScheme $scheme */

?>
<div class="row" id="issuetype_scheme_<?php echo $scheme->getID(); ?>">
    <div class="column name-container multiline">
        <div class="title"><?php echo $scheme->getName(); ?></div>
        <?php if ($scheme->getDescription()): ?>
            <div class="description"><?php echo $scheme->getDescription(); ?></div>
        <?php endif; ?>
    </div>
    <div class="column">
        <span class="count-badge">
            <?php echo __('%number_of_projects project(s)', array('%number_of_projects' => $scheme->getNumberOfProjects())); ?>
        </span>
    </div>
    <div class="column actions">
        <div class="dropper-container">
            <button class="dropper button secondary">
                <span><?= __('Actions'); ?></span>
                <?= fa_image_tag('angle-down', ['class' => 'icon']); ?>
            </button>
            <div class="dropdown-container">
                <div class="list-mode">
                    <a class="list-item" title="<?php echo __('Edit issue type scheme'); ?>" href="<?= make_url('configure_issuetypes_scheme', ['scheme_id' => $scheme->getID()]); ?>">
                        <?php echo fa_image_tag('edit', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Edit'); ?></span>
                    </a>
                    <a class="list-item" href="javascript:void(0);" onclick="$('copy_scheme_<?php echo $scheme->getID(); ?>_popup').toggle();" class="button">
                        <?php echo fa_image_tag('clone', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Make a copy'); ?></span>
                    </a>
                    <div class="list-item separator"></div>
                    <a class="list-item danger" title="<?php echo __('Remove issuetype'); ?>" onclick="<?php if (!$scheme->isInUse()): ?>Pachno.UI.Dialog.show('<?php echo __('Delete this issue type?'); ?>', '<?php echo __('Do you really want to delete this issue type? Issues with this issue type will be unavailable.').'<br><b>'.__('This action cannot be reverted').'</b>'; ?>', {yes: {click: function() {Pachno.Config.Issuetype.remove('<?php echo make_url('configure_issuetypes_delete', array('id' => $scheme->getID())); ?>', <?php echo $scheme->getID(); ?>);}}, no: {click: Pachno.UI.Dialog.dismiss}});<?php else: ?>Pachno.UI.Message.error('<?php echo __('Cannot delete issuetype scheme'); ?>', '<?php echo __('This issuetype scheme can not be deleted as it is being used by %number_of_projects project(s)', array('%number_of_projects' => $scheme->getNumberOfProjects())); ?>');<?php endif; ?>">
                        <?php echo fa_image_tag('times', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Delete'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /*
<li class="rounded_box white shadowed" id="copy_scheme_<?php echo $scheme->getID(); ?>_popup" style="margin-bottom: 5px; padding: 10px; display: none;">
    <div class="header"><?php echo __('Copy issue type scheme'); ?></div>
    <div class="content">
        <?php echo __('Please enter the name of the new issue type scheme'); ?><br>
        <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuetypes_copy_scheme', array('scheme_id' => $scheme->getID())); ?>" onsubmit="Pachno.Config.IssuetypeScheme.copy('<?php echo make_url('configure_issuetypes_copy_scheme', array('scheme_id' => $scheme->getID())); ?>', <?php echo $scheme->getID(); ?>);return false;" id="copy_issuetype_scheme_<?php echo $scheme->getID(); ?>_form">
            <label for="copy_scheme_<?php echo $scheme->getID(); ?>_new_name"><?php echo __('New name'); ?></label>
            <input type="text" name="new_name" id="copy_scheme_<?php echo $scheme->getID(); ?>_new_name" value="<?php echo __('Copy of %old_name', array('%old_name' => addslashes($scheme->getName()))); ?>" style="width: 300px;">
            <div style="text-align: right;">
                <?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'copy_issuetype_scheme_'.$scheme->getID().'_indicator')); ?>
                <input type="submit" value="<?php echo __('Copy issue type scheme'); ?>">
            </div>
        </form>
    </div>
</li>
<?php if (!$scheme->isInUse()): ?>
    <li class="rounded_box white shadowed" id="delete_scheme_<?php echo $scheme->getID(); ?>_popup" style="margin-bottom: 5px; padding: 10px; display: none;">
        <div class="header"><?php echo __('Are you sure?'); ?></div>
        <div class="content">
            <?php echo __('Please confirm that you want to delete this issue type scheme.'); ?><br>
            <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuetypes_delete_scheme', array('scheme_id' => $scheme->getID())); ?>" onsubmit="Pachno.Config.IssuetypeScheme.remove('<?php echo make_url('configure_issuetypes_delete_scheme', array('scheme_id' => $scheme->getID())); ?>', <?php echo $scheme->getID(); ?>);return false;" id="delete_issuetype_scheme_<?php echo $scheme->getID(); ?>_form">
                <div style="text-align: right;">
                    <?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'delete_issuetype_scheme_'.$scheme->getID().'_indicator')); ?>
                    <input type="submit" value="<?php echo __('Yes, delete it'); ?>"><?php echo __('%delete or %cancel', array('%delete' => '', '%cancel' => '<b>'.javascript_link_tag(__('cancel'), array('onclick' => "$('delete_scheme_{$scheme->getID()}_popup').toggle();")).'</b>')); ?>
                </div>
            </form>
        </div>
    </li>
<?php endif; ?>
*/ ?>
