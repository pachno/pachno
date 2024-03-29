<?php
    
    use pachno\core\entities\CustomDatatype;
    use pachno\core\entities\Issuetype;
    use pachno\core\entities\IssuetypeScheme;
    use \pachno\core\entities\tables\IssueFields;
    
    /**
     * @var IssuetypeScheme $scheme
     * @var Issuetype $issue_type
     * @var CustomDatatype[] $custom_fields
     * @var string[] $builtin_fields
     * @var string[] $visible_fields
     */

?>
<h5>
    <span class="name"><?= __('Issue fields'); ?></span>
    <span class="dropper-container">
        <button class="button secondary dropper"><?= __('Add field'); ?></button>
        <span class="dropdown-container list-mode two-columns" id="add-issue-field-list">
            <span class="row">
                <span class="column full">
                    <span class="list-item filter-container">
                        <input type="search" placeholder="<?= __('Filter values'); ?>">
                    </span>
                </span>
            </span>
            <span class="row">
                <span class="column">
                    <span class="header"><?= __('Built-in fields'); ?></span>
                    <div class="filter-values-container">
                        <?php foreach ($builtin_fields as $item): ?>
                            <?php if (in_array($item, ['status'])) continue; ?>
                            <a href="javascript:void(0);" class="list-item filtervalue multiline <?php if (array_key_exists($item, $visible_fields)) echo 'disabled'; ?>" data-issue-field data-id="<?= $item; ?>" data-url="<?= make_url('configure_issuetypes_scheme_field', ['key' => $item, 'scheme_id' => $scheme->getId(), 'issue_type_id' => $issue_type->getId()]); ?>">
                                <span class="icon"><?= fa_image_tag(IssueFields::getFieldFontAwesomeImage($item), [], IssueFields::getFieldFontAwesomeImageStyle($item)); ?></span>
                                <span class="name">
                                    <span class="title value"><?= IssueFields::getFieldName($item); ?></span>
                                    <?php if (IssueFields::getFieldName($item) != IssueFields::getFieldDescription($item)): ?>
                                        <span class="description"><?= IssueFields::getFieldDescription($item); ?></span>
                                    <?php endif; ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </span>
                <span class="column">
                    <span class="header"><?= __('Custom fields'); ?></span>
                    <div class="filter-values-container">
                        <?php foreach ($custom_fields as $item): ?>
                            <a href="javascript:void(0);" class="list-item filtervalue multiline <?php if (array_key_exists($item->getKey(), $visible_fields)) echo 'disabled'; ?>" data-issue-field data-id="<?= $item->getKey(); ?>" data-url="<?= make_url('configure_issuetypes_scheme_field', ['key' => $item->getKey(), 'scheme_id' => $scheme->getId(), 'issue_type_id' => $issue_type->getId()]); ?>">
                                <span class="icon"><?= fa_image_tag('tag'); ?></span>
                                <span class="name">
                                    <span class="title value"><?= $item->getName(); ?></span>
                                    <?php if ($item->getDescription() != '' && $item->getName() != $item->getDescription()): ?>
                                        <span class="description"><?= $item->getDescription(); ?></span>
                                    <?php endif; ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <span class="list-item separator"></span>
                    <a href="javascript:void(0);" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_issuefield', 'scheme_id' => $scheme->getId(), 'issue_type_id' => $issue_type->getId()]); ?>');" class="list-item">
                        <span class="icon"><?= fa_image_tag('plus'); ?></span>
                        <span class="name"><?= __('Create new field'); ?></span>
                    </a>
                </span>
            </span>
        </span>
    </span>
</h5>
<div class="form-container">
    <form action="<?= make_url('configure_issuetypes_scheme_options_post', ['issue_type_id' => $issue_type->getID(), 'scheme_id' => $scheme->getID()]); ?>" data-simple-submit id="edit_issuetype_scheme_options_<?= $issue_type->getId(); ?>">
        <div class="configurable-components-list" id="issue-type-fields-list">
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
