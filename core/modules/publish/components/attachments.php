<div class="attachments-container" id="article_<?php echo $article->getID(); ?>_files">
    <?php foreach ($attachments as $file_id => $file): ?>
        <?php include_component('main/attachedfile', array('mode' => 'article', 'article' => $article, 'file' => $file)); ?>
    <?php endforeach; ?>
    <div class="file-upload-placeholder"></div>
</div>
