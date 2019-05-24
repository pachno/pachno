<div class="paginator">
    <div class="page-buttons">
        <?php foreach ($pagination->getPageURLs() as $page_url): ?>
            <?php $page_url_classes = ($page_url['url'] === null ? 'button secondary highlight' : 'button secondary'); ?>
            <?php if ($page_url['url'] === null): ?>
                <a class="<?= $page_url_classes ?>"><?= $page_url['text']?></a>
            <?php else: ?>
                <?= link_tag($page_url['url'], $page_url['text'], ['class' => $page_url_classes, 'title' => $page_url['hint']]); ?>
            <?php endif ?>
        <?php endforeach ?>
    </div>
</div>
