<?php foreach ($structure['dirs'] as $foldername => $directory): ?>
    <?php include_component('livelink/directory', ['basepath' => $foldername, 'foldername' => $foldername, 'directory' => $directory, 'structure' => $structure]); ?>
<?php endforeach; ?>
<script>
    require(['domReady', 'jquery'], function (domReady, jquery) {
        domReady(function () {
            $('body').on('click', '.foldername', function (e) {
                $(this).toggleClass('collapsed');
            });
        });
    });
</script>
