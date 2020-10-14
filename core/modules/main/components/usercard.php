<?php

    /** @var \pachno\core\entities\User $user */

?>
<div class="backdrop_box medium usercard" id="user_details_popup">
    <div class="backdrop_detail_header">
        <div class="avatar-container">
            <?php echo image_tag($user->getAvatarURL(false), array('alt' => ' '), true); ?>
        </div>
        <span><?php echo (!$user->isScopeConfirmed()) ? $user->getUsername() : $user->getRealname(); ?></span>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?php if (!$user->isScopeConfirmed()): ?>
            <div class="user_details">
                <div class="user_realname">
                    <?php echo $user->getUsername(); ?>
                    <div class="user_status"><?php echo __('This user has not been confirmed yet'); ?></div>
                </div>
            </div>
        <?php else: ?>
            <div class="list-mode">
                <div class="list-item">
                    <?= fa_image_tag('at', ['class' => 'icon']); ?>
                    <span class="name"><?php echo $user->getUsername(); ?></span>
                </div>
                <div class="list-item">
                    <span class="icon"><?php echo pachno_get_userstate_image($user); ?></span>
                    <span class="name"><?= __($user->getState()->getName()); ?></span>
                </div>
                <?php if ($user->isEmailPublic() || $pachno_user->canAccessConfigurationPage(\pachno\core\framework\Settings::CONFIGURATION_SECTION_USERS)): ?>
                    <div class="list-item">
                        <?= fa_image_tag('envelope', ['class' => 'icon']); ?>
                        <span class="name"><?php echo link_tag('mailto:'.$user->getEmail(), $user->getEmail()); ?></span>
                    </div>
                <?php endif; ?>
                <?php if (\pachno\core\entities\User::isThisGuest() == false): ?>
                    <div id="friends_message_<?php echo $user->getUsername() . '_' . $rnd_no; ?>" style="padding: 10px 0 0 0; font-size: 0.75em;"></div>
                    <?php if ($user->getID() != \pachno\core\framework\Context::getUser()->getID() && !(\pachno\core\framework\Context::getUser()->isFriend($user)) && !$user->isGuest()): ?>
                            <div id="friends_link_<?php echo $user->getUsername() . '_' . $rnd_no; ?>" class="friends_link">
                        <span style="padding: 2px; <?php if (\pachno\core\framework\Context::getUser()->isFriend($user)): ?> display: none;<?php endif; ?>" id="add_friend_<?php echo $user->getID() . '_' . $rnd_no; ?>">
                            <?php echo javascript_link_tag(__('Become friends'), array('onclick' => "Pachno.Main.Profile.addFriend('".make_url('toggle_friend', array('mode' => 'add', 'user_id' => $user->getID()))."', {$user->getID()}, {$rnd_no});")); ?>
                        </span>
                                <?php echo image_tag('spinning_16.gif', array('id' => "toggle_friend_{$user->getID()}_{$rnd_no}_indicator", 'style' => 'display: none;')); ?>
                                <span style="padding: 2px; <?php if (!\pachno\core\framework\Context::getUser()->isFriend($user)): ?> display: none;<?php endif; ?>" id="remove_friend_<?php echo $user->getID() . '_' . $rnd_no; ?>">
                            <?php echo javascript_link_tag(__('Remove this friend'), array('onclick' => "Pachno.Main.Profile.removeFriend('".make_url('toggle_friend', array('mode' => 'remove', 'user_id' => $user->getID()))."', {$user->getID()}, {$rnd_no});")); ?>
                        </span>
                            </div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($pachno_user->canAccessConfigurationPage(\pachno\core\framework\Settings::CONFIGURATION_SECTION_USERS)): ?>
                    <div class="list-item">
                        <form action="<?php echo make_url('configure_users'); ?>">
                            <input type="hidden" name="finduser" value="<?php echo $user->getUsername(); ?>">
                            <a href="javascript:void(0);" onclick="$(this).up('form').submit();"><?php echo __('Edit this user'); ?></a>
                        </form>
                    </div>
                <?php endif; ?>
                <?php if (!$user->getJoinedDate()): ?>
                    <div class="list-item disabled">
                        <span class="name"><?php echo __('This user has been a member for a while'); ?></span>
                    </div>
                <?php else: ?>
                    <div class="list-item">
                        <span class="name"><?php echo __('This user has been a member since %date', ['%date' => \pachno\core\framework\Context::getI18n()->formatTime($user->getJoinedDate(), 11)]); ?></span>
                    </div>
                <?php endif; ?>
                <?php if (!$user->getLastSeen()): ?>
                    <div class="list-item disabled">
                        <span class="name"><?php echo __('This user has not logged in yet'); ?></span>
                    </div>
                <?php else: ?>
                    <div class="list-item">
                        <span class="name"><?php echo __('This user was last seen online at %time', ['%time' => \pachno\core\framework\Context::getI18n()->formatTime($user->getLastSeen(), 11)]); ?></span>
                    </div>
                <?php endif; ?>
                <?php if (!$user->getLatestActions(1)): ?>
                    <div class="list-item disabled">
                        <span class="name"><?php echo __('There is no recent activity available for this user'); ?></span>
                    </div>
                <?php else: ?>
                    <?php foreach ($user->getLatestActions(1) as $action): ?>
                        <div class="list-item">
                            <span class="name"><?php echo __('Last user activity was at %time', ['%time' => \pachno\core\framework\Context::getI18n()->formatTime($action->getTime(), 11)]); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="header">
                    <h3><?= __('Recently reported issues'); ?></h3>
                </div>
                <?php if (count($issues)): ?>
                    <?php echo __('This user has reported %issues issue(s)', array('%issues' => '<b>'.count($issues).'</b>')); ?>
                    <?php echo link_tag(make_url('search', array('search' => true, 'fs[posted_by]' => array('o' => '=', 'v' => $user->getID()))), __('Show issues'), array('class' => 'button', 'title' => __('Show issues reported by this user'))); ?>
                    <?php $seen = 0; ?>
                    <?php foreach ($issues as $issue): ?>
                        <?php if ($issue->hasAccess()): ?>
                            <a class="list-item" href="<?= make_url('project_dashboard', array('project_key' => $issue->getProject()->getKey())); ?>">
                                <?= image_tag($issue->getProject()->getIconName(), ['class' => 'icon issuelog-project-logo'], true); ?>
                                <span class="name"><?php echo pachno_truncateText($issue->getFormattedTitle(true), 100); ?></span>
                            </a>
                            <?php if (++$seen == 7) break; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="list-item disabled"><span class="name"><?php echo __('This user has not reported any issues yet'); ?></span></div>
                <?php endif; ?>
                <?php if (count($user->getTeams())): ?>
                    <div class="list-item header">
                        <h3><?= __('Team memberships'); ?></h3>
                    </div>
                    <?php foreach ($user->getTeams() as $team): ?>
                        <div class="list-item"><?php include_component('main/teamdropdown', array('team' => $team)); ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php \pachno\core\framework\Event::createNew('core', 'usercardactions_top', $user)->trigger(); ?>
            <?php \pachno\core\framework\Event::createNew('core', 'usercardactions_bottom', $user)->trigger(); ?>
        <?php endif; ?>
        <button class="button secondary closer"><?= __('Close popup'); ?></button>
    </div>
</div>
