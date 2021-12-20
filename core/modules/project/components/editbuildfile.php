<?php

    use pachno\core\entities\File;
    use pachno\core\entities\Build;

    /**
     * @var File $file
     * @var Build $build
     */

    $build_id = ($build instanceof Build) ? $build->getID() : 0;

?>
<div class="row" data-attachment data-file-id="<?= $file->getID(); ?>">
    <?php if (!$build instanceof Build): ?>
        <input type="hidden" name="files[<?= $file->getID(); ?>]" value="<?= $file->getID(); ?>">
    <?php endif; ?>
    <div class="column info-icons">
        <?= fa_image_tag($file->getIcon(), ['class' => 'icon'], 'far'); ?>
    </div>
    <div class="column name-container">
        <span><?= $file->getName(); ?></span>
    </div>
    <div class="column numeric"><?= $file->getReadableFilesize(); ?></div>
    <div class="column actions">
        <?php echo javascript_link_tag(fa_image_tag('times', ['class' => 'icon']), ['onclick' => "Pachno.UI.Dialog.show('" . __('Do you really want to remove this file?') . "', '" . __('If you remove this file it will no longer be available to download, even if you choose not to save the other changes. Are you sure you want to remove this file? This action cannot be undone.') . "', {yes: {click: function() { Pachno.trigger(Pachno.EVENTS.build.removeFile, { url: '" . make_url('build_detach_file', ['build_id' => $build_id, 'file_id' => $file->getID()]) . "', file_id: " . $file->getID() . ", build_id: " . $build_id . "}); }}, no: { click: Pachno.UI.Dialog.dismiss }});", 'class' => 'button secondary icon remove-button']); ?>
    </div>
</div>