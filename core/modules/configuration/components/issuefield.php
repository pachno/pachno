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
                    <span><?= IssueFields::getFieldDescription($type_key); ?></span>
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
    <?php /*
    <div class="configurable-component-options">
        <div id="issuetype_<?= $type->getID(); ?>_options" class="collapse-target">
            <h5>
                <span class="name"><?= __('Existing fields'); ?></span>
                <span class="dropper-container">
                    <button class="button primary dropper"><?= __('Add field'); ?></button>
                    <span class="dropdown-container list-mode columns two-columns">
                        <span class="column">
                            <span class="header"><?= __('Built-in fields'); ?></span>
                            <?php foreach ($builtin_fields as $item): ?>
                                <?php if (array_key_exists($item, $visiblefields)) continue; ?>
                                <a href="javascript:void(0);" class="list-item">
                                    <span class="name"><?= \pachno\core\entities\tables\IssueFields::getFieldDescription($item); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </span>
                        <span class="column">
                            <span class="header"><?= __('Custom fields'); ?></span>
                            <?php foreach ($custom_fields as $item): ?>
                                <?php if (array_key_exists($key, $visiblefields)) continue; ?>
                                <a href="javascript:void(0);" class="list-item">
                                    <span class="name"><?= $item->getDescription(); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </span>
                    </span>
                </span>
            </h5>
            <div class="configurable-components-list" id="<?= $type->getID(); ?>_list">
                <?php foreach ($builtin_fields as $item): ?>
                    <?php if (!array_key_exists($item, $visiblefields)) continue; ?>
                    <?php include_component('issuetypeschemeoption', array('issuetype' => $type, 'scheme' => $scheme, 'key' => $item, 'item' => $item, 'visiblefields' => $visiblefields)); ?>
                <?php endforeach; ?>
                <?php if (count($custom_fields)): ?>
                    <?php foreach ($custom_fields as $key => $item): ?>
                        <?php if (!array_key_exists($key, $visiblefields)) continue; ?>
                        <?php include_component('issuetypeschemeoption', array('issuetype' => $type, 'scheme' => $scheme, 'key' => $key, 'item' => $item, 'visiblefields' => $visiblefields)); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div> */ ?>
</div>
