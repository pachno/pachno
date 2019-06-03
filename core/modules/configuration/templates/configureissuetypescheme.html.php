<?php

    /**
     * @var \pachno\core\entities\IssuetypeScheme $scheme
     * @var \pachno\core\entities\Issuetype[] $issue_types
     */

    $pachno_response->setTitle(__('Configure issue type scheme %scheme_name', ['%scheme_name' => $scheme->getName()]));

?>
<div class="content-with-sidebar">
    <?php include_component('leftmenu', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_ISSUETYPE_SCHEMES]); ?>
    <div class="configuration-container">
        <div class="configuration-content centered">
            <h1><?php echo __('Configure issue type scheme %scheme_name', ['%scheme_name' => $scheme->getName()]); ?></h1>
            <div class="form-container">
                <form action="<?= make_url('configure_issuetypes_scheme', ['scheme_id' => $scheme->getId()]); ?>" onsubmit="Pachno.Config.IssuetypeScheme.save(this);return false;">
                    <div class="configurable-components-list">
                        <?php foreach ($issue_types as $type): ?>
                            <?php include_component('schemeissuetype', ['type' => $type, 'scheme' => $scheme, 'builtin_fields' => $builtin_fields, 'custom_fields' => $custom_fields]); ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-row submit-container">
                        <a class="button secondary" href="<?= make_url('configure_issuetypes_schemes'); ?>"><?= __('Cancel'); ?></a>
                        <button type="submit" class="button primary"><?= __('Save scheme configuration'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
