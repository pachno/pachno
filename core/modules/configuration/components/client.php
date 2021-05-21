<?php

    use pachno\core\entities\Client;

    /**
     * @var Client $client
     */

?>
<div class="row" id="configure-client-<?php echo $client->getID(); ?>">
    <div class="column info-icons"><?= fa_image_tag('users'); ?></div>
    <div class="column name-container">
        <span><?php echo $client->getName(); ?></span>
    </div>
    <div class="column numeric"><span class="count-badge"><?= $client->getNumberOfMembers(); ?></span></div>
    <div class="column actions">
        <div class="dropper-container">
            <button class="dropper button secondary">
                <span><?= __('Actions'); ?></span>
                <?= fa_image_tag('angle-down', ['class' => 'icon']); ?>
            </button>
            <div class="dropdown-container">
                <div class="list-mode">
                    <a href="javascript:void(0);" class="list-item trigger-backdrop" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'edit_client', 'client_id' => $client->getId()]); ?>">
                        <?= fa_image_tag('edit', ['class' => 'icon']); ?>
                        <span class="name"><?php echo __('Edit'); ?></span>
                    </a>
                    <div class="list-item disabled" data-url="<?= make_url('configure_client', ['client_id' => $client->getID()]); ?>?clone=1">
                        <?= fa_image_tag('copy', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Copy this client'); ?></span>
                    </div>
                    <div class="list-item separator"></div>
                    <a href="javascript:void(0);" onclick="Pachno.UI.Dialog.show('<?= __('Do you really want to delete this client?'); ?>', '<?= __('If you delete this client, then all users in this client will be disabled until moved to a different client'); ?>', {yes: {click: function() { Pachno.trigger(Pachno.EVENTS.client.delete, { url: '<?= make_url('configure_client', ['client_id' => $client->getID()]); ?>', client_id: <?= $client->getID(); ?> }); }}, no: { click: Pachno.UI.Dialog.dismiss }});" class="list-item danger">
                        <?= fa_image_tag('times', ['class' => 'icon']); ?>
                        <span><?= __('Delete'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
