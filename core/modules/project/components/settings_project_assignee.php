<?php

/**
 * @var \pachno\core\entities\common\Identifiable $assignee
 * @var \pachno\core\entities\Project $project
 */

$assignee_type = ($assignee instanceof \pachno\core\entities\User) ? 'user' : 'team';

?>
<div class="row" data-project-assignee data-assignee-type="<?= $assignee_type; ?>" data-assignee-id="<?= $assignee->getId() ;?>">
    <div class="column name-container">
        <?php if ($assignee instanceof \pachno\core\entities\User): ?>
            <?php include_component('main/userdropdown', ['user' => $assignee, 'size' => 'small']); ?>
            <?php if (!$assignee->isActivated()): ?>
                <span class="count-badge"><?= fa_image_tag('envelope-open-text', ['class' => 'icon']); ?><span><?= __('Invitation sent'); ?></span></span>
            <?php endif; ?>
        <?php elseif ($assignee instanceof \pachno\core\entities\Team): ?>
            <?php include_component('main/teamdropdown', ['team' => $assignee, 'size' => 'small']); ?>
        <?php endif; ?>
    </div>
    <div class="column roles">
        <div class="list-mode">
            <?php $roles = ($assignee instanceof \pachno\core\entities\User) ? $project->getRolesForUser($assignee) : $project->getRolesForTeam($assignee); ?>
            <?php foreach ($roles as $role): ?>
                <span class="count-badge"><?= $role->getName(); ?></span>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="column actions">
        <button class="secondary danger icon" onclick="Pachno.UI.Dialog.show(
            '<?= __('Remove %username from this project?', array('%username' => $assignee->getName())); ?>',
            '<?= __('Please confirm removal from the project team'); ?>',
            {
                yes: {click: function() { Pachno.trigger(Pachno.EVENTS.project.removeAssignee, { url: '<?= make_url('configure_project_remove_assignee', ['project_id' => $project->getID(), 'assignee_type' => $assignee_type, 'assignee_id' => $assignee->getID()]); ?>'})}},
                no: {click: Pachno.UI.Dialog.dismiss}
            }
        );"><?= fa_image_tag('times'); ?></button>
    </div>
</div>
