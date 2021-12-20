<?php foreach ($structure['dirs'] as $foldername => $directory): ?>
    <?php include_component('livelink/directory', ['basepath' => $foldername, 'foldername' => $foldername, 'directory' => $directory, 'structure' => $structure]); ?>
<?php endforeach; ?>
