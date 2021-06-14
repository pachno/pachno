<?php

    use pachno\core\entities\Issue;

    /**
     * @var Issue $issue
     */

?>
<div class="backdrop_box medium" id="viewissue_move_issue_div">
    <div class="backdrop_detail_header">
        <span><?= __('Move issue to a different project'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="Pachno.UI.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <form action="<?= make_url('move_issue', array('issue_id' => $issue->getID())); ?>" id="move_issue_form" method="post" data-simple-submit <?php if (isset($multi) && $multi): ?>data-update-container="#viewissue_move_issue_div"<?php endif; ?>>
                <input type="hidden" name="multi" value="<?= (int) (isset($multi) && $multi); ?>">
                <div class="form-row">
                    <div class="message-box type-warning">
                        <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                        <span class="message"><?= __('Please be aware that moving this issue to a different project will reset details such as status, category, etc., and may also make some fields invisible, depending on the issue type configuration for that project. The issue will also be renumbered.'); ?></span>
                    </div>
                </div>
                <div class="form-row">
                    <div class="fancy-dropdown-container">
                        <div class="fancy-dropdown" data-default-label="<?= __('Please select a project'); ?>">
                            <label><?php echo __('Move issue to'); ?></label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode">
                                <?php foreach (\pachno\core\entities\Project::getAll() as $project): ?>
                                    <?php if (!$project->hasAccess() || $project->isDeleted() || $project->isArchived() || !$pachno_user->canReportIssues($project) || $project->getID() == $issue->getProject()->getID()) continue; ?>
                                    <input class="fancy-checkbox" id="move_project_<?= $project->getID(); ?>" name="project_id" type="radio" value="<?= $project->getID(); ?>" <?php if ($project->getID() == $issue->getProject()->getID()) echo 'checked'; ?>>
                                    <label class="list-item" for="move_project_<?= $project->getID(); ?>">
                                        <span class="name value"><?= $project->getName(); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row submit-container">
                    <button type="submit" class="button primary">
                        <?= fa_image_tag('spinner', ['class' => 'indicator fa-spin icon']); ?>
                        <span><?= __('Move issue'); ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
