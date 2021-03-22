<div class="backdrop_box large">
    <div id="change_workflow_box">
        <div class="backdrop_detail_header">
            <span><?= ($invite) ? __('Add or invite team members') : __('Add people'); ?></span>
            <a href="javascript:void(0);" class="closer" onclick="Pachno.UI.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
        </div>
        <div id="backdrop_detail_content" class="backdrop_detail_content">
            <div class="form-container">
                <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_project_find_assignee', array('project_id' => $project->getID())); ?>" method="post" data-simple-submit data-update-container="#find_dev_results" id="find_dev_form">
                    <div class="form-row search-container">
                        <input type="search" name="find_by" id="find_by" value="" placeholder="<?= ($invite) ? __('Enter user details or email address to find or invite users') : __('Enter something to search for teams or users'); ?>">
                        <button type="submit" class="button primary">
                            <?= fa_image_tag('search', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Find'); ?></span>
                            <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']); ?>
                        </button>
                    </div>
                </form>
            </div>
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
