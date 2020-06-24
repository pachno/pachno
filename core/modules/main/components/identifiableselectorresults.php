<?php if (isset($clients)): ?>
    <div class="header"><label><?php echo __('Clients found'); ?></label></div>
    <?php if (count($clients) > 0): ?>
        <?php foreach ($clients as $client): ?>
            <a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_value'), '%identifiable_value', urlencode('%identifiable_type'), '%identifiable_type'), array($client->getID(), $client->getID(), 'client', "'client'"), (isset($client_callback)) ? $client_callback : $callback); ?>">
                <span class="icon"><?= fa_image_tag('users'); ?></span>
                <span class="name"><?php echo $client->getName(); ?></span>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="list-item disabled">
            <?php echo __("Couldn't find any clients"); ?>
        </div>
    <?php endif; ?>
<?php else: ?>
    <?php if (!isset($header) || $header == true): ?>
        <div class="header"><?php echo __('Users found'); ?></div>
    <?php endif; ?>
    <?php if (count($users) > 0): ?>
        <?php foreach ($users as $user): ?>
            <a href="javascript:void(0);" class="list-item" onclick="<?php echo str_replace(array(urlencode('%identifiable_value'), '%identifiable_value', urlencode('%identifiable_type'), '%identifiable_type'), array($user->getID(), $user->getID(), 'user', "'user'"), $callback); ?>">
                <span class="icon"><?php echo image_tag($user->getAvatarURL(), ['class' => 'avatar small'], true); ?></span>
                <span class="name"><?php echo $user->getNameWithUsername(); ?></span>
            </a>
            <?php if (isset($teamup_callback)): ?>
                <a href="javascript:void(0);" class="list-item" onclick="<?php echo str_replace(array(urlencode('%identifiable_value'), '%identifiable_value', urlencode('%identifiable_type'), '%identifiable_type'), array($user->getID(), $user->getID(), 'user', "'user'"), $teamup_callback); ?>">
                    <span class="icon"><?php echo image_tag($user->getAvatarURL(), ['class' => 'avatar small'], true); ?><?= fa_image_tag('user-plus'); ?></span>
                    <span class="name"><?php echo __('Team up with %username', array('%username' => $user->getNameWithUsername())); ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="list-item disabled"><?php echo __("Couldn't find any users"); ?></div>
    <?php endif; ?>
    <?php if ($include_teams): ?>
        <div class="header"><?php echo __('Teams found'); ?></div>
        <?php if (isset($teams) && count($teams) > 0): ?>
            <?php foreach ($teams as $team): ?>
                <a href="javascript:void(0);" class="list-item" onclick="<?php echo str_replace(array(urlencode('%identifiable_value'), '%identifiable_value', urlencode('%identifiable_type'), '%identifiable_type'), array($team->getID(), $team->getID(), 'team', "'team'"), (isset($team_callback)) ? $team_callback : $callback); ?>">
                    <span class="icon"><?= fa_image_tag('users'); ?></span>
                    <span class="name"><?php echo $team->getName(); ?></span>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="list-item disabled"><?php echo __("Couldn't find any teams"); ?></div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
