<?php

    use pachno\core\entities\Client;
    use pachno\core\entities\User;
    use pachno\core\framework\Context;
    use pachno\core\framework\Event;

    /**
     * @var Client $client
     * @var string $members_url
     */

?>
<div class="backdrop_box huge edit_agileboard">
    <div class="backdrop_detail_header">
        <span><?= ($client->getId()) ? __('Edit client') : __('Create new client'); ?></span>
        <a href="javascript:void(0);" class="closer"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content with-sidebar">
        <div class="sidebar">
            <div class="list-mode tab-switcher" id="client_form_tabs">
                <a href="javascript:void(0);" data-tab-target="client-info" class="tab-switcher-trigger list-item selected">
                    <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Information'); ?></span>
                </a>
                <a href="javascript:void(0);" data-tab-target="members" class="<?= ($client->getID()) ? 'tab-switcher-trigger' : 'disabled'; ?> list-item">
                    <?= fa_image_tag('users', ['class' => 'icon']); ?>
                    <span class="name"><?= __('People and members'); ?></span>
                </a>
                <a href="javascript:void(0);" data-tab-target="permissions" class="<?= ($client->getID()) ? 'tab-switcher-trigger' : 'disabled'; ?> list-item">
                    <?= fa_image_tag('lock', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Client permissions'); ?></span>
                </a>
            </div>
        </div>
        <div class="content" id="client_form_tabs_panes">
            <div data-tab-id="client-info" class="form-container">
                <form action="<?= make_url('configure_client', ['client_id' => $client->getID()]); ?>" id="edit_client_form" method="post" data-simple-submit>
                    <div class="form-row">
                        <input type="text" id="client_<?= $client->getID(); ?>_name" name="name" value="<?= __e($client->getName()); ?>" class="name-input-enhance">
                        <label style for="client_<?= $client->getID(); ?>_name"><?= __('Client name'); ?></label>
                        <?php if (!$client->getID()): ?>
                            <div class="message-box type-info">
                                <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                                <span><?= __('You can add client details, members and define permissions after the client has been created.'); ?></span>
                            </div>
                        <?php else: ?>
                            <div class="helper-text">
                                <?= __("The client name should match the business, reference or organization name of the client"); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($client->getID()): ?>
                        <div class="form-row">
                            <input type="email" id="client_<?= $client->getID(); ?>_email" name="email" value="<?= __e($client->getEmail()); ?>">
                            <label style for="client_<?= $client->getID(); ?>_email"><?= __('Client email address (optional)'); ?></label>
                        </div>
                        <div class="form-row">
                            <input type="text" id="client_<?= $client->getID(); ?>_website" name="website" value="<?= __e($client->getWebsite()); ?>">
                            <label style for="client_<?= $client->getID(); ?>_website"><?= __('Client website (optional)'); ?></label>
                        </div>
                        <div class="form-row">
                            <input type="text" id="client_<?= $client->getID(); ?>_phone" name="phone" value="<?= __e($client->getTelephone()); ?>">
                            <label style for="client_<?= $client->getID(); ?>_phone"><?= __('Client contact phone (optional)'); ?></label>
                        </div>
                        <div class="form-row">
                            <input type="text" id="client_<?= $client->getID(); ?>_fax" name="fax" value="<?= __e($client->getFax()); ?>">
                            <label style for="client_<?= $client->getID(); ?>_fax"><?= __('Client fax number (optional)'); ?></label>
                        </div>
                    <?php endif; ?>
                    <div class="form-row submit-container">
                        <button type="submit" class="button primary">
                            <?= fa_image_tag('spinner', ['class' => 'indicator fa-spin icon']); ?>
                            <span><?= ($client->getID()) ? __('Save client') : __('Create client'); ?></span>
                        </button>
                    </div>
                </form>
            </div>
            <div data-tab-id="members" class="form-container" style="display: none;">
                <div id="client_members_list" class="flexible-table assignee-results-list">
                    <div class="row header">
                        <div class="column header name-container"><?= __('Name'); ?></div>
                        <div class="column header role"><?= __('Role'); ?></div>
                        <div class="column header actions"></div>
                    </div>
                    <div class="row">
                        <div class="column name-container" id="client-external-contact-container" data-client-id="<?= $client->getID(); ?>" data-url="<?= $members_url; ?>">
                            <?php if ($client->getExternalContact() instanceof User): ?>
                                <?php include_component('main/userdropdown', ['user' => $client->getExternalContact(), 'size' => 'small']); ?>
                            <?php else: ?>
                                <?= __('No external contact assigned'); ?>
                            <?php endif; ?>
                        </div>
                        <div class="column">
                            <span class="count-badge"><?= __('Client external contact'); ?></span>
                        </div>
                        <div class="column actions">
                            <div class="dropper-container">
                                <button class="button dropper secondary icon"><?= fa_image_tag('ellipsis-v'); ?></button>
                                <?php include_component('main/identifiableselector', [
                                    'base_id'         => 'external_contact',
                                    'header'          => __('Change / set client external contact'),
                                    'clear_link_text' => __('Clear client external contact'),
                                    'trigger_class'   => 'trigger-set-client-external-contact',
                                    'allow_clear'     => true,
                                    'include_teams'   => false
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="column name-container" id="client-internal-contact-container" data-client-id="<?= $client->getID(); ?>" data-url="<?= $members_url; ?>">
                            <?php if ($client->getInternalContact() instanceof User): ?>
                                <?php include_component('main/userdropdown', ['user' => $client->getInternalContact(), 'size' => 'small']); ?>
                            <?php else: ?>
                                <?= __('No internal contact assigned'); ?>
                            <?php endif; ?>
                        </div>
                        <div class="column">
                            <span class="count-badge"><?= __('Client internal contact'); ?></span>
                        </div>
                        <div class="column actions">
                            <div class="dropper-container">
                                <button class="button dropper secondary icon"><?= fa_image_tag('ellipsis-v'); ?></button>
                                <?php include_component('main/identifiableselector', [
                                    'base_id'         => 'internal_contact',
                                    'header'          => __('Change / set client internal contact'),
                                    'clear_link_text' => __('Clear client internal contact'),
                                    'trigger_class'   => 'trigger-set-client-internal-contact',
                                    'allow_clear'     => true,
                                    'include_teams'   => false
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    <?php foreach ($client->getMembers() as $member): ?>
                        <?php include_component('configuration/client_member', ['user' => $member, 'client' => $client]); ?>
                    <?php endforeach; ?>
                </div>
                <div class="form-container">
                    <form accept-charset="<?= Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_client_members', ['client_id' => $client->getID()]); ?>" method="post" data-simple-submit data-update-container="#find_client_members_results" id="find_client_members_form">
                        <div class="form-row search-container">
                            <label for="add_client_search_input"></label>
                            <input type="search" name="find_by" id="add_client_search_input" value="" placeholder="<?= __('Enter user details or email address to find or invite users'); ?>">
                            <button type="submit" class="button primary">
                                <?= fa_image_tag('search', ['class' => 'icon']); ?>
                                <span class="name"><?= __('Find'); ?></span>
                                <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']); ?>
                            </button>
                        </div>
                    </form>
                </div>
                <div id="find_client_members_results">
                    <div class="onboarding medium">
                        <div class="image-container">
                            <?= image_tag('/unthemed/onboarding_invite.png', [], true); ?>
                        </div>
                        <div class="helper-text">
                            <?= __('Add existing users or invite new users by adding them to the client'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div data-tab-id="permissions" class="form-container" style="display: none;">
                <form action="<?= make_url('configure_client', ['client_id' => $client->getID()]); ?>" id="edit_client_permissions_form" method="post" data-simple-submit>
                    <div class="form-row">
                        <div class="helper-text">
                            <div class="image-container"><?= image_tag('/unthemed/onboarding_configure_client_permissions.png', [], true); ?></div>
                            <span class="description">
                                <?php echo __("These permissions apply to all members of this client. In addition to these permissions, clients can be added to projects, allowing client members access to specific project resources.", array('%online_documentation' => link_tag('https://projects.pach.no/pachno/docs/UserClients', '<b>'.__('online documentation').'</b>'))); ?>
                            </span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="message-box type-info">
                            <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                            <span class="message"><?= __('If you have more than one client and would like to share client permissions across all clients it is probably better to create a "Clients" user group, add client users to that user group and manage permissions via group permissions instead of manually setting permissions per client.'); ?></span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="list-mode">
                            <div class="interactive_menu_values filter_existing_values">
                                <?php include_component('configuration/grouppermissionseditlist', ['target' => $client, 'permissions_list' => Context::getAvailablePermissions('user'), 'module' => 'core', 'target_id' => null]); ?>
                                <?php include_component('configuration/grouppermissionseditlist', ['target' => $client, 'permissions_list' => Context::getAvailablePermissions('pages'), 'module' => 'core', 'target_id' => null]); ?>
                                <?php include_component('configuration/grouppermissionseditlist', ['target' => $client, 'permissions_list' => Context::getAvailablePermissions('configuration'), 'module' => 'core', 'target_id' => null, 'is_configuration' => true]); ?>
                                <?php Event::createNew('core', 'clientpermissionsedit', $client)->trigger(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-row submit-container">
                        <button type="submit" class="button primary">
                            <?= fa_image_tag('spinner', ['class' => 'indicator fa-spin icon']); ?>
                            <span><?= __('Save client'); ?></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
