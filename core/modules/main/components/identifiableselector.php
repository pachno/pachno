<?php

use pachno\core\entities\User;

/**
 * @var boolean $allow_clear
 * @var boolean $include_teams
 * @var boolean $include_users
 * @var boolean $include_clients
 *
 * @var string $base_id
 * @var string $header;
 * @var string $clear_link_text
 *
 * @var User $pachno_user
 */

?>
<div class="dropdown-container">
    <div class="list-mode">
        <div class="header"><?php echo $header; ?></div>
        <?php if ($allow_clear): ?>
            <a href="javascript:void(0);" class="list-item <?= $trigger_class; ?>" data-identifiable-value="0" data-identifiable-type="0">
                <span class="icon"><?php echo fa_image_tag('times'); ?></span>
                <span class="name"><?= $clear_link_text; ?></span>
            </a>
            <div class="separator"></div>
        <?php endif; ?>
        <div id="<?php echo $base_id; ?>_form" class="list-item filter-container" data-url="<?php echo make_url('main_find_identifiable'); ?>" data-include-teams="<?= (int) $include_teams; ?>" data-include-clients="<?= (int) $include_clients; ?>" data-trigger-class="<?= $trigger_class; ?>">
            <?php if ($include_teams && $include_users): ?>
                <?php $text_title = __('Find a user or team'); ?>
            <?php elseif ($include_teams): ?>
                <?php $text_title = __('Find a team'); ?>
            <?php elseif ($include_clients): ?>
                <?php $text_title = __('Find a client'); ?>
            <?php else: ?>
                <?php $text_title = __('Find a user'); ?>
            <?php endif; ?>
            <input type="search" class="identifiable_lookup" name="find_identifiable_by" id="<?php echo $base_id; ?>_input" placeholder="<?php echo $text_title; ?>">
        </div>
        <div id="<?php echo $base_id; ?>_results_container">
            <div id="<?php echo $base_id; ?>_results"></div>
        </div>
        <?php if ($include_users): ?>
            <div class="separator"></div>
            <a href="javascript:void(0);" class="list-item <?= $trigger_class; ?>" data-identifiable-type="user" data-identifiable-value="<?= $pachno_user->getID(); ?>">
                <span class="icon"><?php echo image_tag($pachno_user->getAvatarURL(), ['class' => 'avatar small'], true); ?></span>
                <span class="name"><?php echo __('Select yourself'); ?> (<?php echo $pachno_user->getUsername(); ?>)</span>
            </a>
        <?php endif; ?>
        <?php if (isset($include_teams) && $include_teams && count($pachno_user->getTeams()) > 0): ?>
            <div class="separator"></div>
            <div class="header">
                <?php if ($include_users): ?>
                    <?php echo __('%select_yourself or select one of your teams', array('%select_yourself' => '')); ?>
                <?php else: ?>
                    <?php echo __('Select one of your teams'); ?>
                <?php endif; ?>
            </div>
            <?php foreach ($pachno_user->getTeams() as $team): ?>
                <a href="javascript:void(0);" class="list-item <?= $trigger_class; ?>" data-identifiable-type="team" data-identifiable-value="<?= $team->getID(); ?>">
                    <span class="icon"><?= fa_image_tag('users'); ?></span>
                    <span class="name"><?php echo $team->getName(); ?></span>
                </a>
            <?php endforeach; ?>
        <?php elseif (isset($include_clients) && $include_clients && count($pachno_user->getClients()) > 0): ?>
            <div class="separator"></div>
            <div class="header"><?php echo __('Select one of your clients'); ?></div>
            <?php foreach ($pachno_user->getClients() as $client): ?>
                <a href="javascript:void(0);" class="list-item <?= $trigger_class; ?>" data-identifiable-type="client" data-identifiable-value="<?= $client->getID(); ?>">
                    <span class="icon"><?= fa_image_tag('users'); ?></span>
                    <span class="name"><?php echo __('Select %clientname', array('%clientname' => $client->getName())); ?> (<?php echo $client->getName(); ?>)</span>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
