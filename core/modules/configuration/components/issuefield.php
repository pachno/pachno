<?php

    use pachno\core\framework\Context;
    use pachno\core\entities\tables\IssueFields;

    /**
     * @var \pachno\core\entities\IssuetypeScheme $scheme
     * @var \pachno\core\entities\Issuetype $type
     * @var \pachno\core\entities\CustomDatatype[] $custom_fields
     */

?>
<div class="configurable-component issue-field" data-issue-field data-id="<?= $type_key; ?>" id="issuefield_<?= $type_key; ?>_box" data-options-url="<?= make_url('configure_issuefields_getoptions', ['type' => $type_key]); ?>">
    <div class="row">
        <div class="icon">
            <?= fa_image_tag(IssueFields::getFieldFontAwesomeImage($type_key), [], IssueFields::getFieldFontAwesomeImageStyle($type_key)); ?>
        </div>
        <?php if (!$type instanceof \pachno\core\entities\CustomDatatype): ?>
            <div class="information">
                <span class="count-badge"><?= __('Built-in'); ?></span>
            </div>
        <?php endif; ?>
        <div class="name">
            <div class="title">
                <?php if ($type instanceof \pachno\core\entities\CustomDatatype): ?>
                    <?= $type->getName(); ?>
                <?php else: ?>
                    <span><?= IssueFields::getFieldName($type_key); ?></span>
                <?php endif; ?>
            </div>
            <?php if ($type instanceof \pachno\core\entities\CustomDatatype): ?>
                <div class="description"><?= $type->getTypeDescription(); ?></div>
            <?php endif; ?>
        </div>
        <button class="icon open trigger-open-component" type="button">
            <?= fa_image_tag('angle-right'); ?>
        </button>
    </div>
</div>
