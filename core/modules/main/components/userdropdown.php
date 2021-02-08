<?php if (!$user instanceof \pachno\core\entities\User || $user->getID() == 0 || $user->isDeleted()): ?>
    <span class="faded_out"><?php echo __('No such user'); ?></span>
<?php elseif (!$user->isScopeConfirmed()): ?>
    <span class="faded_out" title="<?php echo __('This user has not been confirmed yet'); ?>"><?php echo $user->getUsername() ?></span>
<?php else: ?>
<span class="userlink trigger-backdrop <?php if ($pachno_user->isFriend($user)): ?>friend<?php endif; ?>" data-url="<?php echo make_url('get_partial_for_backdrop', ['key' => 'usercard', 'user_id' => $user->getID()]); ?>">
    <?php if (!isset($userstate) || $userstate): ?><span class="userstate"><?php echo pachno_get_userstate_image($user); ?></span><?php endif; ?>
    <?php if ($show_avatar): ?>
        <?php $extraClass = (isset($size)) ? $size : "small"; ?>
        <?php echo image_tag($user->getAvatarURL(), array('alt' => ' ', 'class' => 'avatar '.$extraClass), true); ?>
    <?php endif; ?>
    <?php if ($show_name): ?>
        <span class="name"><?php echo (isset($displayname)) ? $displayname : $user->getName(); ?></span>
    <?php endif; ?>
</span>
<?php endif; ?>
