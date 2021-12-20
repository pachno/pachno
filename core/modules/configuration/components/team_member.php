<?php

    use pachno\core\entities\User;
    use pachno\core\entities\Team;

    /**
     * @var User $user
     * @var Team $team
     */

?>
<div class="row" data-team-member data-user-id="<?= $user->getId() ;?>">
    <div class="column name-container">
        <?php include_component('main/userdropdown', ['user' => $user, 'size' => 'small']); ?>
        <?php if (!$user->isActivated()): ?>
            <span class="count-badge"><?= fa_image_tag('envelope-open-text', ['class' => 'icon']); ?><span><?= __('Invitation sent'); ?></span></span>
        <?php endif; ?>
    </div>
    <div class="column roles"></div>
    <div class="column actions">
        <button class="secondary danger icon" onclick="Pachno.UI.Dialog.show(
            '<?= __('Remove %username from this team?', array('%username' => $user->getName())); ?>',
            '<?= __('Please confirm removal from the team'); ?>',
            {
                yes: {click: function() { Pachno.trigger(Pachno.EVENTS.team.removeUser, { url: '<?= make_url('configure_team_members', ['team_id' => $team->getID()]); ?>', user_id: <?= $user->getID(); ?>})}},
                no: {click: Pachno.UI.Dialog.dismiss}
            }
        );"><?= fa_image_tag('times'); ?></button>
    </div>
</div>
