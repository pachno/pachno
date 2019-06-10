<?php

    use \pachno\core\entities\tables\IssueFields;

?>
<h5>
    <span class="name"><?= __('Issue fields'); ?></span>
    <span class="dropper-container">
        <button class="button secondary dropper"><?= __('Add field'); ?></button>
        <span class="dropdown-container list-mode columns two-columns">
            <span class="column">
                <span class="header"><?= __('Built-in fields'); ?></span>
                <?php foreach ($builtin_fields as $item): ?>
                    <?php if (array_key_exists($item, $visible_fields)) continue; ?>
                    <a href="javascript:void(0);" class="list-item">
                        <span class="icon"><?= (is_object($item)) ? fa_image_tag('tag') : fa_image_tag(IssueFields::getFieldFontAwesomeImage($item), [], IssueFields::getFieldFontAwesomeImageStyle($item)); ?></span>
                        <span class="name"><?= IssueFields::getFieldDescription($item); ?></span>
                    </a>
                <?php endforeach; ?>
            </span>
            <span class="column">
                <span class="header"><?= __('Custom fields'); ?></span>
                <?php foreach ($custom_fields as $item): ?>
                    <?php if (array_key_exists($key, $visible_fields)) continue; ?>
                    <a href="javascript:void(0);" class="list-item">
                        <span class="icon"><?= (is_object($item)) ? fa_image_tag('tag') : fa_image_tag(IssueFields::getFieldFontAwesomeImage($item), [], IssueFields::getFieldFontAwesomeImageStyle($item)); ?></span>
                        <span class="name"><?= $item->getDescription(); ?></span>
                    </a>
                <?php endforeach; ?>
            </span>
        </span>
    </span>
</h5>
<div class="form-container">
    <form action="<?= make_url('configure_issuetypes_scheme_options_post', ['issue_type_id' => $issue_type->getID(), 'scheme_id' => $scheme->getID()]); ?>" onsubmit="Pachno.Config.IssuetypeScheme.saveOptions(this);return false;">
        <div class="configurable-components-list" id="<?= $issue_type->getID(); ?>_list">
            <?php foreach ($builtin_fields as $item): ?>
                <?php if (!array_key_exists($item, $visible_fields)) continue; ?>
                <?php include_component('issuetypeschemeoption', array('issue_type' => $issue_type, 'scheme' => $scheme, 'key' => $item, 'item' => $item, 'visible_fields' => $visible_fields)); ?>
            <?php endforeach; ?>
            <?php if (count($custom_fields)): ?>
                <?php foreach ($custom_fields as $key => $item): ?>
                    <?php if (!array_key_exists($key, $visible_fields)) continue; ?>
                    <?php include_component('issuetypeschemeoption', array('issue_type' => $issue_type, 'scheme' => $scheme, 'key' => $key, 'item' => $item, 'visible_fields' => $visible_fields)); ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="form-row submit-container">
            <button type="submit" class="button primary">
                <span class="name"><?= __('Save'); ?></span>
                <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
            </button>
        </div>
    </form>
</div>
