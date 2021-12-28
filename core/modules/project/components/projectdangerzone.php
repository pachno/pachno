<?php

/**
 * @var \pachno\core\entities\Project $project
 * @var \pachno\core\entities\Group $user_group
 */

?>
<div class="form-container">
    <div class="form-row">
        <h3>
            <span><?= __('Danger zone'); ?></span>
        </h3>
        <div class="helper-text">
            <div class="image-container"><?= image_tag('/unthemed/onboarding_project_danger_zone.png', [], true); ?></div>
            <span class="description">
                <?= __("Sometimes things does not go to plan, or maybe the plan executed flawlessly. Either way it's time to let go."); ?>
            </span>
        </div>
    </div>
    <div class="list-mode">
        <div class="list-item multiline danger">
            <span class="icon"><?= fa_image_tag('trash-alt'); ?></span>
            <span class="name">
                <span class="title"><?= __('Delete this project'); ?></span>
                <span class="description"><?= __('When deleted, noone can see the project anymore. Every issue, document and other content is made unavailable.'); ?></span>
            </span>
            <span class="button-group">
                <button class="primary primary danger trigger-delete-project-popup">
                    <span class="name"><?= __('Delete project'); ?></span>
                    <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                </button>
            </span>
        </div>
    </div>
</div>
<script>
    Pachno.on(Pachno.EVENTS.ready, () => {
        const $body = $('body');
        const deleteProject = function () {
            const url = '<?= make_url('configure_project_delete', ['project_id' => $project->getID()]); ?>';
            $('button.trigger-delete-project-popup').attr('disabled', true);
            Pachno.UI.Dialog.setSubmitting();
            Pachno.fetch(url, {
                method: 'POST'
            })
            .then((json) => {
                Pachno.UI.Dialog.dismiss();
                Pachno.UI.Dialog.showModal('<?= __('The project has been deleted'); ?>', '<?= __('The project was deleted successfully.'); ?>', { url: '<?= make_url('home'); ?>' });
            })
            .catch((error) => {
                $('button.trigger-delete-project-popup').removeAttr('disabled');
            });
        };

        const popupDeleteProject = function () {
            Pachno.UI.Dialog.show(
                '<?= __('Really delete project?'); ?>',
                '<?= __('Deleting this project will prevent users from accessing it or any associated data, such as issues.'); ?>',
                {
                    yes: {click: function() { deleteProject(); }},
                    no: { click: Pachno.UI.Dialog.dismiss }
                }
            );
        }

        $body.on('click', '.trigger-delete-project-popup', function (event) {
            event.preventDefault();

            popupDeleteProject();
        });
    });
</script>