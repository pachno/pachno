<?php

    /**
     * @var \pachno\core\entities\Scope[] $scopes
     */

?>
<div class="backdrop_box medium" id="client_users">
    <div class="backdrop_detail_header">
        <span><?= __('Editing scopes for user %username', ['%username' => $user->getUsername()]); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="Pachno.UI.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <form action="<?= make_url('configure_users_update_user_scopes', ['user_id' => $user->getID()]); ?>" method="post" data-simple-submit data-reset-backdrop id="edit_user_<?= $user->getID(); ?>_scopes_form">
                <div class="form-row">
                    <div class="helper-text"><?= __('The user can access the following scopes'); ?></div>
                </div>
                <div class="form-row">
                    <div class="list-mode">
                        <?php foreach ($scopes as $scope): ?>
                            <?php if ($scope->isDefault()): ?>
                                <input type="hidden" name="scopes[<?= $scope->getID(); ?>]" checked>
                                <label for="user_<?= $user->getID(); ?>_scopes_<?= $scope->getID(); ?>" class="list-item disabled">
                                    <span class="icon"><?= fa_image_tag('check-square', [], 'far'); ?></span>
                                    <span class="name"><?= $scope->getName(); ?>&nbsp;<span class="count-badge"><?= implode(', ', $scope->getHostnames()); ?></span></span>
                                </label>
                            <?php else: ?>
                                <input type="checkbox" class="fancy-checkbox" name="scopes[<?= $scope->getID(); ?>]" <?php if ($user->isMemberOfScope($scope)) echo ' checked'; ?> <?php if ($scope->isDefault()) echo ' disabled'; ?> id="user_<?= $user->getID(); ?>_scopes_<?= $scope->getID(); ?>">
                                <label for="user_<?= $user->getID(); ?>_scopes_<?= $scope->getID(); ?>" class="list-item">
                                    <span class="icon"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?></span>
                                    <span class="name"><?= $scope->getName(); ?>&nbsp;<span class="count-badge"><?= implode(', ', $scope->getHostnames()); ?></span></span>
                                </label>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-row submit-container">
                    <button type="submit" class="button primary">
                        <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator icon']); ?>
                        <span><?= __('Save'); ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
