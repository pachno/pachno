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
        <div id="commit-list-with-filters-container" data-url="<?php echo make_url('livelink_project_commits_post', ['project_key' => $selected_project->getKey()]); ?>">
            <div class="top-search-filters-container">
                <div class="search-and-filters-strip">
                    <div class="header">&nbsp;</div>
                    <div class="search-strip">
                        <?php if (count($branches)): ?>
                            <div class="fancy-dropdown-container filter from-left">
                                <div class="fancy-dropdown" data-default-label="<?= __('Select a branch'); ?>">
                                    <label><?= __('Branch'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode">
                                        <?php foreach ($branches as $branch): ?>
                                            <input type="radio" name="branch" value="<?= $branch->getName(); ?>" class="fancy-checkbox" id="commit-branch-<?= $branch->getName(); ?>" <?php if (in_array($branch->getName(), ['master', 'main', 'trunk'])) echo 'checked '; if (!$branch->getLatestCommit() instanceof \pachno\core\entities\Commit) echo ' disabled' ?>>
                                            <label for="commit-branch-<?= $branch->getName(); ?>" class="<?= ($branch->getLatestCommit() instanceof \pachno\core\entities\Commit) ? 'trigger-show-branch' : 'disabled'; ?> list-item multiline" data-branch="<?php echo $branch->getName(); ?>">
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
                        <?php else: ?>
                            <span class="message-box">
                                <span class="icon"><?= fa_image_tag('code'); ?></span>
                                <span class="message">
                                    <span class="title"><?= __('This project is not linked to a repository'); ?></span>
                                    <span><?= __('Link the project to an external repository to see commits, external issues and more'); ?></span>
                                </span>
                                <span class="actions">
                                    <a href="<?= make_url('project_settings', ['project_key' => $selected_project->getKey()]); ?>" class="button secondary highlight"><?php echo __('Import / link'); ?></a>
                                </span>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div id="commits-container" class="commits-container">
                <div class="message-box type-warning <?php if (!$is_importing) echo 'hidden'; ?>" id="project-repository-import-in-progress-message">
                    <span class="message">
                        <?= fa_image_tag('spinner', ['class' => 'fa-spin']) . __('This repository is being imported and may not be fully up-to-date yet.'); ?>
                    </span>
                </div>
                <?php if (count($branches) && !$connector): ?>
                    <div class="message-box type-warning">
                        <span class="icon"><?= fa_image_tag('info-circle'); ?></span>
                        <span class="message">
                            <?= __('This project is not linked to a repository. New commits will not be processed.'); ?>
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
        const commits_url = $('#commit-list-with-filters-container').data('url');

        const showBranchCommits = (branch, from_commit, offset) => {
            let data = { branch };
            if (offset) {
                data.offset = offset;
            }
            if (from_commit) {
                data.from_commit = from_commit;
            }

            const $project_commits = $('#project_commits');
            const $paginator = $project_commits.find('.paginator');

            if ($paginator.length) {
                $paginator.addClass('submitting');
                $paginator.find('button').attr('disabled', true);
            }

            Pachno.fetch(commits_url, {
                method: 'POST',
                data
            }).then((json) => {
                if ($paginator.length) {
                    $paginator.remove();
                }

                if (data.offset && $project_commits.data('loaded') && $project_commits.data('branch') === branch) {
                    $project_commits.append(json.content);
                } else {
                    $project_commits.html(json.content);
                    $project_commits.data('loaded', true);
                    $project_commits.data('branch', branch);
                }
                $('nav.sidebar').addClass('collapsed');
            });
        };

        $body.on('change', '.trigger-mark-seen', function () {
            const $file = $(this).parents('.file-preview');
            $file.toggleClass('seen');
        });

        $body.on('click', '.trigger-show-branch', function () {
            const branch = $(this).data('branch');
            const from_commit = $(this).data('from-commit');
            const offset = $(this).data('offset');
            showBranchCommits(branch, from_commit, offset);
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

        const $checked = $('#commit-list-with-filters-container').find('input[type=radio][name=branch]:checked');
        if ($checked.length) {
            showBranchCommits($checked.val());
        }
    });

</script>
