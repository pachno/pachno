<?php

    /**
     * @var \pachno\core\entities\Build $build
     * @var \pachno\core\entities\Project $project
     */

    use pachno\core\entities\Edition;
    use pachno\core\entities\Milestone;

    $savebuttonlabel = ($build->getID()) ? __('Update details') : __('Save and publish');

?>
<div class="backdrop_box large">
    <div class="backdrop_detail_header">
        <span><?php echo ($build->getId()) ? __('Edit release details') : __('Add new release'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="Pachno.UI.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div class="form-container">
        <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_projects_build', ['project_id' => $project->getID()]); ?>" method="post" id="build_<?= $build->getId(); ?>_form" enctype="multipart/form-data">
            <div id="backdrop_detail_content" class="backdrop_detail_content with-sidebar">
                <div class="sidebar">
                    <div class="list-mode tab-switcher" id="build_form_tabs">
                        <a href="javascript:void(0);" data-tab-target="release-details" class="tab-switcher-trigger list-item selected">
                            <?= fa_image_tag('pen-fancy', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Release details'); ?></span>
                        </a>
                        <a href="javascript:void(0);" data-tab-target="download-details" class="tab-switcher-trigger list-item">
                            <?= fa_image_tag('file-download', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Downloads and files'); ?></span>
                        </a>
                    </div>
                </div>
                <div class="content" id="build_form_tabs_panes">
                    <div data-tab-id="release-details">
                        <?php if ($build->getID()): ?>
                            <input type="hidden" name="build_id" value="<?php echo $build->getID(); ?>">
                        <?php endif; ?>
                        <input type="hidden" name="edition" value="<?= $build->getEditionID(); ?>">
                        <div class="form-row">
                            <label for="build_name"><?php echo __('Release name'); ?></label>
                            <input type="text" name="name" id="build_name" class="name-input-enhance" value="<?= $build->getName(); ?>">
                        </div>
                        <div class="form-row unified tooltip-container">
                            <div class="tooltip">
                                <span><?= __('Pachno can track major, minor and revision releases using these input fields. You decide which ones you want to use'); ?></span>
                            </div>
                            <label for="ver_mj"><?php echo __('Version name or number'); ?><?= fa_image_tag('question-circle', ['class' => 'icon']); ?></label>
                            <input type="text" name="ver_mj" id="ver_mj" class="version" value="<?php echo $build->getVersionMajor(); ?>">&nbsp;.&nbsp;<input type="text" name="ver_mn" id="ver_mn" class="version" value="<?php echo $build->getVersionMinor(); ?>">&nbsp;.&nbsp;<input type="text" name="ver_rev" id="ver_rev" class="version" value="<?php echo $build->getVersionRevision(); ?>">
                        </div>
                        <div class="row aligned">
                            <div class="column small">
                                <div class="form-row">
                                    <div class="fancy-label-select">
                                        <input class="fancy-checkbox" type="radio" name="released" id="is_released_yes" value="1"<?php if ($build->isReleased()) echo ' checked'; ?>><label for="is_released_yes"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Released'); ?></label>
                                        <input class="fancy-checkbox" type="radio" name="released" id="is_released_no" value="0"<?php if (!$build->isReleased()) echo ' checked'; ?>><label for="is_released_no"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Not released'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="column small">
                                <div class="form-row">
                                    <div class="fancy-label-select">
                                        <input class="fancy-checkbox" type="radio" name="active" id="is_active_yes" value="1"<?php if ($build->isActive()) echo ' checked'; ?>><label for="is_active_yes"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Active'); ?></label>
                                        <input class="fancy-checkbox" type="radio" name="active" id="is_active_no" value="0"<?php if (!$build->isActive()) echo ' checked'; ?>><label for="is_active_no"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Archived'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="column small">
                                <div class="form-row">
                                    <div class="fancy-label-select">
                                        <input class="fancy-checkbox" type="radio" name="locked" id="locked_no" value="0"<?php if (!$build->isInternal()) echo ' checked'; ?>><label for="locked_no"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Public release'); ?></label>
                                        <input class="fancy-checkbox" type="radio" name="locked" id="locked_yes" value="1"<?php if ($build->isInternal()) echo ' checked'; ?>><label for="locked_yes"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Internal relase'); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <label for="release_date"><?php echo __('Release date'); ?></label>
                            <input type="hidden" id="edit_build_date_container" class="auto-calendar">
                        </div>
                        <div class="form-row">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown">
                                    <label><?= __('Milestone release'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode from-bottom">
                                        <input type="radio" name="milestone_id" class="fancy-checkbox" value="0"<?php if (!$build->getMilestone() instanceof Milestone) echo ' checked'; ?> id="build_milestone_release_0">
                                        <label for="build_milestone_release_0" class="list-item">
                                            <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                            <span class="name value"><?php echo __('This release is not related to a milestone'); ?></span>
                                        </label>
                                        <?php foreach ($project->getAvailableMilestones() as $milestone): ?>
                                            <input type="radio" name="milestone_id" class="fancy-checkbox" value="<?= $milestone->getID(); ?>" <?php if ($build->getMilestone() instanceof Milestone && $build->getMilestone()->getID() == $milestone->getID()) echo ' checked'; ?> id="build_milestone_release_<?= $milestone->getID(); ?>">
                                            <label for="build_milestone_release_<?= $milestone->getID(); ?>" class="list-item">
                                                <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                                <span class="name value"><?= __('This is a release of %milestone_name', ['%milestone_name' => $milestone->getName()]); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($build->getProject()->isEditionsEnabled() || $build->getEdition() instanceof \pachno\core\entities\Edition): ?>
                            <div class="form-row">
                                <div class="fancy-dropdown-container">
                                    <div class="fancy-dropdown">
                                        <label><?= __('Edition release'); ?></label>
                                        <span class="value"></span>
                                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                        <div class="dropdown-container list-mode from-bottom">
                                            <input type="radio" name="edition_id" class="fancy-checkbox" value="0"<?php if (!$build->getEdition() instanceof Edition) echo ' checked'; ?> id="build_edition_release_0">
                                            <label for="build_edition_release_0" class="list-item">
                                                <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                                <span class="name value"><?php echo __('This release is not related to a edition'); ?></span>
                                            </label>
                                            <?php foreach ($project->getEditions() as $edition): ?>
                                                <input type="radio" name="edition_id" class="fancy-checkbox" value="<?= $edition->getID(); ?>" <?php if ($build->getEdition() instanceof Edition && $build->getEdition()->getID() == $edition->getID()) echo ' checked'; ?> id="build_edition_release_<?= $edition->getID(); ?>">
                                                <label for="build_edition_release_<?= $edition->getID(); ?>" class="list-item">
                                                    <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                                    <span class="name value"><?= __('This is a release of %edition_name', ['%edition_name' => $edition->getName()]); ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="form-row submit-container">
                            <button class="button primary" type="submit">
                                <?= fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?>
                                <span><?= $savebuttonlabel; ?></span>
                            </button>
                        </div>
                    </div>
                    <div data-tab-id="download-details" style="display: none;">
                        <div class="form-row">
                            <div class="helper-text">
                                <div class="image-container"><?= image_tag('/unthemed/icon_build_files.png', [], true); ?></div>
                                <span class="description">
                                    <?= __('You can upload files, specify a download link - or both - so users and developers can download this release'); ?>
                                </span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="flexible-table" id="release-list-files">
                                <div class="row header">
                                    <div class="column header info-icons"></div>
                                    <div class="column header name-container"><?= __('Filename'); ?></div>
                                    <div class="column header numeric"><?= __('Size'); ?></div>
                                    <div class="column header actions"></div>
                                </div>
                                <div class="body file-list-container">
                                    <?php foreach ($build->getFiles() as $file): ?>
                                        <?php include_component('project/editbuildfile', ['file' => $file, 'build' => $build]); ?>
                                    <?php endforeach; ?>
                                </div>
                                <label class="trigger-file-upload file-upload-placeholder button secondary"><?= fa_image_tag('upload', ['class' => 'icon']); ?><span class="name"><?= __('Add file'); ?></span></label>
                            </div>
                        </div>
                        <div class="form-row">
                            <label for="build_download_url"><?php echo __('Alternate download URL'); ?></label>
                            <input type="text" name="file_url" id="build_download_url" value="<?= $build->getFileURL(); ?>" placeholder="<?= __('Enter an alternate download link here, if desired'); ?>">
                        </div>
                        <div class="form-row submit-container">
                            <button class="button primary" type="submit">
                                <?= fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?>
                                <span><?= $savebuttonlabel; ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    setTimeout(function () {
        public const uploader = new Uploader({
            uploader_container: '#release-list-files',
            mode: 'table',
            only_images: false,
            type: '<?= \pachno\core\entities\File::TYPE_DOWNLOAD; ?>',
            data: {
                build_id: <?= $build->getID(); ?>
            }
        });
    }, 500);

    Pachno.on(Pachno.EVENTS.build.removeFile, function (PachnoApplication, data) {
        if (data.build_id != <?= $build->getID(); ?>)
            return;

        $(`[data-attachment][data-file-id="${data.file_id}"]`).remove();
        Pachno.UI.Dialog.setSubmitting();

        Pachno.fetch(data.url, { method: 'DELETE' })
            .then(json => {
                Pachno.UI.Dialog.dismiss();
                if (parseInt(json.attachments) === 0) {
                    $('#build-' + data.build_id + '-download-container').remove();
                }
            })
    });

    $form = $('#build_<?= $build->getId(); ?>_form');
    $form.off('submit');
    $form.on('submit', function (event) {
        $form.addClass('submitting');
        event.preventDefault();
        event.stopPropagation();
        let datepickerInstance = Pachno.UI.calendars['edit_build_date_container'];
        let date = datepickerInstance.selectedDates.map(date => date.getTime() / 1000);

        let options = {
            method: 'POST',
            form: $form.attr('id'),
            data: {
                date
            }
        };
        Pachno.fetch($form.attr('action'), options)
            .then(json => {
                $form.removeClass('submitting');
                Pachno.UI.Backdrop.reset();
                Pachno.trigger(Pachno.EVENTS.formSubmitResponse, { form: $form.attr('id'), json });
                public const $release_row = $('[data-release][data-release-id=' + json.build.id + ']');
                let $container;
                if (json.build.archived == 1) {
                    $container = $('#archived_releases_list');
                } else if (json.build.released == 1) {
                    $container = $('#active_releases_list');
                } else {
                    $container = $('#upcoming_releases_list');
                }

                if (json.build.edition && json.build.edition.id) {
                    $container = $container.attr('id') + '_' + json.build.edition.id;
                }

                if ($release_row.length) {
                    if ($release_row.parents('.body').attr('id') !== $container.attr('id')) {
                        $container.append(json.component);
                        $release_row.remove();
                    } else {
                        $release_row.replaceWith(json.component);
                    }
                } else {
                    $container.append(json.component);
                }

                for (public const mode of ['archived', 'active', 'upcoming']) {
                    $('.release-count[data-list=' + mode + ']').html($('#tab_project_releases_' + mode + '_pane .release-row').length);
                }
            });
    })
</script>
