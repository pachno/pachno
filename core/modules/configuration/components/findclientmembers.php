<?php

    use pachno\core\entities\Client;
    use pachno\core\entities\User;

    /**
     * @var User $email_user
     * @var User[] $users
     * @var Client $client
     */

?>
<?php if (!count($users) && isset($email_user)): ?>
    <div class="helper-text">
        <div class="image-container"><?= image_tag('/unthemed/onboarding_invite_email.png', [], true); ?></div>
        <span class="description">
            <?= __('There are no users registered with that email address. Send them an invite below!'); ?>
        </span>
    </div>
    <div class="flexible-table" data-url="<?php echo make_url('configure_client_members', ['client_id' => $client->getID()]); ?>">
        <div class="row">
            <div class="column header name-container"><?= __('Invite user'); ?></div>
        </div>
        <div class="row" data-email="<?= $email_user->getEmail(); ?>">
            <div class="column name-container">
                <?php echo image_tag($email_user->getAvatarURL(16), ['class' => 'avatar small'], true); ?>
                <span><?php echo $email_user->getEmail(); ?></span>
                <span class="count-badge tooltip-container">
                    <?= fa_image_tag('envelope-open-text', ['class' => 'icon']); ?><span><?= __('Not registered'); ?></span>
                    <span class="tooltip">
                        <span><?= __('An email will be sent to this user with information on how to log in'); ?></span>
                    </span>
                </span>
            </div>
            <div class="column"></div>
            <div class="column actions">
                <button class="button secondary trigger-assign-to-project">
                    <span class="name"><?php echo __('Invite'); ?></span>
                    <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']); ?>
                </button>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="helper-text">
        <div class="image-container"><?= image_tag('/unthemed/onboarding_project_add_to_team.png', [], true); ?></div>
        <span class="description">
            <?= __('We found someone that matched what you searched for'); ?>
        </span>
    </div>
    <div class="flexible-table" data-url="<?php echo make_url('configure_client_members', ['client_id' => $client->getID()]); ?>">
        <div class="row">
            <div class="column header name-container"><?= __('Name'); ?></div>
            <div class="column header"></div>
            <div class="column header actions"></div>
        </div>
        <?php foreach ($users as $user): ?>
            <div class="row" data-id="<?= $user->getId(); ?>" data-identifiable-type="user">
                <div class="column name-container">
                    <?php echo image_tag($user->getAvatarURL(16), ['class' => 'avatar small'], true); ?>
                    <?php if ($user->isActivated()): ?>
                        <span><?php echo $user->getNameWithUsername(); ?></span>
                    <?php else: ?>
                        <span><?php echo $user->getUsername(); ?></span>
                        <span class="count-badge"><?= fa_image_tag('envelope-open-text', ['class' => 'icon']); ?><span><?= __('Not activated yet'); ?></span></span>
                    <?php endif; ?>
                </div>
                <div class="column"></div>
                <div class="column actions tooltip-container">
                    <?php if ($user->isMemberOfClient($client)): ?>
                        <button class="button secondary" disabled>
                            <span class="name"><?= fa_image_tag('check'); ?></span>
                        </button>
                        <div class="tooltip from-bottom from-right">
                            <span><?= __('This user is already a member of this client'); ?></span>
                        </div>
                    <?php else: ?>
                        <button class="button secondary trigger-assign-to-client">
                            <span class="name"><?php echo __('Add'); ?></span>
                            <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']); ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
