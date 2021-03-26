<div class="backdrop_box large">
    <div id="change_workflow_box">
        <div class="backdrop_detail_header">
            <span><?= __('Add or invite team members'); ?></span>
            <a href="javascript:void(0);" class="closer" onclick="Pachno.UI.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
        </div>
        <div id="backdrop_detail_content" class="backdrop_detail_content">
            <div class="form-container">
                <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('find_invite_users'); ?>" method="post" data-simple-submit data-update-container="#find_dev_results"  id="invite_form">
                    <div class="form-row search-container">
                        <input type="search" name="find_by" id="find_by" value="" placeholder="<?= __('Enter email address to invite users'); ?>">
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
