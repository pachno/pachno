<?php

/**
 * @var \pachno\core\entities\Project $project
 * @var \pachno\core\entities\Group $user_group
 */

?>
<div class="form-container">
    <div class="form-row">
        <h3>
            <span><?= __('Help / FAQ'); ?></span>
            <a class="button secondary" href="https://pachno.com/help?from=project_help" target="_blank">
                <?= fa_image_tag('globe', ['class' => 'icon']); ?>
                <span class="name"><?= __('Visit online help'); ?></span>
            </a>
        </h3>
        <div class="helper-text">
            <div class="image-container"><?= image_tag('/unthemed/help_faq.png', [], true); ?></div>
            <span class="description">
                <?= __("Here are answers to some common questions about projects and settings. If you can't find what you're looking for here, visit the online documentation for more help."); ?>
            </span>
        </div>
    </div>
</div>
<div class="list-mode">
    <div class="list-item multiline faq expandable">
        <span class="icon"><?= fa_image_tag('question-circle'); ?></span>
        <span class="name">
            <span class="title"><?= __('How can I give other people access to the project?'); ?></span>
        </span>
        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
    </div>
    <div class="submenu">
        <div class="list-item multiline faq">
            <span class="name">
                <span class="description">
                    <?= __('To give other people access to the project, go to the %people_and_access tab in project settings. From that page you can give specific people and teams access to the project, or grant general access to different project areas to regular users.', ['%people_and_access' => '<span class="button secondary">' . fa_image_tag('users') . '<span>' . __('People and access') . '</span></span>']); ?>
                </span>
            </span>
        </div>
    </div>
</div>
