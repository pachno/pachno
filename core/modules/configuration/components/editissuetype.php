<?php

    use pachno\core\framework\Context;
    use pachno\core\entities;

    /** @var Issuetype $type */

    $route = ($type->getID()) ? make_url('configure_edit_issuetype', ['issuetype_id' => $type->getID()]) : make_url('configure_add_issuetype');

?>
<div class="backdrop_box large edit_issuetype">
    <div class="backdrop_detail_header">
        <span><?= ($type->getId()) ? __('Edit issue type') : __('Create issue type'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="Pachno.UI.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <form accept-charset="<?= Context::getI18n()->getCharset(); ?>" action="<?= $route; ?>" data-simple-submit data-auto-close data-update-container="#<?= ($type->getId()) ? 'issuetype_'.$type->getId() : 'issuetypes_list'; ?>" <?php echo ($type->getId()) ? 'data-update-replace' : 'data-update-insert'; ?> id="edit_issuetype_<?= $type->getID(); ?>_form">
                <?php if (isset($scheme) && $scheme instanceof entities\IssuetypeScheme): ?>
                    <input type="hidden" name="scheme_id" value="<?= $scheme->getId(); ?>">
                <?php endif; ?>
                <div class="column small">
                    <div class="form-row">
                        <div class="fancy-dropdown-container">
                            <div class="fancy-dropdown">
                                <label><?= __('Issue type'); ?></label>
                                <span class="value"></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode">
                                    <?php foreach ($icons as $icon => $description): ?>
                                        <input type="radio" id="edit_issuetype_icon_<?= $icon; ?>" name="icon" value="<?= $icon; ?>" <?php if ($icon == $type->getIcon()) echo ' checked'; ?> class="fancy-checkbox">
                                        <label for="edit_issuetype_icon_<?= $icon; ?>" class="list-item">
                                            <span class="icon"><?= fa_image_tag(entities\Issuetype::getFontAwesomeIconFromIcon($icon), ['class' => 'issuetype-icon issuetype-' . $icon]); ?></span>
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
                        <input type="text" name="name" value="<?= $type->getName(); ?>" class="name-input-enhance" id="edit-issuetype-name">
                        <label for="edit-issuetype-name"><?= __('Issue type name'); ?></label>
                    </div>
                </div>
                <div class="form-row">
                    <input type="text" name="description" id="issuetype_<?= $type->getID(); ?>_description" value="<?= $type->getDescription(); ?>">
                    <label for="issuetype_<?= $type->getID(); ?>_description"><?= __('Description'); ?></label>
                    <div class="helper-text"><?= __('Users see this description when choosing an issue type to report'); ?></div>
                </div>
                <div class="form-row error-container">
                    <div class="error"></div>
                </div>
                <div class="form-row submit-container">
                    <button type="submit" class="button primary">
                        <span><?= ($type->getID()) ? __('Save issue type') : __('Create issue type'); ?></span>
                        <?= fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
