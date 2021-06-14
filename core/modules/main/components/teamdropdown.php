<?php if (!$team instanceof \pachno\core\entities\Team || $team->getID() == 0): ?>
    <span class="faded_out"><?php echo __('No such team'); ?></span>
<?php else: ?>
<a href="<?= make_url('team_dashboard', ['team_id' => $team->getID()]); ?>" class="userlink<?php if ($pachno_user->isMemberOfTeam($team)) echo ' friend'; ?>">
    <?php echo fa_image_tag('users', ['class' => "icon"]); ?>
    <span><?php echo isset($displayname) && is_string($displayname) ? $displayname : $team->getName(); ?></span>
</a>
<?php endif; ?>
