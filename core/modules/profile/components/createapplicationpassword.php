<div class="backdrop_box medium">
    <div class="backdrop_detail_header">
        <span><?= __('Add application-specific password'); ?></span>
        <a href="javascript:void(0);" class="closer"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content edit_milestone">
        <div class="form-container" id="add_application_password_container">
            <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('profile_add_application_password'); ?>" method="post" id="add_application_password_form" data-simple-submit data-update-container="#add_application_password_form">
                <div class="form-row">
                    <div class="helper-text">
                        <div class="image-container"><?= image_tag('/unthemed/onboarding_application_password_icon.png', [], true); ?></div>
                        <span class="description"><?= __('Please enter the name of the application or computer which will be using this password. Examples include "Toms computer", "Work laptop", "My iPhone" and similar.'); ?></span>
                    </div>
                </div>
                <div class="form-row">
                    <label for="add_application_password_name"><?= __('Application name'); ?></label>
                    <input type="text" name="name" id="add_application_password_name" value="" class="name-input-enhance">
                </div>
                <div class="form-row submit-container">
                    <button type="submit" class="primary">
                        <span class="name"><?= __('Add application password'); ?></span>
                        <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>