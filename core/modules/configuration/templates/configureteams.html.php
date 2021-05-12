<?php

    use pachno\core\framework\Response;
    use pachno\core\entities\Team;

    /**
     * @var Response $pachno_response
     * @var Team[] $teams
     */

    $pachno_response->setTitle(__('Configure teams'));

?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_TEAMS]); ?>
    <div class="configuration-container">
        <div class="configuration-content">
            <h1>
                <span><?php echo __('Configure teams'); ?></span>
            </h1>
            <div class="helper-text">
                <div class="image-container"><?= image_tag('/unthemed/onboarding_configure_teams_icon.png', [], true); ?></div>
                <span class="description">
                    <?php echo __("Teams let you grant access to groups of users, as well as provide central starting points for information. Read about teams and permissions in the %online_documentation to learn more about how to create, apply and manage teams.", array('%online_documentation' => link_tag('https://projects.pachno.com/pachno/docs/UserTeams', '<b>'.__('online documentation').'</b>'))); ?>
                </span>
            </div>
            <h3><span><?php echo __('User teams'); ?></span></h3>
            <div class="flexible-table">
                <div class="row header">
                    <div class="column header info-icons"></div>
                    <div class="column header name-container"><?= __('Team name'); ?></div>
                    <div class="column header numeric"><?= __('User(s)'); ?></div>
                    <div class="column header actions"></div>
                </div>
                <div class="body" id="teams-list-container">
                    <?php foreach ($teams as $team): ?>
                        <?php include_component('configuration/team', ['team' => $team]); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    Pachno.on(Pachno.EVENTS.formSubmitResponse, function (PachnoApplication, data) {
        const json = data.json;
        switch (data.form) {
            case 'edit_team_form':
                let $existing_row = $(`[data-team][data-team-id=${json.team.id}]`);
                if ($existing_row.length) {
                    $existing_row.replaceWith(json.component);
                } else {
                    $('#teams-list-container').append(json.component);
                }
                break;
        }
    });

    Pachno.on(Pachno.EVENTS.team.delete, function (PachnoApplication, data) {
        $(`[data-team][data-team-id="${data.team_id}"]`).remove();
        Pachno.UI.Dialog.setSubmitting();

        Pachno.fetch(data.url, { method: 'DELETE' })
            .then(json => {
                Pachno.UI.Dialog.dismiss();
            });
    });
</script>