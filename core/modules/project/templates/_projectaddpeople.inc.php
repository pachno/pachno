<div class="backdrop_box large">
    <div id="change_workflow_box">
        <div class="backdrop_detail_header">
            <span><?php echo __('Add people'); ?></span>
            <a href="javascript:void(0);" class="closer" onclick="Pachno.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
        </div>
        <div id="backdrop_detail_content" class="backdrop_detail_content">
            <div class="form-container">
                <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_project_find_assignee', array('project_id' => $project->getID())); ?>" method="post" onsubmit="Pachno.Project.findDevelopers('<?= make_url('configure_project_find_assignee', array('project_id' => $project->getID())); ?>');return false;" id="find_dev_form">
                    <div class="form-row search-container">
                        <label for="find_by"><?= __('Find team or user'); ?></label>
                        <input type="search" name="find_by" id="find_by" value="" placeholder="<?= __('Enter something to search for teams or users'); ?>">
                        <button type="submit" class="button primary">
                            <?= fa_image_tag('search', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Find'); ?></span>
                        </button>
                    </div>
                </form>
            </div>
            <div style="padding: 10px 0 10px 0; display: none;" id="find_dev_indicator"><span style="float: left;"><?= image_tag('spinning_16.gif'); ?></span>&nbsp;<?= __('Please wait'); ?></div>
            <div id="find_dev_results">
                <div class="onboarding medium">
                    <div class="image-container">
                        <?= image_tag('/unthemed/onboarding_invite.png', [], true); ?>
                    </div>
                    <div class="helper-text">
                        <?= __('Invite team members, colleagues or collaborators'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
