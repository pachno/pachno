<?php

    use pachno\core\entities\Issue;
    use pachno\core\framework\Settings;

    /**
     * @var string $set_field_route
     * @var Issue $issue
     */

?>
<div class="backdrop_box huge" id="issue-card-popup">
    <div class="backdrop_detail_header">
        <span class="title-crumbs">
            <?php include_component('project/issueparent_crumbs', array('issue' => $issue)); ?>
        </span>
        <div class="dropper-container">
            <button class="dropper button secondary" id="more_actions_<?= $issue->getID(); ?>_button"><span><?= __('Actions'); ?></span><?= fa_image_tag('chevron-down'); ?></button>
            <?php include_component('main/issuemoreactions', array('issue' => $issue, 'times' => false, 'show_workflow_transitions' => false)); ?>
        </div>
        <?php include_component('project/issuefavorite', array('issue' => $issue)); ?>
        <a class="closer" href="javascript:void(0);" onclick="Pachno.UI.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <div class="form">
                <div class="row">
                    <div class="column large">
                        <form class="form-row" id="issue-card-title-form" action="<?= $set_field_route . '?field=title'; ?>" method="post" data-interactive-form>
                            <input name="value" class="name-input-enhance invisible" id="edit-issue-name" type="text" value="<?= $issue->getTitle(); ?>" placeholder="<?= __('Enter a short description of this issue here'); ?>">
                        </form>
                        <form class="row" id="issue-card-description-form" action="<?= $set_field_route . '?field=description'; ?>" method="post" data-simple-submit data-field="description">
                            <div class="form-row header">
                                <h5><?= fa_image_tag('align-left', ['class' => 'icon']); ?><span><?= __('Description'); ?></span></h5>
                            </div>
                            <div class="form-row">
                                <div class="formatted-text-container content">
                                    <?php echo $issue->getParsedDescription(['issue' => $issue]); ?>
                                </div>
                                <div class="editor-container">
                                    <?php include_component('main/textarea', ['area_name' => 'value', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'invisible' => true, 'markuppable' => true, 'syntax' => Settings::SYNTAX_MD, 'value' => $issue->getDescription()]); ?>
                                </div>
                            </div>
                            <div class="form-row submit-container">
                                <button type="button" class="button secondary"><?= __('Cancel'); ?></button>
                                <button type="submit" class="button primary">
                                    <span class="name"><?= __('Save'); ?></span>
                                    <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                                </button>
                            </div>
                        </form>
                        <form class="row <?php if (!$issue->isReproductionStepsVisible()) echo 'hidden'; ?>" id="issue-card-reproduction_steps-form" action="<?= $set_field_route . '?field=reproduction_steps'; ?>" method="post" data-simple-submit data-field="reproduction_steps">
                            <div class="form-row header">
                                <h5><?= fa_image_tag('list-ol', ['class' => 'icon']); ?><span><?= __('How to reproduce'); ?></span></h5>
                            </div>
                            <div class="form-row">
                                <div class="formatted-text-container content">
                                    <?php echo $issue->getParsedReproductionSteps(['issue' => $issue]); ?>
                                </div>
                                <div class="editor-container">
                                    <?php include_component('main/textarea', ['area_name' => 'value', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'invisible' => true, 'markuppable' => true, 'syntax' => Settings::SYNTAX_MD, 'value' => $issue->getReproductionSteps()]); ?>
                                </div>
                            </div>
                            <div class="form-row submit-container">
                                <button type="button" class="button secondary"><?= __('Cancel'); ?></button>
                                <button type="submit" class="button primary">
                                    <span class="name"><?= __('Save'); ?></span>
                                    <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="column">
                        <form class="form-row align-right" id="issue-card-issuetype-form" action="<?= $set_field_route . '?field=issuetype'; ?>" method="post" data-interactive-form data-field="issuetype">
                            <?php include_component('project/issuefieldstatus', ['issue' => $issue]); ?>
                        </form>
                    </div>
                </div>
            </div>
            <?php include_component('project/issue', ['issue' => $issue]); ?>
        </div>
    </div>
</div>
