<?php

    /**
 * @var \pachno\core\entities\Project $project
 */
    
    use pachno\core\framework\Context;

?>
<div class="form-container">
    <form
            accept-charset="<?= Context::getI18n()->getCharset(); ?>"
            data-submit-project-settings
            data-project-id="<?= $project->getID(); ?>"
            action="<?= make_url('configure_project_settings', ['project_id' => $project->getID()]); ?>"
            method="post"
            id="project_client_information"
            data-interactive-form
    >
        <div class="form-row">
            <h3>
                <span><?= __('Client'); ?></span>
                <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                    <button class="button secondary" onclick="Pachno.UI.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', ['key' => 'edit_client', 'project_id' => $project->getID()]); ?>');">
                        <?= fa_image_tag('plus', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Create client'); ?></span>
                    </button>
                <?php endif; ?>
            </h3>
            <div class="helper-text">
                <div class="image-container"><?= image_tag('/unthemed/onboarding_project_client_icon.png', [], true); ?></div>
                <span class="description">
                    <?= __('If this project is related to an external client, add that client here to grant access.'); ?>
                </span>
            </div>
        </div>
        <div class="form-row">
            <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                <div class="fancy-dropdown-container">
                    <div class="fancy-dropdown">
                        <label><?= __('Client'); ?></label>
                        <span class="value"><?= __('No client assigned'); ?></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode" id="project_client_list">
                            <input type="radio" class="fancy-checkbox" id="client_id_checkbox_0" name="client_id" value="0" <?php if (!$project->hasClient()) echo 'checked'; ?>>
                            <label for="client_id_checkbox_0" class="list-item">
                                <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                <span class="name value"><?= __('No client assigned'); ?></span>
                            </label>
                            <?php if (count(\pachno\core\entities\Client::getAll())): ?>
                                <?php foreach (\pachno\core\entities\Client::getAll() as $client): ?>
                                    <input type="radio" class="fancy-checkbox" id="client_id_checkbox_<?= $client->getID(); ?>" name="client_id" value="<?= $client->getID(); ?>" <?php if ($project->hasClient() && $project->getClient()->getID() == $client->getID()) echo 'checked'; ?> data-client data-client-id="<?= $client->getId(); ?>">
                                    <label for="client_id_checkbox_<?= $client->getID(); ?>" class="list-item">
                                        <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                        <span class="name value"><?= $client->getName(); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <input type="radio" class="fancy-checkbox" id="client_id_checkbox_0" name="client_id" value="0">
                                <label for="client_id_checkbox_0" class="list-item disabled">
                                    <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                    <span class="name value"><?= __('No clients exist'); ?></span>
                                </label>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php if ($project->getClient() == null): echo __('No client'); else: echo $project->getClient()->getName(); endif; ?>
                <label for="client"><?= __('Client'); ?></label>
            <?php endif; ?>
        </div>
    </form>
</div>
<script>
    Pachno.on(Pachno.EVENTS.formSubmitResponse, function (PachnoApplication, data) {
        const json = data.json;
        switch (data.form) {
            case 'edit_client_form':
                const $no_client_input = $('#client_id_checkbox_0');
                const $no_client_label = $('label[for=client_id_checkbox_0]');
                
                if ($no_client_input.length) {
                    $no_client_input.remove();
                    $no_client_label.remove();
                }
                let $existing_row = $(`[data-client][data-client-id=${json.client.id}]`);
                const html = `<input type="radio" class="fancy-checkbox" id="client_id_checkbox_${json.client.id}" name="client_id" value="${json.client.id}" data-client data-client-id="${json.client.id}">
                    <label for="client_id_checkbox_${json.client.id}" class="list-item">
                        <span class="icon">${Pachno.UI.fa_image_tag('check-circle', {classes: ['checked']}, 'far')}${Pachno.UI.fa_image_tag('circle', {classes: ['unchecked']}, 'far')}</span>
                        <span class="name value">${json.client.name}</span>
                    </label>`;
                
                if ($existing_row.length) {
                    $existing_row.replaceWith(html);
                } else {
                    $('#project_client_list').append(html);
                }
                break;
        }
    });
</script>