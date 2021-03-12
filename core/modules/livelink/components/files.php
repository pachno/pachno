<?php if (isset($structure['filepaths'][$basepath])): ?>
    <?php foreach ($structure['filepaths'][$basepath] as $file): ?>
        <a href="#file_<?= $file->getID(); ?>" class="list-item action_<?= $file->getAction(); ?>">
            <?php if ($file->getAction() == \pachno\core\entities\CommitFile::ACTION_DELETED): ?>
                <?= fa_image_tag('minus-square', ['class' => 'icon action-icon']) . fa_image_tag($file->getFontAwesomeIcon(), ['class' => 'icon'], $file->getFontAwesomeIconStyle()); ?>
            <?php elseif ($file->getAction() == \pachno\core\entities\CommitFile::ACTION_RENAMED): ?>
                <?= fa_image_tag('edit-square', ['class' => 'icon action-icon']) . fa_image_tag($file->getFontAwesomeIcon(), ['class' => 'icon'], $file->getFontAwesomeIconStyle()); ?>
            <?php elseif ($file->getAction() == \pachno\core\entities\CommitFile::ACTION_ADDED): ?>
                <?= fa_image_tag('plus-square', ['class' => 'icon action-icon']) . fa_image_tag($file->getFontAwesomeIcon(), ['class' => 'icon'], $file->getFontAwesomeIconStyle()); ?>
            <?php else: ?>
                <?= fa_image_tag('dot-circle', ['class' => 'icon action-icon'], 'far') . fa_image_tag($file->getFontAwesomeIcon(), ['class' => 'icon'], $file->getFontAwesomeIconStyle()); ?>
            <?php endif; ?>
            <span class="name"><?= $file->getFilename(); ?></span>
        </a>
    <?php endforeach; ?>
<?php endif; ?>
