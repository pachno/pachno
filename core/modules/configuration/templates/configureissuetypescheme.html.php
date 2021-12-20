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
                        <a class="configurable-component trigger-backdrop" href="javascript:void(0);" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'edit_issuetype', 'scheme_id' => $scheme->getId()]); ?>">
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
    Pachno.on(Pachno.EVENTS.ready, function () {
        // $('body').on('click', '.list-item[data-issue-field]:not(.disabled)', function(event) {
        //     const key = $(this).data('id'),
        //         url = $(this).data('url');
        //
        //     pachno_index_js.Config.IssuetypeScheme.addField(url, key);
        // });

        $('body').on('click', '.configurable-component[data-issue-field] .remove-item', function(event) {
            const $item = $(this).parents('.configurable-component'),
                key = $item.data('id');

            $item.remove();
            $('.list-item[data-issue-field][data-id=' + key + ']').removeClass('disabled');
        });
    });
</script>
