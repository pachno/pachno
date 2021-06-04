<?php

    use pachno\core\framework\Response;
    use pachno\core\entities\Client;

    /**
     * @var Response $pachno_response
     * @var Client[] $clients
     */

    $pachno_response->setTitle(__('Configure clients'));

?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_CLIENTS]); ?>
    <div class="configuration-container">
        <div class="configuration-content">
            <h1>
                <span><?php echo __('Configure clients'); ?></span>
            </h1>
            <div class="helper-text">
                <div class="image-container"><?= image_tag('/unthemed/onboarding_configure_clients_icon.png', [], true); ?></div>
                <span class="description">
                    <?php echo __("Clients let you group external users separate from your internal teams. Clients also have extra information available to help you organize contact details. Read about clients and permissions in the %online_documentation to learn more about how to create, apply and manage clients.", array('%online_documentation' => link_tag('https://projects.pachno.com/pachno/docs/UserClients', '<b>'.__('online documentation').'</b>'))); ?>
                </span>
            </div>
            <h3><span><?php echo __('User clients'); ?></span></h3>
            <div class="flexible-table">
                <div class="row header">
                    <div class="column header info-icons"></div>
                    <div class="column header name-container"><?= __('Client name'); ?></div>
                    <div class="column header numeric"><?= __('User(s)'); ?></div>
                    <div class="column header actions"></div>
                </div>
                <div class="body" id="clients-list-container">
                    <?php foreach ($clients as $client): ?>
                        <?php include_component('configuration/client', ['client' => $client]); ?>
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
            case 'edit_client_form':
                let $existing_row = $(`[data-client][data-client-id=${json.client.id}]`);
                if ($existing_row.length) {
                    $existing_row.replaceWith(json.component);
                } else {
                    $('#clients-list-container').append(json.component);
                }
                break;
        }
    });

    Pachno.on(Pachno.EVENTS.client.delete, function (PachnoApplication, data) {
        $(`[data-client][data-client-id="${data.client_id}"]`).remove();
        Pachno.UI.Dialog.setSubmitting();

        Pachno.fetch(data.url, { method: 'DELETE' })
            .then(json => {
                Pachno.UI.Dialog.dismiss();
            });
    });
</script>