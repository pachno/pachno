<?php

/**
 * @var \pachno\core\entities\File[] $custom_icons
 * @var \pachno\core\framework\Routing $pachno_routing
 */

?>
<div class="backdrop_box large">
    <div class="backdrop_detail_header">
        <span><?php echo __('Choose project icon'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="Pachno.UI.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_projects_icons', array('project_id' => $project->getID())); ?>" method="post" id="project_config_icon_form" enctype="multipart/form-data" data-simple-submit data-reset-backdrop>
        <div id="backdrop_detail_content" class="backdrop_detail_content">
            <div class="form-container">
                <div class="form-row">
                    <div class="image-grid" id="project-icons-grid">
                        <?php foreach (\pachno\core\entities\Project::getIcons() as $index => $icon): ?>
                            <input type="radio" name="project_icon" value="<?= $icon; ?>" id="project_icon_<?= $index; ?>" <?php if ($icon == $project->getIconName()) echo ' checked'; ?>>
                            <label for="project_icon_<?= $index; ?>"><?= image_tag($icon, [], true); ?></label>
                        <?php endforeach; ?>
                        <?php foreach ($custom_icons as $icon): ?>
                            <input type="radio" name="file_id" value="<?= $icon->getID(); ?>" id="project_icon_file_<?= $icon->getID(); ?>" <?php if ($project->getIcon() instanceof \pachno\core\entities\File && $project->getIcon()->getID() == $icon->getID()) echo ' checked'; ?>>
                            <label for="project_icon_file_<?= $icon->getID(); ?>"><?= image_tag($icon->getURL(), [], true); ?></label>
                        <?php endforeach; ?>
                        <label class="trigger-file-upload button secondary"><?= fa_image_tag('upload', ['class' => 'icon']); ?><span class="name"><?= __('Add icon'); ?></span></label>
                    </div>
                </div>
                <div class="form-row submit-container">
                    <button type="submit" class="button primary">
                        <span><?php echo __('Save'); ?></span>
                        <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    setTimeout(function () {
        const uploader = new Uploader({
            uploader_container: $('#project-icons-grid'),
            mode: 'grid',
            input_name: 'file_id',
            only_images: true,
            type: '<?= \pachno\core\entities\File::TYPE_PROJECT_ICON; ?>',
            data: {
                project_id: <?= $project->getID(); ?>
            }
        });
    }, 500);

    $('body').off('click', '#project-icons-grid input');
    $('body').on('click', '#project-icons-grid input', function () {
        if ($(this).attr('name') == 'project_icon') {
            $('input[name=file_id]').prop('checked', false);
        } else {
            $('input[name=project_icon]').prop('checked', false);
        }
    });
</script>
