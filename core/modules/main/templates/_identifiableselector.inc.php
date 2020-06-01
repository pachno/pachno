<?php

use pachno\core\entities\User;

/**
 * @var boolean $allow_clear
 * @var boolean $use_form
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
            <a href="javascript:void(0);" class="list-item" onclick="<?php echo str_replace(array(urlencode('%identifiable_value'), '%identifiable_value'), array(0, 0), $callback); ?>">
                <span class="icon"><?php echo fa_image_tag('times'); ?></span>
                <span class="name"><?= $clear_link_text; ?></span>
            </a>
            <div class="separator"></div>
        <?php endif; ?>
        <?php if (!$use_form): ?>
            <div id="<?php echo $base_id; ?>_form" class="list-item filter-container">
        <?php else: ?>
            <form id="<?php echo $base_id; ?>_form" class="list-item filter-container" data-identifiable-selector-form data-base-id="<?= $base_id; ?>" accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" method="post" action="<?php echo make_url('main_find_identifiable'); ?>">
        <?php endif; ?>
            <?php if ($include_teams && $include_users): ?>
                <?php $text_title = __('Find a user or team'); ?>
            <?php elseif ($include_teams): ?>
                <?php $text_title = __('Find a team'); ?>
            <?php elseif ($include_clients): ?>
                <?php $text_title = __('Find a client'); ?>
            <?php else: ?>
                <?php $text_title = __('Find a user'); ?>
            <?php endif; ?>
            <?php if (isset($teamup_callback)): ?>
                <input type="hidden" name="teamup_callback" value="<?php echo $teamup_callback; ?>">
            <?php endif; ?>
            <input type="hidden" name="callback" value="<?php echo $callback; ?>">
            <?php if (isset($team_callback)): ?>
                <input type="hidden" name="team_callback" value="<?php echo $team_callback; ?>">
            <?php endif; ?>
            <input type="hidden" name="include_teams" value="<?php echo (int) $include_teams; ?>">
            <input type="hidden" name="include_clients" value="<?php echo (int) $include_clients; ?>">
            <input type="search" class="identifiable_lookup" name="find_identifiable_by" id="<?php echo $base_id; ?>_input" placeholder="<?php echo $text_title; ?>">
        <?php if ($use_form): ?>
            </form>
        <?php else: ?>
            </div>
        <?php endif; ?>
        <div id="<?php echo $base_id; ?>_results_container">
            <div id="<?php echo $base_id; ?>_results"></div>
        </div>
        <?php if ($include_users): ?>
            <div class="separator"></div>
            <div class="header"><?php echo __('Select yourself or a friend below'); ?></div>
            <a href="javascript:void(0);" class="list-item" onclick="<?php echo str_replace([urlencode('%identifiable_value'), '%identifiable_value', urlencode('%identifiable_type'), '%identifiable_type'], array($pachno_user->getID(), $pachno_user->getID(), 'user', "'user'"), $callback); ?>">
                <span class="icon"><?php echo image_tag($pachno_user->getAvatarURL(), ['class' => 'avatar small'], true); ?></span>
                <span class="name"><?php echo __('Select yourself'); ?> (<?php echo $pachno_user->getUsername(); ?>)</span>
            </a>
            <div class="list-item separator"></div>
            <?php if (count($pachno_user->getFriends()) == 0): ?>
                <div class="list-item disabled">
                    <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                    <span><?php echo __("Your friends will appear here"); ?></span>
                </div>
            <?php else: ?>
                <?php include_component('main/identifiableselectorresults', array('header' => false, 'users' => $pachno_user->getFriends(), 'callback' => $callback, 'team_callback' => ((isset($team_callback)) ? $team_callback : null))); ?>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (isset($team_callback) && count($pachno_user->getTeams()) > 0): ?>
            <div class="separator"></div>
            <div class="header">
                <?php if ($include_users): ?>
                    <?php echo __('%select_yourself_or_a_friend or select one of your teams', array('%select_yourself_or_a_friend' => '')); ?>
                <?php else: ?>
                    <?php echo __('Select one of your teams'); ?>
                <?php endif; ?>
            </div>
            <?php foreach ($pachno_user->getTeams() as $team): ?>
                <a href="javascript:void(0);" class="list-item" onclick="<?php echo str_replace(array(urlencode('%identifiable_value'), '%identifiable_value', urlencode('%identifiable_type'), '%identifiable_type'), array($team->getID(), $team->getID(), 'team', "'team'"), $team_callback); ?>">
                    <span class="icon"><?= fa_image_tag('users'); ?></span>
                    <span class="name"><?php echo $team->getName(); ?></span>
                </a>
            <?php endforeach; ?>
        <?php elseif (isset($client_callback) && count($pachno_user->getClients()) > 0): ?>
            <div class="separator"></div>
            <div class="header"><?php echo __('Select one of your clients'); ?></div>
            <?php foreach ($pachno_user->getClients() as $client): ?>
                <a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_value'), '%identifiable_value', urlencode('%identifiable_type'), '%identifiable_type'), array($client->getID(), $client->getID(), 'client', "'client'"), $client_callback); ?>">
                    <span class="icon"><?= fa_image_tag('users'); ?></span>
                    <span class="name"><?php echo __('Select %clientname', array('%clientname' => $client->getName())); ?> (<?php echo $client->getName(); ?>)</span>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
