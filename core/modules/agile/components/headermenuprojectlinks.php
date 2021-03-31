<?php

    use pachno\core\entities\tables\Permissions;

    /**
     * @var \pachno\core\entities\User $pachno_user
     * @var \pachno\core\entities\Project $project
     * @var \pachno\core\framework\Response $pachno_response
     */

?>
<?php if ($pachno_user->hasProjectPermission(Permissions::PERMISSION_PROJECT_ACCESS_BOARDS, $project)): ?>
    <div class="list-item <?php if (in_array($pachno_response->getPage(), ['project_planning', 'agile_index', 'agile_board', 'agile_whiteboard'])): ?> selected<?php endif; ?>">
        <a href="<?= make_url('agile_index', ['project_key' => $project->getKey()]); ?>">
            <?= fa_image_tag('chalkboard', ['class' => 'icon']); ?>
            <span class="name"><?= __('Boards'); ?></span>
        </a>
        <div class="dropper-container pop-out-expander">
            <?= fa_image_tag('angle-right', ['class' => 'dropper']); ?>
            <div class="dropdown-container interactive_filters_list list-mode from-left slide-out">
                <a class="list-item" href="javascript:void(0);">
                    <span class="icon"><?= fa_image_tag('angle-double-left'); ?></span>
                    <span class="name"><?= __('Back'); ?></span>
                </a>
                <?php if (count($boards)): ?>
                    <?php foreach ($boards as $board): ?>
                        <a href="<?= make_url('agile_whiteboard', ['project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID()]); ?>" class="list-item <?php if ($pachno_request['board_id'] == $board->getID()) echo ' selected'; ?>">
                            <?= fa_image_tag('chalkboard', ['class' => 'icon']); ?>
                            <span class="name"><?= $board->getName(); ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="list-item disabled">
                        <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                        <span class="name"><?= __('No project boards available'); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
