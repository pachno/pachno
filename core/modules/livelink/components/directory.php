<?php if ($foldername !== '.'): ?>
    <div class="list-item expandable expanded">
        <?= fa_image_tag('caret-right', ['class' => 'icon expander']); ?>
        <span class="icon"><?= fa_image_tag('folder'); ?></span>
        <span class="name"><?= $foldername; ?></span>
    </div>
    <div class="submenu">
        <?php foreach ($directory as $foldername => $directory): ?>
            <?php include_component('livelink/directory', ['basepath' => $basepath . '/' . $foldername, 'foldername' => $foldername, 'directory' => $directory, 'structure' => $structure]); ?>
        <?php endforeach; ?>
        <?php include_component('livelink/files', ['basepath' => $basepath, 'structure' => $structure]); ?>
    </div>
<?php else: ?>
    <?php include_component('livelink/files', ['basepath' => $basepath, 'structure' => $structure]); ?>
<?php endif; ?>
