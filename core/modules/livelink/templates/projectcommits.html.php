<?php

    use pachno\core\entities\Branch;

    /** @var Branch[] $branches */
    /** @var \pachno\core\entities\Project $selected_project */
    /** @var \pachno\core\framework\Response $pachno_response */

    $pachno_response->setTitle(__('"%project_name" commits', array('%project_name' => $selected_project->getName())));

?>
<div class="content-with-sidebar">
    <?php include_component('project/sidebar', ['dashboard' => __('Find issues')]); ?>
    <div class="commit-navigation-container">
        <div id="commit-list-with-filters-container">
            <div class="top-search-filters-container">
                <div class="search-and-filters-strip">
                    <div class="header">&nbsp;</div>
                    <div class="search-strip">
                        <div class="fancy-dropdown-container filter from-left">
                            <div class="fancy-dropdown" data-default-label="<?= __('All branches'); ?>">
                                <label><?= __('Branch(es)'); ?></label>
                                <span class="value"></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode">
                                    <?php foreach ($branches as $branch): ?>
                                        <input type="radio" name="branch" value="<?= $branch->getName(); ?>" class="fancy-checkbox" id="commit-branch-<?= $branch->getName(); ?>" <?php if (!$branch->getLatestCommit() instanceof \pachno\core\entities\Commit) echo 'disabled' ?>>
                                        <label for="commit-branch-<?= $branch->getName(); ?>" class="<?= ($branch->getLatestCommit() instanceof \pachno\core\entities\Commit) ? 'trigger-show-branch' : 'disabled'; ?> list-item multiline" data-url="<?php echo make_url('livelink_project_commits_post', ['project_key' => $selected_project->getKey()]); ?>" data-branch="<?php echo $branch->getName(); ?>">
                                            <span class="icon"><?= fa_image_tag('code-branch'); ?></span>
                                            <span class="name">
                                                <span class="title value"><?php echo $branch->getName(); ?></span>
                                                <span class="description">
                                                    <?php if ($branch->getLatestCommit() instanceof \pachno\core\entities\Commit): ?>
                                                        <?= __('Last commit: %date', ['%date' => \pachno\core\framework\Context::getI18n()->formatTime($branch->getLatestCommit()->getDate(), 20)]); ?>
                                                    <?php else: ?>
                                                        <?= __('No commits yet'); ?>
                                                    <?php endif; ?>
                                                </span>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="commits-container" class="commits-container">
                <?php if ($is_importing): ?>
                    <div class="message-box type-warning">
                        <span class="message">
                            <?= fa_image_tag('spinner', ['class' => 'fa-spin']) . __('This repository is still being imported and may not be fully up-to-date yet.'); ?>
                        </span>
                    </div>
                <?php endif; ?>
                <div id="project_commits" class="list-mode">
                    <div class="list-item disabled">
                        <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                        <span class="name"><?php echo __('Choose branch on the left to filter commits for this project'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div id="commit-details-container" class="commit-details-container hidden"></div>
    </div>
</div>
<script>

    Pachno.on(Pachno.EVENTS.ready, () => {
        const $body = $('body');
        $body.on('click', '.trigger-show-branch', function () {
            const branch = $(this).data('branch');
            const url = $(this).data('url');
            Pachno.fetch(url, {
                method: 'POST',
                data: { branch },
                success: {
                    show: 'project_commits_box',
                    update: '#project_commits',
                    callback: () => {
                        $('nav.sidebar').addClass('collapsed');
                    }
                }
            });
        });

        $body.on('click', '.back-to-commits-list', function (event) {
            event.preventDefault();
            event.stopPropagation();

            const $commitDetailsContainer = $('#commit-details-container');
            $('#commit-list-with-filters-container').removeClass('hidden');
            $commitDetailsContainer.addClass('hidden');
            $commitDetailsContainer.html('');
            $('nav.sidebar').addClass('collapsed');
        });

        $body.on('click', '.trigger-show-commit', function (event) {
            event.preventDefault();
            event.stopPropagation();

            const $commit = $(this);
            const url = $commit.data('url');
            const commit_id = $commit.data('commit-id');

            $('#commit-list-with-filters-container').addClass('hidden');
            $('nav.sidebar:not(#commit-sidebar-menu)').addClass('collapsed');
            const $commitDetailsContainer = $('#commit-details-container');
            $commitDetailsContainer.removeClass('hidden');
            $commitDetailsContainer.html(`
<div class="top-search-filters-container">
    <div class="search-and-filters-strip">
        <div class="header">&nbsp;</div>
        <div class="search-strip">
            <button class="button secondary back-to-commits-list">
                <span class="icon">${Pachno.UI.fa_image_tag('angle-double-left')}</span>
                <span class="name">&nbsp;</span>
            </button>
        </div>
    </div>
</div>
<div class="content-with-sidebar">
    <nav class="sidebar" id="commit-sidebar-menu">
        <div class="list-mode">
            <div class="indicator-container">${Pachno.UI.fa_image_tag('spinner', { classes: 'fa-spin indicator' })}</div>
        </div>
    </nav>
    <div class="project_info_container">
        <div class="indicator-container">${Pachno.UI.fa_image_tag('spinner', { classes: 'fa-spin indicator' })}</div>
    </div>
</div>
`);
            Pachno.fetch(url, { method: 'GET' })
                .then((json) => {
                    $commitDetailsContainer.html(json.component);
                    $commit.replaceWith(json.commit);
                });

            return false;
        });
    });

</script>
