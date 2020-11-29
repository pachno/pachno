<?php if (!$user instanceof \pachno\core\entities\User || $user->getID() == 0 || $user->isDeleted()): ?>
    <span class="faded_out"><?php echo __('No such user'); ?></span>
<?php elseif (!$user->isScopeConfirmed()): ?>
    <span class="faded_out" title="<?php echo __('This user has not been confirmed yet'); ?>"><?php echo $user->getUsername() ?></span>
<?php else: ?>
<a href="javascript:void(0);" class="userlink trigger-backdrop <?php if ($pachno_user->isFriend($user)): ?>friend<?php endif; ?>" data-url="<?php echo make_url('get_partial_for_backdrop', ['key' => 'usercard', 'user_id' => $user->getID()]); ?>">
    <?php if (!isset($userstate) || $userstate): ?><span class="userstate"><?php echo pachno_get_userstate_image($user); ?></span><?php endif; ?>
    <?php if ($show_avatar): ?>
        <?php $extraClass = (isset($size)) ? $size : ""; ?>
        <?php echo image_tag($user->getAvatarURL(), array('alt' => ' ', 'class' => 'avatar '.$extraClass), true); ?>
    <?php endif; ?>
    <?php echo (isset($displayname)) ? $displayname : $user->getName(); ?>
</a>
<div class="dropper-container" style="display: none;">
    <div class="dropdown-container from-left">
        <div class="list-mode">
            <div class="header-banner">
                <div class="header-name">
                    <div class="image-container">
                        <?php echo image_tag($user->getAvatarURL(false), array('alt' => ' ', 'style' => "width: 36px; height: 36px;"), true); ?>
                    </div>
                    <div class="name-container">
                        <span><?php echo $user->getRealname(); ?></span>
                        <span class="info-container">@<?= $user->getUsername(); ?></span>
                    </div>
                </div>
            </div>
            <div class="list-item disabled">
                <?php if(!$user->getLastSeen()): ?>
                    <span class="name"><?php echo __('This user has not logged in yet'); ?></span>
                <?php else: ?>
                    <span class="name"><?php echo __('Last seen online at %time', ['%time' => \pachno\core\framework\Context::getI18n()->formatTime($user->getLastSeen(), 11)]); ?></span>
                <?php endif; ?>
            </div>
            <div class="list-item separator"></div>
            <?php \pachno\core\framework\Event::createNew('core', 'useractions_top', $user)->trigger(); ?>
            <?php if (\pachno\core\entities\User::isThisGuest() == false && $user->getID() != $pachno_user->getID()): ?>
                <div class="list-item" style="<?php if ($pachno_user->isFriend($user)): ?> display: none;<?php endif; ?>" id="add_friend_<?php echo $user->getID() . '_' . $rnd_no; ?>">
                    <?php echo javascript_link_tag('<span class="name">'.__('Become friends').'</span>', array('onclick' => "Pachno.Main.Profile.addFriend('".make_url('toggle_friend', array('mode' => 'add', 'user_id' => $user->getID()))."', {$user->getID()}, {$rnd_no});")); ?>
                </div>
                <?php echo image_tag('spinning_16.gif', array('id' => "toggle_friend_{$user->getID()}_{$rnd_no}_indicator", 'style' => 'display: none;')); ?>
                <div class="list-item" style="<?php if (!$pachno_user->isFriend($user)): ?> display: none;<?php endif; ?>" id="remove_friend_<?php echo $user->getID() . '_' . $rnd_no; ?>">
                    <?php echo javascript_link_tag('<span class="name">'.__('Remove this friend').'</span>', array('onclick' => "Pachno.Main.Profile.removeFriend('".make_url('toggle_friend', array('mode' => 'remove', 'user_id' => $user->getID()))."', {$user->getID()}, {$rnd_no});")); ?>
                </div>
            <?php endif; ?>
            <?php if ($pachno_user->canAccessConfigurationPage(\pachno\core\framework\Settings::CONFIGURATION_SECTION_USERS)): ?>
                <?php if ($pachno_routing->getCurrentRoute()->getName() != 'configure_users_find_user'): ?>
                    <a class="list-item" href="<?php echo make_url('configure_users'); ?>?finduser=<?php echo $user->getUsername(); ?>">
                        <span class="icon"><?= fa_image_tag('edit'); ?></span>
                        <span class="name"><?php echo __('Edit this user'); ?></span>
                    </a>
                <?php endif; ?>
                <?php if (!$pachno_request->hasCookie('original_username')): ?>
                    <a class="list-item" href="<?= make_url('auth_switch_to_user', array('user_id' => $user->getID())); ?>">
                        <?= fa_image_tag('random', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Switch to this user'); ?></span>
                    </a>
                <?php else: ?>
                    <a class="list-item" href="<?= make_url('switch_back_user'); ?>">
                        <?= fa_image_tag('random', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Switch back to original user'); ?></span>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            <a class="list-item" href="javascript:void(0);" onclick="Pachno.UI.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $user->getID())); ?>');$('#bud_<?php echo $user->getUsername() . "_" . $rnd_no; ?>').hide();">
                <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                <span class="name"><?php echo __('Show user details'); ?></span>
            </a>
            <?php \pachno\core\framework\Event::createNew('core', 'useractions_bottom', $user)->trigger(); ?>
        </div>
    </div>
</div>
<?php endif; ?>
