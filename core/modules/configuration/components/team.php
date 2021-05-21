<?php

    use pachno\core\entities\Team;
    use pachno\core\framework\Settings;

    /**
     * @var Team $team
     */

?>
<div class="row" id="configure-team-<?php echo $team->getID(); ?>" data-team data-team-id="<?= $team->getID(); ?>">
    <div class="column info-icons"><?= fa_image_tag('users'); ?></div>
    <div class="column name-container">
        <span><?php echo $team->getName(); ?></span>
    </div>
    <div class="column numeric"><span class="count-badge"><?= $team->getNumberOfMembers(); ?></span></div>
    <div class="column actions">
        <div class="dropper-container">
            <button class="dropper button secondary">
                <span><?= __('Actions'); ?></span>
                <?= fa_image_tag('angle-down', ['class' => 'icon']); ?>
            </button>
            <div class="dropdown-container">
                <div class="list-mode">
                    <a href="javascript:void(0);" class="list-item trigger-backdrop" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'edit_team', 'team_id' => $team->getId()]); ?>">
                        <?= fa_image_tag('edit', ['class' => 'icon']); ?>
                        <span class="name"><?php echo __('Edit'); ?></span>
                    </a>
                    <div class="list-item disabled" data-url="<?= make_url('configure_users_clone_team', ['team_id' => $team->getID()]); ?>">
                        <?= fa_image_tag('copy', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Copy this team'); ?></span>
                    </div>
                    <div class="list-item separator"></div>
                    <a href="javascript:void(0);" onclick="Pachno.UI.Dialog.show('<?= __('Do you really want to delete this team?'); ?>', '<?= __('If you delete this team, then all users in this team will be disabled until moved to a different team'); ?>', {yes: {click: function() { Pachno.trigger(Pachno.EVENTS.team.delete, { url: '<?= make_url('configure_users_delete_team', ['team_id' => $team->getID()]); ?>', team_id: <?= $team->getID(); ?> }); }}, no: { click: Pachno.UI.Dialog.dismiss }});" class="list-item danger">
                        <?= fa_image_tag('times', ['class' => 'icon']); ?>
                        <span><?= __('Delete'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
