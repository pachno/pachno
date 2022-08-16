<?php

    use pachno\core\entities\Build;
    use pachno\core\entities\User;
    use pachno\core\framework\Context;

    /**
     * @var Build $build
     * @var User $pachno_user
     */

?>
<div class="release-row row" data-release data-release-id="<?= $build->getID(); ?>">
    <div class="column info-icons"><?= fa_image_tag('boxes', ['class' => 'icon']); ?></div>
    <div class="column name-container">
        <span><?php echo $build->getName(); ?></span>
        <?php if ($build->isInternal()): ?>
            <span class="count-badge">
                <?= fa_image_tag('lock', ['class' => 'icon']); ?>
                <span><?= __('Internal release'); ?></span>
            </span>
        <?php endif; ?>
    </div>
    <div class="column"><?php echo $build->getVersion(); ?></div>
    <div class="column">
        <?= ($build->hasReleaseDate()) ? Context::getI18n()->formatTime($build->getReleaseDate(), 14, true, true) : '-'; ?>
    </div>
    <div class="column actions">
        <?php if ($build->hasDownload()): ?>
            <div class="dropper-container" id="build-<?= $build->getID(); ?>-download-container">
                <button class="dropper button secondary icon">
                    <?= fa_image_tag('download', ['class' => 'icon']); ?>
                </button>
                <div class="dropdown-container">
                    <div class="list-mode">
                        <?php foreach ($build->getFiles() as $file): ?>
                            <a href="<?= make_url('downloadfile', ['id' => $file->getID()]); ?>" class="list-item" data-attachment data-file-id="<?= $file->getID(); ?>">
                                <?= fa_image_tag($file->getIcon(), ['class' => 'icon'], 'far'); ?>
                                <span class="name"><?= $file->getName(); ?></span>
                                <span class="count-badge"><?= $file->getReadableFilesize(); ?></span>
                            </a>
                        <?php endforeach; ?>
                        <?php if ($build->getNumberOfDownloads() > 1 && $build->hasFileURL()): ?>
                            <div class="list-item separator"></div>
                        <?php endif; ?>
                        <?php if ($build->hasFileURL()): ?>
                            <a href="<?= $build->getFileURL(); ?>" class="list-item">
                                <?= fa_image_tag('download', ['class' => 'icon']); ?>
                                <span class="name"><?= __('Download from %hostname', ['%hostname' => $build->getFileDownloadHost()]); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php /*
        <a href="<?= make_url('project_issues', ['project_key' => Context::getCurrentProject()->getKey(), 'search' => true, 'fs[state]' => ['o' => '=', 'v' => \pachno\core\entities\Issue::STATE_OPEN], 'fs[build]' => ['o' => '=', 'v' => $build->getID()]]); ?>?sortfields=issues.posted=desc" class="button secondary icon"><?= fa_image_tag('search', ['class' => 'icon']); ?></a>
        */ ?>
        <?php if ($pachno_user->canManageProjectReleases($build->getProject())): ?>
            <div class="dropper-container">
                <button class="dropper button secondary icon">
                    <?= fa_image_tag('ellipsis-v', ['class' => 'icon']); ?>
                </button>
                <div class="dropdown-container">
                    <div class="list-mode">
                        <a href="javascript:void(0);" class="list-item trigger-backdrop" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'project_build', 'project_id' => $build->getProject()->getId(), 'build_id' => $build->getId()]); ?>">
                            <?= fa_image_tag('edit', ['class' => 'icon']); ?>
                            <span class="name"><?php echo __('Edit'); ?></span>
                        </a>
                        <div class="list-item separator"></div>
                        <a href="javascript:void(0);" class="list-item danger" onclick="Pachno.UI.Dialog.show('<?php echo __('Delete this release?'); ?>', '<?php echo __('Do you really want to delete this release?').'<br>'.__('Deleting this release will make it unavailable for download, and remove it from any associated issue reports or feature requests.').'<br><b>'.__('This action cannot be reverted').'</b>'; ?>', {yes: {click: function() { Pachno.trigger(Pachno.EVENTS.build.delete, { url: '<?php echo make_url('configure_build_delete', array('build_id' => $build->getID(), 'project_id' => $build->getProject()->getId())); ?>', build_id: <?php print $build->getID(); ?>});}}, no: {click: Pachno.UI.Dialog.dismiss}});">
                            <?= fa_image_tag('times', ['class' => 'icon']); ?>
                            <span class="name"><?php echo __('Delete'); ?></span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
