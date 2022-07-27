<?php
    
    use pachno\core\entities\IssuetypeScheme;
    use pachno\core\framework\Context;
    use pachno\core\entities;

    /** @var IssuetypeScheme $scheme */

?>
<div class="backdrop_box large edit_issuetype">
    <div class="backdrop_detail_header">
        <span><?= ($scheme->getId()) ? __('Edit issue type') : __('Create issue type'); ?></span>
        <a href="javascript:void(0);" class="closer"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <form accept-charset="<?= Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_issuetypes_scheme_post', ['scheme_id' => $scheme->getId()]); ?>" data-simple-submit id="edit_issuetype_<?= $scheme->getID(); ?>_form">
                <div class="column">
                    <div class="form-row">
                        <input type="text" name="name" value="<?= $scheme->getName(); ?>" class="name-input-enhance" id="edit-issuetype-scheme-name" placeholder="<?= __('Issue type scheme name'); ?>">
                        <label for="edit-issuetype-name"><?= __('Scheme name'); ?></label>
                    </div>
                </div>
                <div class="form-row">
                    <input type="text" name="description" id="issuetype_scheme_<?= $scheme->getID(); ?>_description" value="<?= $scheme->getDescription(); ?>" placeholder="<?= __('Enter an optional description here'); ?>">
                    <label for="issuetype_scheme_<?= $scheme->getID(); ?>_description"><?= __('Description'); ?></label>
                </div>
                <div class="form-row error-container">
                    <div class="error"></div>
                </div>
                <div class="form-row submit-container">
                    <button type="submit" class="button primary">
                        <span><?= ($scheme->getID()) ? __('Save scheme') : __('Create scheme'); ?></span>
                        <?= fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
