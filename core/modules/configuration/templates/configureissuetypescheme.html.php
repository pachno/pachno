<?php

    use \pachno\core\framework\Settings;

    /**
     * @var \pachno\core\entities\IssuetypeScheme $scheme
     * @var \pachno\core\entities\Issuetype[] $issue_types
     */

    $pachno_response->setTitle(__('Configure issue type scheme %scheme_name', ['%scheme_name' => $scheme->getName()]));
    $selected_section = ($number_of_schemes == 1) ? Settings::CONFIGURATION_SECTION_ISSUETYPES : Settings::CONFIGURATION_SECTION_ISSUETYPE_SCHEMES;

?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => $selected_section]); ?>
    <div class="configuration-container">
        <div class="configuration-content centered">
            <div class="form-container">
                <form action="<?= make_url('configure_issuetypes_scheme', ['scheme_id' => $scheme->getId()]); ?>" onsubmit="Pachno.Config.IssuetypeScheme.save(this);return false;" data-interactive-form data-interactive-form-method="Pachno.Config.IssuetypeScheme.save">
                    <div class="form-row">
                        <input type="text" name="name" value="<?= $scheme->getName(); ?>" class="invisible" id="scheme_<?= $scheme->getID(); ?>_name_input">
                        <label for="scheme_<?= $scheme->getID(); ?>_name_input"><?= __('Scheme name'); ?><?= fa_image_tag('spinner', ['class' => 'fa-spin submit-indicator icon']); ?></label>
                    </div>
                    <div class="form-row error-container">
                        <div class="error"></div>
                    </div>
                </form>
            </div>
            <div class="configurable-components-container" id="issue-type-configuration-container">
                <div class="configurable-components-list-container">
                    <h3><?php echo __('Issue types'); ?></h3>
                    <div class="configurable-components-list" id="issue-types-list">
                        <?php foreach ($issue_types as $type): ?>
                            <?php include_component('schemeissuetype', ['type' => $type, 'scheme' => $scheme]); ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="configurable-components-list">
                        <a class="configurable-component" href="javascript:void(0);" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_issuetype', 'scheme_id' => $scheme->getId()]); ?>');">
                            <span class="row">
                                <span class="icon"><?= fa_image_tag('plus'); ?></span>
                                <span class="name">
                                    <span class="title"><?= __('Create issue type'); ?></span>
                                </span>
                            </span>
                        </a>
                    </div>
                </div>
                <div class="configurable-component-options" id="selected-issue-type-options"></div>
            </div>
        </div>
    </div>
</div>
<script>
    require(['domReady', 'pachno/index', 'jquery'], function (domReady, pachno_index_js, jQuery) {
        domReady(function () {
            jQuery('body').on('click', '.issue-type-scheme-issue-type .open', function(event) {
                event.preventDefault();
                event.stopPropagation();

                const $item = jQuery(this).parents('.issue-type-scheme-issue-type');
                pachno_index_js.Config.IssuetypeScheme.showOptions($item);
            });

            jQuery('body').on('click', '.list-item[data-issue-field]:not(.disabled)', function(event) {
                const key = jQuery(this).data('id'),
                    url = jQuery(this).data('url');

                pachno_index_js.Config.IssuetypeScheme.addField(url, key);
            });

            jQuery('body').on('click', '.configurable-component[data-issue-field] .remove-item', function(event) {
                const $item = jQuery(this).parents('.configurable-component'),
                    key = $item.data('id');

                $item.remove();
                jQuery('.list-item[data-issue-field][data-id=' + key + ']').removeClass('disabled');
            });
        });
    });
</script>
