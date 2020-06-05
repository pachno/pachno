<?php

    use pachno\core\entities\User;

    /**
     * @var User $user
     */

?>
<div class="row" id="users_results_user_<?php echo $user->getID(); ?>">
    <div class="column name-container" xmlns:javascript="http://www.w3.org/1999/xhtml">
        <span class="count-badge">
            <?php if ($user->isScopeConfirmed()): ?>
                <?php echo ($user->getID()); ?>
            <?php else: ?>
                -
            <?php endif; ?>
        </span>
        <?php include_component('main/userdropdown', ['user' => $user, 'displayname' => ($user->isOpenIdLocked()) ? '<span class="faded_out">'.$user->getEmail().'</span>' : $user->getUsername()]); ?>
    </div>
    <div class="column">
        <?php if ($user->isScopeConfirmed()): ?>
            <?php echo ($user->getEmail() != '') ? link_tag("mailto:{$user->getEmail()}", $user->getEmail()) : '<span class="faded_out"> - </span>'; ?>
        <?php else: ?>
        -
        <?php endif; ?>
    </div>
    <div class="column info-icons"><?php echo ($user->isActivated()) ? fa_image_tag('check-square') : fa_image_tag('square'); ?></div>
    <div class="column actions">
        <div class="dropper-container">
            <button class="button icon secondary dropper"><?= fa_image_tag('ellipsis-v'); ?></button>
            <div class="dropdown-container">
                <div class="list-mode">
                    <?php if ($user->isScopeConfirmed()): ?>
                        <a href="javascript:void(0);" class="list-item trigger-backdrop" data-url="<?= make_url('configure_users_edit_user_form', ['user_id' => $user->getID()]); ?>">
                            <?= fa_image_tag('edit', ['class' => 'icon']); ?>
                            <span class="name"><?php echo __('Edit this user'); ?></span>
                            <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                        </a>
                        <?php if (!\pachno\core\framework\Settings::isUsingExternalAuthenticationBackend()): ?>
                            <a href="javascript:void(0);" class="list-item">
                                <?= fa_image_tag('key', ['class' => 'icon']); ?>
                                <span class="name"><?= __('Change / reset password'); ?></span>
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="javascript:void(0);" class="list-item disabled" onclick="Pachno.UI.Message.error('<?php echo __('This user cannot be edited'); ?>', '<?php echo __('The user must confirm his membership in this scope before you can perform this action'); ?>');">
                            <?= fa_image_tag('edit', ['class' => 'icon']); ?>
                            <span class="name"><?php echo __('Edit this user'); ?></span>
                        </a>
                    <?php endif; ?>
                    <div class="list-item separator"></div>
                    <?php if (\pachno\core\framework\Context::getScope()->isDefault()): ?>
                        <a href="javascript:void(0);" class="list-item" onclick="Pachno.UI.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', ['key' => 'userscopes', 'user_id' => $user->getID()]); ?>');">
                            <?= fa_image_tag('copy', ['class' => 'icon']); ?>
                            <span class="name"><?php echo __('Edit available scopes for this user'); ?></span>
                        </a>
                    <?php endif; ?>
                    <?php /* if ($user->getID() != $pachno_user->getID()): ?>
                        <a href="javascript:void(0);" class="list-item" onclick="Pachno.Main.Profile.addFriend('<?= make_url('toggle_friend', array('mode' => 'add', 'user_id' => $user->getID())); ?>', <?= $user->getID(); ?>, 12);" style="<?php if ($pachno_user->isFriend($user)): ?> display: none;<?php endif; ?>" id="add_friend_<?php echo $user->getID(); ?>_12">
                            <?= fa_image_tag('user-plus', ['class' => 'icon']); ?>
                            <span class="name"><?php echo __('Become friends'); ?></span>
                        </a>
                        <a href="javascript:void(0);" class="list-item" onclick="Pachno.Main.Profile.removeFriend('<?= make_url('toggle_friend', array('mode' => 'remove', 'user_id' => $user->getID())); ?>', <?= $user->getID(); ?>, 12);" style="<?php if (!$pachno_user->isFriend($user)): ?> display: none;<?php endif; ?>" id="remove_friend_<?php echo $user->getID(); ?>_12">
                            <?= fa_image_tag('user-minus', ['class' => 'icon']); ?>
                            <span class="name"><?php echo __('Remove this friend'); ?></span>
                        </a>
                    <?php endif; */ ?>
                    <a href="javascript:void(0);" class="list-item" onclick="Pachno.UI.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $user->getID())); ?>');$('#bud_<?php echo $user->getUsername() . "_12"; ?>').hide();">
                        <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                        <span class="name"><?php echo __('Show user details'); ?></span>
                    </a>
                    <div class="list-item separator"></div>
                    <?php if (!in_array($user->getID(), array(1, (int) \pachno\core\framework\Settings::get(\pachno\core\framework\Settings::SETTING_DEFAULT_USER_ID)))): ?>
                        <?php if (\pachno\core\framework\Context::getScope()->isDefault()): ?>
                            <a href="javascript:void(0);" class="list-item danger" onclick="Pachno.UI.Dialog.show('<?= __('Permanently delete this user?'); ?>', '<?= __('Are you sure you want to remove this user? This will remove the users login data, as well as memberships in (and data in) any scopes the user is a member of.'); ?>', {yes: {click: function() {Pachno.Config.User.remove('<?= make_url('configure_users_delete_user', ['user_id' => $user->getID()]); ?>', <?= $user->getID(); ?>); Pachno.UI.Dialog.dismiss(); } }, no: {click: Pachno.UI.Dialog.dismiss}});">
                                <?= fa_image_tag('times', ['class' => 'icon']); ?>
                                <span class="name"><?php echo __('Delete this user'); ?></span>
                            </a>
                        <?php elseif ($user->isScopeConfirmed()): ?>
                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.UI.Dialog.show('<?= __('Remove this user?'); ?>', '<?= __('Are you sure you want to remove this user from the current scope? The users login is kept, and you can re-add the user later.'); ?>', {yes: {click: function() {Pachno.Config.User.remove('<?= make_url('configure_users_delete_user', array('user_id' => $user->getID())); ?>', <?= $user->getID(); ?>); Pachno.UI.Dialog.dismiss(); } }, no: {click: Pachno.UI.Dialog.dismiss}});">
                                <?= fa_image_tag('times', ['class' => 'icon']); ?>
                                <span class="name"><?php echo __('Remove user from this scope'); ?></span>
                            </a>
                        <?php else: ?>
                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.UI.Dialog.show('<?= __('Cancel membership in this scope?'); ?>', '<?= __('If you cancel the invitation to this scope, then this user will be notified and the unconfirmed membership removed from this scope.'); ?>', {yes: {click: function() {Pachno.Config.User.remove('<?= make_url('configure_users_delete_user', array('user_id' => $user->getID())); ?>', <?= $user->getID(); ?>); Pachno.UI.Dialog.dismiss(); } }, no: {click: Pachno.UI.Dialog.dismiss}});">
                                <?= fa_image_tag('times', ['class' => 'icon']); ?>
                                <span class="name"><?php echo __('Cancel invitation'); ?></span>
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="javascript:void(0);" class="list-item disabled danger" onclick="Pachno.UI.Message.error('<?php echo __('This user cannot be removed'); ?>', '<?php echo __('This is a system user which cannot be removed'); ?>');">
                            <?= fa_image_tag('times', ['class' => 'icon']); ?>
                            <span class="name"><?php echo __('Delete this user'); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
