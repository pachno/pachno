<ul class="attached_items" id="article_<?php echo $article->getID(); ?>_files">
    <?php foreach ($attachments as $file_id => $file): ?>
        <?php include_component('main/attachedfile', array('base_id' => 'article_'.$article->getId().'_files', 'mode' => 'article', 'article' => $article, 'file' => $file)); ?>
    <?php endforeach; ?>
</ul>
