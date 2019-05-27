<?php

    /** @var \pachno\core\entities\Issuetype $type */
    $route = ($type->getID()) ? make_url('configure_edit_issuetype', ['issuetype_id' => $type->getID()]) : make_url('configure_add_issuetype');

?>
<div class="backdrop_box large edit_issuetype">
    <div class="backdrop_detail_header">
        <span><?php echo ($type->getId()) ? __('Edit issue type') : __('Create issue type'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="Pachno.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_add_issuetype', array('id' => (int) $type->getID())); ?>" onsubmit="Pachno.Config.Issuetype.update(<?php echo (int) $type->getID(); ?>);return false;" id="edit_issuetype_<?php echo (int) $type->getID(); ?>_form">
                <div class="column small">
                    <div class="form-row">
                        <div class="fancydropdown-container">
                            <div class="fancydropdown">
                                <label><?= __('Issue type'); ?></label>
                                <span class="value"></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode">
                                    <?php foreach ($icons as $icon => $description): ?>
                                        <input type="radio" id="edit_issuetype_icon_<?= $icon; ?>" name="icon" value="<?= $icon; ?>" <?php if ($icon == $type->getIcon()) echo ' checked'; ?> class="fancycheckbox">
                                        <label for="edit_issuetype_icon_<?= $icon; ?>" class="list-item">
                                            <span class="icon"><?= fa_image_tag(\pachno\core\entities\Issuetype::getFontAwesomeIconFromIcon($icon), ['class' => 'issuetype-icon issuetype-' . $icon]); ?></span>
                                            <span class="name value"><?= $description; ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="form-row">
                        <input type="text" name="name" value="<?php echo $type->getName(); ?>" class="name-input-enhance" id="edit-issuetype-name">
                        <label for="edit-issuetype-name"><?php echo __('Issue type name'); ?></label>
                    </div>
                </div>
                <div class="form-row">
                    <input type="text" name="description" id="issuetype_<?php echo $type->getID(); ?>_description" value="<?php echo $type->getDescription(); ?>">
                    <label for="issuetype_<?php echo $type->getID(); ?>_description"><?php echo __('Description'); ?></label>
                    <div class="helper-text"><?php echo __('Users see this description when choosing an issue type to report'); ?></div>
                </div>
                <div class="form-row submit-container">
                    <button type="submit" class="button primary">
                        <?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?>
                        <span><?php echo ($type->getID()) ? __('Save issue type') : __('Create issue type'); ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
