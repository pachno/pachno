<div class="onboarding">
    <div class="image-container">
        <?= image_tag('/unthemed/onboarding_invite.png', [], true); ?>
    </div>
    <div class="helper-text">
        <?= __('No man or woman is an island'); ?><br>
        <?= __('Invite team members, colleagues or collaborators'); ?>
    </div>
    <?php if (\pachno\core\framework\Context::getCurrentProject() instanceof \pachno\core\entities\Project): ?>
        <button class="button secondary highlight" onclick="Pachno.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', ['key' => 'project_add_people', 'invite' => true, 'project_id' => \pachno\core\framework\Context::getCurrentProject()->getID()]); ?>');"><?= __('Invite people'); ?></button>
    <?php else: ?>
        <button class="button secondary highlight" onclick="Pachno.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', ['key' => 'invite_users']); ?>');"><?= __('Invite people'); ?></button>
    <?php endif; ?>
</div>
