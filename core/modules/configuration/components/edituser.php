<?php if ($user->isScopeConfirmed()): ?>
    <div class="backdrop_box medium">
        <div class="backdrop_detail_header">
            <span><?= __('Edit user'); ?></span>
            <?= javascript_link_tag(fa_image_tag('times'), ['class' => 'closer']); ?>
        </div>
        <div id="backdrop_detail_content" class="backdrop_detail_content">
            <div class="form-container">
                <form action="<?= make_url('configure_user', array('user_id' => $user->getID())); ?>" method="post" data-simple-submit data-auto-close id="edit_user_<?= $user->getID(); ?>_form" data-update-container="#users_results_user_<?php echo $user->getID(); ?>" data-update-replace>
                    <div class="form-row">
                        <?php if (\pachno\core\framework\Settings::isUsingExternalAuthenticationBackend()): ?>
                            <span class="value"><?= $user->getUsername(); ?></span>
                        <?php else: ?>
                            <input type="text" name="username" id="username_<?= $user->getID(); ?>" class="name-input-enhance" value="<?= $user->getUsername(); ?>">
                        <?php endif; ?>
                        <label for="username_<?= $user->getID(); ?>"><?= __('Username'); ?></label>
                    </div>
                    <div class="form-row">
                        <?php if (\pachno\core\framework\Settings::isUsingExternalAuthenticationBackend()): ?>
                            <span class="value"><?= ($user->getEmail() == null) ? '-' : $user->getEmail(); ?></span>
                        <?php else: ?>
                            <input type="text" name="email" id="email_<?= $user->getID(); ?>" value="<?= $user->getEmail(); ?>">
                        <?php endif; ?>
                        <label for="email_<?= $user->getID(); ?>"><?= __('Email address'); ?></label>
                    </div>
                    <div class="form-row">
                        <div class="fancy-dropdown-container">
                            <div class="fancy-dropdown">
                                <label><?= __('Group membership'); ?></label>
                                <span class="value"></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode">
                                    <?php foreach (\pachno\core\entities\Group::getAll() as $group): ?>
                                        <input type="radio" name="group" value="<?= $group->getId(); ?>" id="edit_user_<?= $user->getId(); ?>_group_<?= $group->getId(); ?>" class="fancy-checkbox" <?php if ($user->getGroupID() == $group->getID()) echo 'checked'; ?>>
                                        <label for="edit_user_<?= $user->getId(); ?>_group_<?= $group->getId(); ?>" class="list-item">
                                            <span class="icon"><?php echo fa_image_tag('users'); ?></span>
                                            <span class="name value"><?= $group->getName(); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="column">
                            <div class="form-row">
                                <?php if (\pachno\core\framework\Context::getScope()->isDefault()): ?>
                                    <input type="checkbox" class="fancy-checkbox" name="activated" value="1" id="user_activated_<?= $user->getID(); ?>" <?php if ($user->isActivated()) echo 'checked'; ?>>
                                    <label for="user_activated_<?= $user->getID(); ?>">
                                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                        <span><?= __('Activated'); ?></span>
                                    </label>
                                <?php else: ?>
                                    <label for="activated_<?= $user->getID(); ?>_yes"><?= __('Activated'); ?></label>
                                    <span class="value"><?= ($user->isActivated()) ? __('Yes') : __('No'); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="column">
                            <div class="form-row">
                                <?php if (\pachno\core\framework\Context::getScope()->isDefault()): ?>
                                    <input type="checkbox" class="fancy-checkbox" name="enabled" value="1" id="user_enabled_<?= $user->getID(); ?>" <?php if ($user->isEnabled()) echo 'checked'; ?>>
                                    <label for="user_enabled_<?= $user->getID(); ?>">
                                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                        <span><?= __('Enabled'); ?></span>
                                    </label>
                                <?php else: ?>
                                    <label for="enabled_<?= $user->getID(); ?>_yes"><?= __('Enabled'); ?></label>
                                    <?= ($user->isEnabled()) ? __('Yes') : __('No'); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <?php if (\pachno\core\framework\Settings::isUsingExternalAuthenticationBackend()): ?>
                            <span class="value"><?= ($user->getRealname() == null) ? '-' : $user->getRealname(); ?></span>
                        <?php else: ?>
                            <input type="text" name="realname" id="realname_<?= $user->getID(); ?>" value="<?= $user->getRealname(); ?>">
                        <?php endif; ?>
                        <label for="realname_<?= $user->getID(); ?>"><?= __('Real name'); ?></label>
                    </div>
                    <div class="form-row">
                        <?php if (\pachno\core\framework\Settings::isUsingExternalAuthenticationBackend()): ?>
                            <span class="value"><?= ($user->getNickname() == null) ? '-' : $user->getNickname(); ?></span>
                        <?php else: ?>
                            <input type="text" name="nickname" id="nickname_<?= $user->getID(); ?>" value="<?= $user->getNickname(); ?>">
                        <?php endif; ?>
                        <label for="buddyname_<?= $user->getID(); ?>"><?= __('Nickname'); ?></label>
                    </div>
                    <div class="form-row">
                        <input type="text" name="homepage" id="homepage_<?= $user->getID(); ?>" style="width: 250px;" value="<?= $user->getHomepage(); ?>">
                        <label for="homepage_<?= $user->getID(); ?>"><?= __('Homepage'); ?></label>
                    </div>
                    <?php if (\pachno\core\framework\Settings::isUsingExternalAuthenticationBackend()): ?>
                        <div class="form-row explanation">
                            <?= __('The password setting, along with a number of other settings for this user, have been disabled due to use of an alternative authentictation mechanism'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="form-row">
                        <label><?= __('Member of team(s)'); ?></label>
                        <div class="grid">
                            <?php foreach (\pachno\core\entities\Team::getAll() as $team): ?>
                                <input type="checkbox" class="fancy-checkbox" name="teams[<?= $team->getID(); ?>]" id="team_<?= $user->getID(); ?>_<?= $team->getID(); ?>" value="<?= $team->getID(); ?>"<?php if ($user->isMemberOfTeam($team)): ?> checked<?php endif; ?>>
                                <label for="team_<?= $user->getID(); ?>_<?= $team->getID(); ?>">
                                    <span class="icon"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?></span>
                                    <span class="name"><?= $team->getName(); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count(\pachno\core\entities\Team::getAll()) == 0): ?>
                            <?= __('No teams exist'); ?>
                        <?php endif; ?>
                    </div>
                    <div class="form-row">
                        <label><?= __('Member of client(s)'); ?></label>
                        <?php foreach (\pachno\core\entities\Client::getAll() as $client): ?>
                            <div>
                                <input type="checkbox" class="fancy-checkbox" name="clients[<?= $client->getID(); ?>]" id="client_<?= $user->getID(); ?>_<?= $client->getID(); ?>" value="<?= $client->getID(); ?>"<?php if ($user->isMemberOfClient($client)): ?> checked<?php endif; ?>>
                                <label for="client_<?= $user->getID(); ?>_<?= $client->getID(); ?>">
                                    <span class="icon"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?></span>
                                    <span class="name"><?= $client->getName(); ?></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <?php if (count(\pachno\core\entities\Client::getAll()) == 0): ?>
                            <?= __('No clients exist'); ?>
                        <?php endif; ?>
                    </div>
                    <div class="form-row submit-container">
                        <button type="submit" class="button primary">
                            <span class="name"><?= __('Save'); ?></span>
                            <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
