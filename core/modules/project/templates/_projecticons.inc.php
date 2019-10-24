<div class="backdrop_box large">
    <div class="backdrop_detail_header">
        <span><?php echo __('Choose project icon'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="Pachno.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_projects_icons', array('project_id' => $project->getID())); ?>" method="post" id="build_form" onsubmit="$('update_icons_indicator').show();return true;" enctype="multipart/form-data">
        <div id="backdrop_detail_content" class="backdrop_detail_content">
            <div class="form-container">
                <div class="form-row">
                    <div class="image-grid" id="project-icons-grid">
                        <?php foreach (\pachno\core\entities\Project::getIcons() as $index => $icon): ?>
                            <input type="radio" name="project_icon" value="<?= $icon; ?>" id="project_icon_<?= $index; ?>" <?php if ($icon == $project->getLargeIconName()) echo ' checked'; ?>>
                            <label for="project_icon_<?= $index; ?>"><?= image_tag($icon, [], true); ?></label>
                        <?php endforeach; ?>
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
