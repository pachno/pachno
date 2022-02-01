<?php
    
    use pachno\core\entities\AgileBoard;
    use pachno\core\entities\Permission;
    use pachno\core\entities\Project;
    use pachno\core\entities\tables\Permissions;
    use pachno\core\entities\User;
    use pachno\core\framework\Request;
    use pachno\core\framework\Response;
    
    /**
     * @var User $pachno_user
     * @var Project $project
     * @var Response $pachno_response
     * @var Request $pachno_request
     * @var AgileBoard[] $boards
     */

?>
<?php if ($pachno_user->hasProjectPermission(Permission::PERMISSION_PROJECT_ACCESS_BOARDS, $project)): ?>
    <div class="list-item <?php if (in_array($pachno_response->getPage(), ['project_planning', 'agile_index', 'agile_board', 'agile_whiteboard'])): ?> selected<?php endif; ?>">
        <a href="<?= make_url('agile_index', ['project_key' => $project->getKey()]); ?>">
            <?= fa_image_tag('chalkboard', ['class' => 'icon']); ?>
            <span class="name"><?= __('Boards'); ?></span>
        </a>
        <?php if (count($boards)): ?>
            <div class="dropper-container pop-out-expander">
                <?= fa_image_tag('angle-right', ['class' => 'dropper']); ?>
                <div class="dropdown-container interactive_filters_list list-mode from-left slide-out">
                    <a class="list-item" href="javascript:void(0);">
                        <span class="icon"><?= fa_image_tag('angle-double-left'); ?></span>
                        <span class="name"><?= __('Back'); ?></span>
                    </a>
                    <?php foreach ($boards as $board): ?>
                        <a href="<?= make_url('agile_whiteboard', ['project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID()]); ?>" class="list-item <?php if ($pachno_request['board_id'] == $board->getID()) echo ' selected'; ?>">
                            <?= fa_image_tag('chalkboard', ['class' => 'icon']); ?>
                            <span class="name"><?= $board->getName(); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
