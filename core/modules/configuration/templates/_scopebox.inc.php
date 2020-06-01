<div class="row">
    <div class="column name-container">
        <?php echo $scope->getName(); ?>
        <?php if (!$scope->isDefault()): ?>
            <form action="<?php echo make_url('configure_scope', array('id' => $scope->getID())); ?>" method="post" style="display: none;" id="delete_scope_<?php echo $scope->getID(); ?>_form">
                <input type="hidden" name="scope_action" value="delete">
            </form>
        <?php endif; ?>
    </div>
    <div class="column">
        <?php if (!$scope->isDefault()): ?>
            <?php echo implode(', ', $scope->getHostnames()); ?>
        <?php else: ?>
            <span style="font-size: 11px;">(<?php echo __('All hostnames not covered by other scopes'); ?>)</span>
        <?php endif; ?>
    </div>
    <div class="column numeric">
        <?php echo $scope->getNumberOfProjects(); ?>
    </div>
    <div class="column numeric">
        <?php echo $scope->getNumberOfIssues(); ?>
    </div>
    <div class="column actions" style="float: right;">
        <?php if (!$scope->isDefault()): ?>
            <div class="dropper-container">
                <button class="dropper button secondary">
                    <span><?= __('Actions'); ?></span>
                    <?= fa_image_tag('angle-down', ['class' => 'icon']); ?>
                </button>
                <div class="dropdown-container">
                    <div class="list-mode">
                        <a href="javascript:void(0);" class="list-item" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'scope_config', 'scope_id' => $scope->getId()]); ?>');">
                            <?= fa_image_tag('edit', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Edit'); ?></span>
                        </a>
                        <a href="javascript:void(0);" class="list-item" onclick="Pachno.UI.Dialog.show('<?php echo __('Do you really want to delete this scope?'); ?>', '<?php echo __('Deleting this scope will destroy all data that exists inside this scope.'); ?> <i><?php echo __('This action cannot be undone.'); ?></i>', {yes: {click: function() {$('delete_scope_<?php echo $scope->getID(); ?>_form').submit();}}, no: {click: Pachno.UI.Dialog.dismiss}});">
                            <?php echo fa_image_tag('times', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Delete'); ?></span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
