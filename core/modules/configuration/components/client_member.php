<?php

    use pachno\core\entities\User;
    use pachno\core\entities\Client;

    /**
     * @var User $user
     * @var Client $client
     */

?>
<div class="row" data-client-member data-user-id="<?= $user->getId() ;?>">
    <div class="column name-container">
        <?php include_component('main/userdropdown', ['user' => $user, 'size' => 'small']); ?>
        <?php if (!$user->isActivated()): ?>
            <span class="count-badge"><?= fa_image_tag('envelope-open-text', ['class' => 'icon']); ?><span><?= __('Invitation sent'); ?></span></span>
        <?php endif; ?>
    </div>
    <div class="column roles"></div>
    <div class="column actions">
        <button class="secondary danger icon" onclick="Pachno.UI.Dialog.show(
            '<?= __('Remove %username from this client?', array('%username' => $user->getName())); ?>',
            '<?= __('Please confirm removal from the client'); ?>',
            {
                yes: {click: function() { Pachno.trigger(Pachno.EVENTS.client.removeUser, { url: '<?= make_url('configure_client_remove_member', ['client_id' => $client->getID(), 'user_id' => $user->getID()]); ?>'})}},
                no: {click: Pachno.UI.Dialog.dismiss}
            }
        );"><?= fa_image_tag('times'); ?></button>
    </div>
</div>
