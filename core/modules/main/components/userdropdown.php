<?php

    use pachno\core\entities\User;

    /**
     * @var User $user
     * @var bool $show_avatar
     * @var bool $show_name
     * @var ?string $size
     * @var ?string $displayname
     */

?>
<?php if (!$user instanceof User || $user->getID() == 0 || $user->isDeleted()): ?>
    <span class="faded_out"><?php echo __('No such user'); ?></span>
<?php elseif (!$user->isScopeConfirmed()): ?>
    <span class="faded_out" title="<?php echo __('This user has not been confirmed yet'); ?>"><?php echo $user->getUsername() ?></span>
<?php else: ?>
    <span class="userlink trigger-backdrop" data-url="<?php echo $user->getUserCardUrl(); ?>">
        <?php if (!isset($userstate) || $userstate): ?><span class="userstate"><?php echo pachno_get_userstate_image($user); ?></span><?php endif; ?>
        <?php if ($show_avatar): ?>
            <?php $extraClass = $size ?? "small"; ?>
            <?php echo image_tag($user->getAvatarURL(), array('alt' => ' ', 'class' => 'avatar '.$extraClass), true); ?>
        <?php endif; ?>
        <?php if ($show_name): ?>
            <span class="name"><?php echo $displayname ?? $user->getName(); ?></span>
        <?php endif; ?>
    </span>
<?php endif; ?>
