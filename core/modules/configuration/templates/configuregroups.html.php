<?php

    use pachno\core\framework\Response;
    use pachno\core\entities\Group;

    /**
     * @var Response $pachno_response
     * @var Group[] $groups
     */

    $pachno_response->setTitle(__('Configure groups'));

?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_USERS]); ?>
    <div class="configuration-container">
        <div class="configuration-content">
            <h1>
                <span><?php echo __('Configure groups'); ?></span>
            </h1>
            <div class="helper-text">
                <div class="image-container"><?= image_tag('/unthemed/onboarding_configure_groups_icon.png', [], true); ?></div>
                <span class="description">
                    <?php echo __("User groups define basic access permissions for all users, allowing you to restrict or grant access to certain parts of the system not linked to specific projects. Read about groups and permissions in the %online_documentation to learn more about how to create, apply and manage groups.", array('%online_documentation' => link_tag('https://projects.pachno.com/pachno/docs/UserGroups', '<b>'.__('online documentation').'</b>'))); ?>
                </span>
            </div>
            <h3><span><?php echo __('User groups'); ?></span></h3>
            <div class="flexible-table">
                <div class="row header">
                    <div class="column header info-icons"></div>
                    <div class="column header name-container"><?= __('Group name'); ?></div>
                    <div class="column header numeric"><?= __('User(s)'); ?></div>
                    <div class="column header actions"></div>
                </div>
                <div class="body" id="groups-list-container">
                    <?php foreach ($groups as $group): ?>
                        <?php include_component('configuration/group', ['group' => $group]); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    Pachno.on(Pachno.EVENTS.formSubmitResponse, function (PachnoApplication, data) {
        public const json = data.json;
        switch (data.form) {
            case 'edit_group_form':
                let $existing_row = $(`[data-group][data-group-id=${json.group.id}]`);
                if ($existing_row.length) {
                    $existing_row.replaceWith(json.component);
                } else {
                    $('#groups-list-container').append(json.component);
                }
                break;
        }
    });

    Pachno.on(Pachno.EVENTS.group.delete, function (PachnoApplication, data) {
        $(`[data-group][data-group-id="${data.group_id}"]`).remove();
        Pachno.UI.Dialog.setSubmitting();

        Pachno.fetch(data.url, { method: 'DELETE' })
            .then(json => {
                Pachno.UI.Dialog.dismiss();
            });
    });
</script>