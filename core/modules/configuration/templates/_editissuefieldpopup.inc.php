<?php

    /** @var \pachno\core\entities\CustomDatatype $item */

?>
<div class="backdrop_box large edit_agileboard">
    <div class="backdrop_detail_header">
        <span><?= (!$item instanceof \pachno\core\entities\CustomDatatype || $item->getId()) ? __('Edit field options') : __('Create new field'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="Pachno.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?php if ($item instanceof \pachno\core\entities\CustomDatatype && !$item->getId()): ?>
            <div class="form-container">
                <form action="<?= make_url('configure_issuefields_add_customtype'); ?>" method="post" onsubmit="Pachno.Config.Issuefields.Custom.save(this);return false;">
                    <div class="form-row">
                        <input type="text" id="edit_field_field_name" name="name" value="<?= htmlentities($item->getName(), ENT_COMPAT, \pachno\core\framework\Context::getI18n()->getCharset()); ?>" class="name-input-enhance" placeholder="<?= __('Enter a field name'); ?>">
                        <label style for="edit_field_field_name"><?= __('Field name'); ?></label>
                    </div>
                    <div class="form-row">
                        <div class="fancydropdown-container">
                            <div class="fancydropdown">
                                <label><?php echo __('Type of field'); ?></label>
                                <span class="value"></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode">
                                    <?php foreach (\pachno\core\entities\CustomDatatype::getFieldTypes() as $type => $details): ?>
                                        <input class="fancycheckbox" type="radio" name="type" value="<?php echo $type; ?>" id="field_type_dropdown_<?php echo $type; ?>">
                                        <label for="field_type_dropdown_<?php echo $type; ?>" class="list-item multiline">
                                            <span class="icon"><?php echo fa_image_tag($details['icon']); ?></span>
                                            <span class="name">
                                                <span class="title value"><?php echo $details['title']; ?></span>
                                                <span class="description"><?php echo $details['description']; ?></span>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row error-container">
                        <div class="error"></div>
                        <input type="hidden" name="type" value="<?= $item->getKey(); ?>">
                        <?php if (isset($issue_type)): ?>
                            <input type="hidden" name="issue_type_id" value="<?= $issue_type->getId(); ?>">
                        <?php endif; ?>
                        <?php if (isset($scheme)): ?>
                            <input type="hidden" name="scheme_id" value="<?= $scheme->getId(); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="form-row submit-container">
                        <button type="submit" class="button primary">
                            <?= fa_image_tag('spinner', ['class' => 'indicator fa-spin icon']); ?>
                            <span><?= __('Add field'); ?></span>
                        </button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <?php include_component('configuration/editissuefield', ['type' => $item]); ?>
            <div class="form-container">
                <div class="form-row submit-container">
                    <button type="submit" class="button primary" onclick="Pachno.Main.Helpers.Backdrop.reset();">
                        <span><?= __('Done'); ?></span>
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
